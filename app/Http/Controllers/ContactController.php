<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\Request;

use App\Constants\FormType;
use App\Constants\Message;
use App\Constants\ContactType;

use App\Models\Address;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\Staff;
use App\Http\Requests\ContactRequest;

use App\Traits\FileHandling;
use App\Traits\ProductUtil;
use App\Traits\TransactionUtil;

use Auth;
use \Carbon\Carbon;

class ContactController extends Controller
{
  use FileHandling, ProductUtil, TransactionUtil;

  /** @var Address object */
  protected $address;

  /** @var string Folder name to store image */
  private $imageFolder = 'contacts';

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
    $type = $request->get('type') ?? ContactType::CUSTOMER;
    if($type==ContactType::SUPPLIER){
      if(!Auth::user()->can('supplier.browse')) {
        return back()->with([
          Message::ERROR_KEY => trans('message.no_permission'),
          'alert-type' => 'warning'
        ], 403);
      }
    }else{
      if(!Auth::user()->can('contact.browse')) {
        return back()->with([
          Message::ERROR_KEY => trans('message.no_permission'),
          'alert-type' => 'warning'
        ], 403);
      }
    }
    

    
    if($type == ContactType::SUPPLIER) {
      $contacts = Contact::whereIn('type', [ContactType::SUPPLIER, ContactType::BOTH]);
    }
    else {
      $contacts = Contact::whereIn('type', [ContactType::CUSTOMER, ContactType::BOTH]);
    }

    if (!empty($request->search)) {
      $contacts = $contacts->where(function ($query) use ($request) {
        $searchText = $request->search;
        $query->where('name', 'like', '%' . $searchText . '%')
        ->orWhere('supplier_business_name', 'like', '%' . $searchText . '%')
        ->orWhere('mobile', 'like', '%' . $searchText . '%')
        ->orWhere('contact_id', 'like', '%' . $searchText . '%')
        ->orWhere('alternate_number', 'like', '%' . $searchText . '%');
      });
    }

    $itemCount = $contacts->count();
    $contacts = $contacts->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);

    $view = ($type==ContactType::SUPPLIER) ? 'contact.supplier_index' : 'contact.customer_index';
    return view($view, compact('contacts', 'itemCount', 'offset', 'type'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @param Client $client
   *
   * @return Response
   */
  public function create(Contact $contact, Request $request)
  {
    if(!Auth::user()->can('supplier.add')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    $ref_count = \App\Models\ReferenceCount::where('ref_type', 'contacts')->first()->ref_count;

    $title = trans('app.create');
    $formType = FormType::CREATE_TYPE;
    $provinces = $this->address->getAllProvinces();
    $contact->type = $request->get('type') ?? ContactType::SUPPLIER;
    $contact->contact_id = $this->generateReferenceNumber('contacts', $ref_count, 'CO');
    $groups = ContactGroup::where('type',$contact->type)->get();

    return view('contact.form', compact(
      'contact',
      'formType',
      'provinces',
      'title',
      'groups'
    ));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param Client $client
   *
   * @return Response
   */
  public function edit(Contact $contact, Request $request)
  {
    if(!Auth::user()->can('supplier.edit')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    if ($contact->is_default == 1) {
      return back();
    }

    $title = trans('app.edit');
    $formType = FormType::EDIT_TYPE;
    $provinces = $this->address->getAllProvinces();
    $districts = $communes = $villages = [];
    $groups = ContactGroup::where('type',$contact->type)->get();
    return view('contact.form', compact(
      'contact',
      'communes',
      'districts',
      'formType',
      'provinces',
      'title',
      'villages',
      'groups'
    ));
  }

  /**
   * Display the specified resource.
   *
   * @param Client $client
   *
   * @return Response
   */
  public function show(Contact $contact, Request $request)
  {
    if(!Auth::user()->can('supplier.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $title = contacttypes($contact->type).' '.trans('app.detail');
    $formType = FormType::SHOW_TYPE;
    $contactType = $request->get('type') ?? ContactType::SUPPLIER;
    $ob_transaction =  Transaction::where('contact_id', $contact->id)
    ->where('type', 'opening_balance')
    ->first();
    
    $opening_balance = !empty($ob_transaction->final_total) ? $ob_transaction->final_total : 0;
    $opening_balance_due = !empty($ob_transaction->invoices) ? $ob_transaction->invoices->sum('total') : 0;
    $payments = Invoice::leftJoin('transactions as t', 'invoices.transaction_id', '=', 't.id')
                ->leftjoin('invoices as parent_payment', 'invoices.parent_id', '=', 'parent_payment.id')
                ->with('child_payments', 'child_payments.transaction')
                ->where('invoices.client_id', $contact->id)
                ->whereIn('t.status', ['received','final'])
                ->whereIn('invoices.type',['sell','purchase','sell_return','purchase_return','opening_balance','advance'])
                ->select(
                  'invoices.id',
                  'invoices.total',
                  'invoices.type as invoice_type',
                  'invoices.is_return',
                  'invoices.payment_method',
                  'invoices.payment_date',
                  'invoices.invoice_number',
                  'invoices.reference_number',
                  'invoices.parent_id',
                  't.invoice_no',
                  't.ref_no',
                  't.type as transaction_type',
                  't.id as  transaction_id',
                  'parent_payment.invoice_number as parent_invoice_no',
                  'parent_payment.reference_number as parent_reference_no'
                )->groupBy('invoices.id')
                ->orderByDesc('invoices.payment_date')
                ->get();
    $transactions = Transaction::where('contact_id',$contact->id)->whereIn('type',['sell','purchase','sell_return','purchase_return','opening_balance'])->whereIn('status', ['received','final'])->orderByDesc('transaction_date')->get();
    return view('contact.show', compact(
      'contact',
      'formType',
      'title',
      'contactType',
      'opening_balance',
      'opening_balance_due',
      'transactions',
      'payments'
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
  public function save(ContactRequest $request, Contact $contact)
  {
    if(!Auth::user()->can('supplier.add') && !Auth::user()->can('supplier.edit')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    $contact->type                    = $request->type;
    $contact->name                    = $request->name;
    if(empty($contact->contact_id)){
      $contact->contact_id              = $request->contact_id;
    }
    $contact->contact_group_id        = $request->contact_group_id;
    $contact->supplier_business_name  = $request->company;
    $contact->mobile                  = $request->phone;
    $contact->alternate_number        = $request->second_phone;
    $contact->custom_field1           = $request->custom_field1;
    $contact->city                    = $request->province;
    $contact->landmark                = $request->address;
    $contact->custom_field2           = str_replace(' ', '', $request->id_card_number);
    if(isAdmin()){
      $contact->created_by              = $request->created_by;
      $location_id = Staff::where('user_id',$request->created_by)->first()->branch_id;
    }else{
      $contact->created_by              = auth()->id();
      $location_id = Staff::where('user_id',auth()->id())->first()->branch_id;
    }
    

    //Update reference count
    $ref_count = $this->setAndGetReferenceCount($contact->type);
    if(empty($contact->id)) {
      $contact->contact_id = $this->generateReferenceNumber('contacts', $ref_count, 'CO');
    }

    if ($contact->save()) {
      //Add opening balance
      if ($request->opening_balance > 0) {
        $this->createOpeningBalanceTransaction($location_id, $contact->id, $request->opening_balance, $contact->created_by);
      }

      $output = [
        'success' => true,
        'data' => $contact,
        'message' => trans('message.item_saved_success'),
      ];
    }
    else {
      $output = [
        'success' => false,
        'message' => trans('message.item_saved_fail')
      ];
    }

    // return redirect(route('contact.index', ['type'=>$request->type]));
    return response()->json($output);
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
      $supplier = Contact::findOrFail($id);
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

    $contact = Contact::where('type', ContactType::SUPPLIER)->where('is_active', 1);

    if(!empty($search)) {
      $contact->where(function ($query) use ($search) {
        $query->where('name', 'like', '%' . $search .'%')
        ->orWhere('supplier_business_name', 'like', '%' . $search . '%')
        ->orWhere('mobile', 'like', '%' . $search . '%')
        ->orWhere('contact_id', 'like', '%' . $search . '%')
        ->orWhere('alternate_number', 'like', '%' . $search . '%');
      });
    }

    $result = $contact->orderBy('is_default', 'desc')->limit(20)->get()->map(function($item) {
      return [
        'id' => $item->id,
        'text' => $item->name,
      ];
    });
    return response()->json($result);
  }

  public function getClients(Request $request)
  {
    // dd($request->all());
    $search = $request->get('search');

    $clients = Contact::where('type', ContactType::CUSTOMER);

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

  public function checkContact(Request $request)
  {
    $type = $request->type;
    $contact_id = $request->contact_id;
    $id = $request->hidden_id;

    $valid = 'true';
    if(!empty($contact_id)) {
      $query = Contact::where('contact_id', $contact_id);

      if(!empty($id)) {
        $query->where('id', '!=', $id);
      }
      if(!empty($type)) {
        $query->where('type', $type);
      }
      if ($query->count() > 0) {
        $valid = 'false';
      }
    }

    echo $valid;
    exit;
  }
}
