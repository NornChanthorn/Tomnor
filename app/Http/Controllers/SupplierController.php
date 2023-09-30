<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\Request;
use App\Constants\FormType;
use App\Constants\Message;
use App\Models\Address;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Http\Requests\SupplierRequest;
use App\Traits\FileHandling;

use Auth;

class SupplierController extends Controller
{
  use FileHandling;

  /** @var Address object */
  protected $address;
  
  /** @var string Folder name to store image */
  private $imageFolder = 'suppliers';
  
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
    if(!Auth::user()->can('supplier.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    $suppliers = Supplier::query();
    if (!empty($request->search)) {
      $suppliers = $suppliers->where(function ($query) use ($request) {
        $searchText = $request->search;
        $query->where('name', 'like', '%' . $searchText . '%')
          ->orWhere('id_card_number', 'like', '%' . $searchText . '%')
          ->orWhere('first_phone', 'like', '%' . $searchText . '%')
          ->orWhere('second_phone', 'like', '%' . $searchText . '%')
          ->orWhere('sponsor_name', 'like', '%' . $searchText . '%')
          ->orWhere('sponsor_phone', 'like', '%' . $searchText . '%');
      });
    }

    $itemCount = $suppliers->count();
    $suppliers = $suppliers->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);
    
    return view('supplier.index', compact('suppliers', 'itemCount', 'offset'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @param Client $client
   *
   * @return Response
   */
  public function create(Supplier $supplier)
  {
    if(!Auth::user()->can('supplier.add')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    $title = trans('app.create');
    $formType = FormType::CREATE_TYPE;
    $provinces = $this->address->getAllProvinces();
    $districts = $communes = $villages = $sponsorDistricts = $sponsorCommunes = $sponsorVillages = [];
    
    return view('supplier.form', compact(
      'supplier',
      'communes',
      'districts',
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
  public function edit(Supplier $supplier)
  {
    if(!Auth::user()->can('supplier.edit')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    if ($supplier->is_default == 1) {
      return back();
    }

    $title = trans('app.edit');
    $formType = FormType::EDIT_TYPE;
    $provinces = $this->address->getAllProvinces();
    $districts = $this->address->getSubAddresses($supplier->province_id);
    $communes = $this->address->getSubAddresses($supplier->district_id);
    $villages = $this->address->getSubAddresses($supplier->commune_id);

    $sponsorDistricts = $this->address->getSubAddresses($supplier->sponsor_province_id);
    $sponsorCommunes = $this->address->getSubAddresses($supplier->sponsor_district_id);
    $sponsorVillages = $this->address->getSubAddresses($supplier->sponsor_commune_id);

    return view('supplier.form', compact(
      'supplier',
      'communes',
      'districts',
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
   * Display the specified resource.
   *
   * @param Client $client
   *
   * @return Response
   */
  public function show(Supplier $supplier)
  {
    if(!Auth::user()->can('supplier.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    $title = trans('app.detail');
    $formType = FormType::SHOW_TYPE;

    return view('supplier.form', compact(
      'supplier',
      'formType',
      'title'
    ));
  }

  /**
   * Save new or existing client.
   *
   * @param ClientRequest $request
   * @param Client $client
   *
   * @return Response
   */
  public function save(SupplierRequest $request, Supplier $supplier)
  {
    if(!Auth::user()->can('supplier.add') && !Auth::user()->can('supplier.edit')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    $supplier->name           = $request->name;
    $supplier->company_name   = $request->company;
    $supplier->vat            = $request->vat;
    $supplier->id_card_number = str_replace(' ', '', $request->id_card_number);
    $supplier->first_phone    = $request->first_phone;
    $supplier->second_phone   = $request->second_phone;
    $supplier->province_id    = $request->province;
    $supplier->address        = $request->address;

    if ($supplier->save()) {
      session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    } 
    else {
      session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
    }

    return redirect(route('supplier.index'));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id, Request $request)
  {
    if(!Auth::user()->can('supplier.delete')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    $transactions = Transaction::where('contact_id', $id)->where('type', 'purchase')->count();
    if($transactions == 0) {
      $supplier = Supplier::findOrFail($id);
      if($supplier->is_default != 1) {
        $supplier->delete();
      }
      session()->flash(Message::SUCCESS_KEY, trans('message.item_deleted_success'));
    }
    else {
      session()->flash(Message::ERROR_KEY, trans('message.in_used_action'));
    }
  }

  public function getSuppliers(Request $request)
  {
    // dd($request->all());
    $search = $request->get('search');

    $suppliers = Supplier::where('is_active', 1);

    if(!empty($search)) {
      $suppliers->where(function ($query) use ($search) {
        $query->where('name', 'like', '%' . $search .'%');
      });
    }

    $result = $suppliers->orderBy('is_default', 'desc')->limit(20)->get()->map(function($item) {
      return [
        'id' => $item->id,
        'text' => $item->name,
      ];
    });
    return response()->json($result);
  }
}