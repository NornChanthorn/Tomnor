<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Constants\UserRole;
use App\Constants\FormType;
use App\Constants\Message;
use App\Constants\LoanStatus;

use App\Models\Address;
use App\Models\Client;
use App\Models\Loan;

use App\Http\Requests\ClientRequest;

use App\Traits\FileHandling;
use Auth;

class ClientController extends Controller
{
    use FileHandling;

    /** @var Address object */
    protected $address;

    /** @var string Folder name to store image */
    private $imageFolder = 'client';

    /** @var string Folder name to store general documents */
    private $fileFolder = 'documents';

    public function __construct(Address $address)
    {
      // $this->middleware('role:'. UserRole::ADMIN)->only('edit');
      $this->address = $address;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if(!Auth::user()->can('customer.browse')) {
        return back()->with([Message::ERROR_KEY => trans('message.no_permission'), 'alert-type' => 'warning'], 403);
      }

      $clients = Client::query();
      if (!empty($request->search)) {
        $clients = $clients->where(function ($query) use ($request) {
          $searchText = $request->search;
          $query->where('name', 'like', '%' . $searchText . '%')
          ->orWhere('id_card_number', 'like', '%' . $searchText . '%')
          ->orWhere('first_phone', 'like', '%' . $searchText . '%')
          ->orWhere('second_phone', 'like', '%' . $searchText . '%')
          ->orWhere('sponsor_name', 'like', '%' . $searchText . '%')
          ->orWhere('sponsor_phone', 'like', '%' . $searchText . '%');
        });
      }

      $itemCount = $clients->count();
      $clients = $clients->sortable()->orderBy('created_at')->paginate(paginationCount());
      $offset = offset($request->page);

      return view('client.index', compact('clients', 'itemCount', 'offset'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Client $client
     *
     * @return Response
     */


    public function create(Client $client)
    {
      if(!Auth::user()->can('customer.add')) {
        return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
      }

      $title = trans('app.create');
      $formType = FormType::CREATE_TYPE;
      $provinces = $this->address->getAllProvinces();
      $districts = $communes = $villages = $sponsorDistricts = $sponsorCommunes = $sponsorVillages = [];

      return view('client/form', compact('client', 'communes', 'districts',
          'formType',
          'provinces',
          'sponsorCommunes',
          'sponsorDistricts',
          'sponsorVillages',
          'title',
          'villages'
      ));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Client $client
     *
     * @return Response
     */
    public function edit(Client $client)
    {
      if(!Auth::user()->can('customer.edit')) {
        return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
      }

      if ($client->is_default == 1) {
        return back();
      }

      $title = trans('app.edit');
      $formType = FormType::EDIT_TYPE;
      $provinces = $this->address->getAllProvinces();
      $districts = $this->address->getSubAddresses($client->province_id);
      $communes = $this->address->getSubAddresses($client->district_id);
      $villages = $this->address->getSubAddresses($client->commune_id);

      $sponsorDistricts = $this->address->getSubAddresses($client->sponsor_province_id);
      $sponsorCommunes = $this->address->getSubAddresses($client->sponsor_district_id);
      $sponsorVillages = $this->address->getSubAddresses($client->sponsor_commune_id);

      return view('client/form', compact('client', 'communes', 'districts', 'formType', 'provinces', 'sponsorCommunes', 'sponsorDistricts', 'sponsorVillages', 'title', 'villages'));
    }

    /**
     * Display the specified resource.
     *
     * @param Client $client
     *
     * @return Response
     */
    public function show(Client $client)
    {
      if(!Auth::user()->can('customer.browse')) {
        return back()->with([Message::ERROR_KEY => trans('message.no_permission'), 'alert-type' => 'warning'], 403);
      }

      $title = trans('app.detail');
      $formType = FormType::SHOW_TYPE;

      return view('client/form', compact('client', 'formType', 'title'));
    }

    /**
     * Save new or existing client.
     *
     * @param ClientRequest $request
     * @param Client $client
     *
     * @return Response
     */
    public function save(ClientRequest $request, Client $client)
    {
      if(!Auth::user()->can('customer.add') && !Auth::user()->can('customer.edit')) {
        return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
      }

      // Personal info
      $client->name           = $request->name;
      $client->gender         = $request->gender;
      $client->date_of_birth  = dateIsoFormat($request->date_of_birth);
      $client->id_card_number = str_replace(' ', '', $request->id_card_number);
      $client->first_phone    = $request->first_phone;
      $client->second_phone   = $request->second_phone;
      $client->facebook_name   = $request->facebook_name;
      $client->facebook_url   = $request->facebook_url;
      $client->telegram       = $request->telegram;
      $client->province_id    = $request->province;
      $client->district_id    = $request->district;
      $client->commune_id     = $request->commune;
      $client->village_id     = $request->village;
      $client->address        = $request->address;

      // Sponsor info
      $client->sponsor_name         = $request->sponsor_name;
      $client->sponsor_gender       = $request->sponsor_gender;
      $client->sponsor_dob          = dateIsoFormat($request->sponsor_date_of_birth);
      $client->sponsor_id_card      = $request->sponsor_id_card_number;
      $client->sponsor_phone        = $request->sponsor_first_phone;
      $client->sponsor_phone_2      = $request->sponsor_second_phone;
      $client->sponsor_province_id  = $request->sponsor_province;
      $client->sponsor_district_id  = $request->sponsor_district;
      $client->sponsor_commune_id   = $request->sponsor_commune;
      $client->sponsor_village_id   = $request->sponsor_village;
      $client->occupation_1         = $request->occupation_1;
      $client->occupation_2         = $request->occupation_2;

      if (!empty($request->profile_photo)) {
        $client->profile_photo = $this->uploadImage($this->imageFolder, $request->profile_photo);
      }

      if (!empty($request->id_card_photo)) {
        $client->id_card_photo = $this->uploadImage($this->imageFolder, $request->id_card_photo);
      }

      if (!empty($request->sponsor_profile_photo)) {
        $client->sponsor_profile_photo = $this->uploadImage($this->imageFolder, $request->sponsor_profile_photo);
      }

      if (!empty($request->sponsor_id_card_photo)) {
        $client->sponsor_id_card_photo = $this->uploadImage($this->imageFolder, $request->sponsor_id_card_photo);
      }
      if (!empty($request->related_document_1)) {
        $client->related_document_1 = $this->uploadImage($this->imageFolder, $request->related_document_1);
      }

      if (!empty($request->related_document_2)) {
        $client->related_document_2 = $this->uploadImage($this->imageFolder, $request->related_document_2);
      }
      if ($client->save()) {
        session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
      }
      else {
        // session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
         // If the client save operation fails, display an error message
        session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));

        // Optionally, you can also flash the validation errors
        session()->flash('errors', $client->errors()); // Use the appropriate method for your model to retrieve errors

        // Redirect back to the form with an error message
        return redirect()->back();
      }

      return redirect(route('client.index'));
    }

  /**
   * Remove the specified resource from storage.
   *
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    if(!Auth::user()->can('customer.delete')) {
      return back()->with([Message::ERROR_KEY => trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }
    $client = Client::find($id)->delete();
  }

  public function getClients(Request $request)
  {
    // dd($request->all());
    $search = $request->get('search');

    $clients = Client::where('is_active', 1);
    // $clients = Client::where('is_active', 1);

    if(!empty($search)) {
      $clients->where(function ($query) use ($search) {
        $query->where('name', 'like', '%' . $search .'%');
      });
    }

    $result = $clients->orderBy('is_default', 'desc')->limit(20)->get()->map(function($item) {
      return [
        'id' => $item->id,
        'text' => $item->name,
      ];
    });
    return response()->json($result);
  }
}
