<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use Auth;

use App\Http\Requests\SaleRequest;

use App\Constants\FormType;
use App\Constants\Message;
use App\Constants\SaleStatus;
use App\Constants\SaleType;
use App\Constants\StockTransaction;
use App\Constants\StockType;
use App\Constants\ContactType;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Contact;
use App\Models\ProductWarehouse;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockHistory;
use App\Models\Staff;
use App\Models\Transaction;
use App\Models\Address;
use App\Models\GeneralSetting;
use App\Models\ContactGroup;
use App\Models\TransactionSellLine;
use App\Traits\FileHandling;
use App\Traits\ProductUtil;
use App\Traits\TransactionUtil;

use \Carbon\Carbon;

class SaleController extends Controller
{
  use FileHandling, ProductUtil, TransactionUtil;

  /** @var Address object */
  protected $address;

  /** @var string Folder name to store sale document */
  private $documentFolder = 'sale';

  public function __construct(Address $address)
  {
    $this->address = $address;
  }

  /**
   * Display a listing of product sales.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function index(Request $request)
  {
    if(!Auth::user()->can('sale.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $sales = Transaction::where('type', 'sell');
    if(Auth::user()->staff) {
      $staff = Auth::user()->staff;
      $sales = $sales->where('location_id', $staff->branch_id);
    }
    else {
      if(!empty($request->location)) {
        $sales = $sales->where('location_id', $request->location);
      }
    }

    if(!empty($request->client)) {
      $sales = $sales->where('contact_id', $request->client);
    }

    if(!empty($request->status)) {
      $sales = $sales->where('status', $request->status);
    }

    if(!empty($request->sale_code)) {
        $sales = $sales->where('invoice_no', 'like', '%'.$request->sale_code.'%');
    }
    if(!empty($request->group)) {
      $sales = $sales->where('contact_group_id', 'like', '%'.$request->sale_code.'%');
    }

    if(!empty($request->payment_status)) {
        $sales = $sales->where('payment_status', $request->payment_status);
    }

    if(!empty($request->start_date) && !empty($request->end_date)) {
      $startDate = Carbon::parse($request->start_date)->toDateTimeString();
      $endDate = Carbon::parse($request->end_date)->toDateTimeString();
      $sales = $sales->where('transaction_date', '>=', $startDate)->where('transaction_date', '<=', $endDate);
    }

    $locations = Branch::get();
    $clients = Contact::whereIn('type', [ContactType::CUSTOMER, ContactType::BOTH])->orderBy('is_default', 'desc')->orderBy('name')->get();

    $itemCount = $sales->count();
    $sales = $sales->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);

    return view('sale.index', compact('itemCount', 'offset', 'sales', 'locations', 'clients'));
  }
  public function contactGroup(Request $request,$group_id=null)
  {
    if(!Auth::user()->can('sale.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $sales = Transaction::where('type', 'sell');
    if(Auth::user()->staff) {
      $staff = Auth::user()->staff;
      $sales = $sales->where('location_id', $staff->branch_id);
    }
    else {
      if(!empty($request->location)) {
        $sales = $sales->where('location_id', $request->location);
      }
    }

    if(!empty($request->client)) {
      $sales = $sales->where('contact_id', $request->client);
    }

    if(!empty($request->status)) {
      $sales = $sales->where('status', $request->status);
    }

    if(!empty($request->sale_code)) {
        $sales = $sales->where('invoice_no', 'like', '%'.$request->sale_code.'%');
    }
    if(!empty($group_id)) {
      $sales = $sales->where('contact_group_id', 'like', '%'.$group_id.'%');
    }

    if(!empty($request->payment_status)) {
        $sales = $sales->where('payment_status', $request->payment_status);
    }

    if(!empty($request->start_date) && !empty($request->end_date)) {
      $startDate = Carbon::parse($request->start_date)->toDateTimeString();
      $endDate = Carbon::parse($request->end_date)->toDateTimeString();
      $sales = $sales->where('transaction_date', '>=', $startDate)->where('transaction_date', '<=', $endDate);
    }

    $locations = Branch::get();
    $clients = Contact::whereIn('type', [ContactType::CUSTOMER, ContactType::BOTH])->orderBy('is_default', 'desc')->orderBy('name')->get();

    $itemCount = $sales->count();
    $sales = $sales->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);

    return view('sale.index', compact('itemCount', 'offset', 'sales', 'locations', 'clients','group_id'));
  }
  /**
   * Show form to create sale.
   *
   * @param Sale $sale
   *
   * @return Response
   */
  public function create(Transaction $sale)
  {
    if(!Auth::user()->can('sale.add')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $title = trans('app.create');
    $formType = FormType::CREATE_TYPE;
    $branches = Branch::getAll();
    $clients = Contact::whereIn('type', [ContactType::CUSTOMER, ContactType::BOTH])->orderBy('is_default', 'desc')->orderBy('name')->get();
    $setting = GeneralSetting::first();

    $agents = [];
    if(isAdmin() && old('branch') !== null) {
      // When form validation has error
      $agents = Staff::where('branch_id', old('branch'))->orderBy('name')->get();
    }
    $provinces = $this->address->getAllProvinces();
    $contact = new Contact;
    $contact->type = ContactType::CUSTOMER;
    $contact->contact_id = $this->generateReferenceNumber($contact->type, $this->setAndGetReferenceCount($contact->type), 'CO');
    $groups = ContactGroup::where('type','customer')->get();
    $details=[];
    return view('sale.form', compact(
      'agents',
      'branches',
      'clients',
      'formType',
      'sale',
      'title',
      'contact',
      'provinces',
      'setting',
      'groups',
      'details'
    ));
  }

  /**
   * Save new purchase.
   *
   * @param PurchaseRequest $request
   *
   * @return Response
   */
  public function save(SaleRequest $request, $saleType)
  {
    if(!Auth::user()->can('sale.add') && !Auth::user()->can('sale.edit')) {
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $productIds = collect($request->products)->pluck('id')->unique()->toArray();
    $productCount = Product::whereIn('id', $productIds)->count();
    // Check if IDs of sale product (s) are invalid
    if (count($productIds)==0 || ($productCount != count($productIds))) {
      session()->flash(Message::SAVE_FAILURE_VALUE, trans('message.invalid_product_data'));
      return back()->withInput()->withErrors([
        Message::ERROR_KEY => trans('message.invalid_product_data'),
      ]);
    }
    $new_sale = true;
    if(!empty($request->sale_id)) {
      $sale = $sale_before = Transaction::findOrFail($request->sale_id);
      $new_sale = false;
    }
    else {
      $sale = new Transaction;
    }

    if (isAdmin() || empty(auth()->user()->staff)) {
      $validationRules = [
        'branch' => 'required|integer',
        'agent' => 'required|integer',
      ];
      $this->validate($request, $validationRules);
    }

    try {
      if(!empty($request->sale_id)) {
        $sale = $sale_before = Transaction::findOrFail($request->sale_id);
        $new_sale = false;
      }
      else {
        $sale = new Transaction;
      }
      $contact = Contact::find($request->client);
      if (isAdmin() || empty(auth()->user()->staff)) {
        $sale->location_id = $request->branch;
        $sale->created_by = $request->agent;
      }
      else {
        $staff = auth()->user()->staff;
        if($staff) {
          $sale->location_id = $staff->branch_id;
        }
        $sale->created_by = $staff->user_id ?? auth()->user()->id;
      }

      $sale->transaction_date = Carbon::parse($request->sale_date)->toDateTimeString();
      $sale->is_quotation     = $request->status=='draft' ? 1 : 0;
      $sale->contact_id       = $contact->id;
      $sale->contact_group_id = $contact->contact_group_id;
      $sale->total_before_tax = collect($request->products)->map(function($item) {
        return $item['quantity'] * $item['price'];
      })->sum();
      $sale->final_total      = ($sale->total_before_tax - $request->discount) + $request->other_service;
      $sale->type             = 'sell';
      $sale->status           = $request->status;
      $sale->staff_note       = $request->note;
      $sale->discount_type    = 'fixed';
      $sale->discount_amount  = $request->discount ?? 0;
      $sale->others_charges   = $request->other_service ?? 0;
      $sale->shipping_charges = 0;
      $sale->payment_status   = 'due';

      if($new_sale) {
        //Update reference count
        $ref_count = $this->setAndGetReferenceCount($sale->type);
        //Generate reference number
        if (empty($request->invoice_id)) {
          $sale->invoice_no = $this->generateReferenceNumber('sell', $ref_count, 'INV', 6);
        }
      }

      DB::beginTransaction();
      if($sale->save()) {
        //Add sale items again
        $this->createOrUpdateSellLines($sale, $request->products, $request->branch);

        // add sale payment
        if ($request->paid_amount<=$sale->final_total) {
          $payment_id = null;
          if(!empty($sale->invoices)){
            $payment_id = @$sale->invoices->first()->id;
          }
          $payment[] = [
            'payment_id'  => $request->payment_id ?? '',
            'amount'      => $request->paid_amount,
            'method'      => $request->payment_method,
            'note'        => $request->note,
            'paid_on'     => Carbon::now()->toDateTimeString(),
          ];
          $this->createOrUpdatePaymentLines($sale, $payment);
        }

        //Update payment status
        $this->updatePaymentStatus($sale->id, $sale->final_total);

        //Check for final and do some processing.
        if ($request->status == SaleStatus::DONE) {
              //update product stock
              foreach ($request->products as $product) {
                if ($product['enable_stock']) {
                  $decrease_qty = $product['quantity'];
                  if (!empty($product['base_unit_multiplier'])) {
                    $decrease_qty = $decrease_qty * $product['base_unit_multiplier'];
                  }
                  if (empty($product['transaction_sell_lines_id'])) {
                    $this->decreaseProductQuantity(
                      $product['id'],
                      $product['variantion_id'],
                      $request->branch,
                      $decrease_qty
                    );
                  }else{
                    $sell_line_id=$product['transaction_sell_lines_id'];
                    $sell_line = TransactionSellLine::find($sell_line_id);
                    if($decrease_qty>$sell_line->quantity){
                      $this->decreaseProductQuantity(
                        $product['id'],
                        $product['variantion_id'],
                        $request->branch,
                        $decrease_qty,
                        $sell_line->quantity
                      );
                    }
                    if($decrease_qty<$sell_line->quantity){
                      $this->updateProductQuantity(
                        $request->branch,
                        $product['id'],
                        $product['variantion_id'],
                        $decrease_qty,
                        $sell_line->quantity
                      );
                    }
      
                    
                  }
                  
                }
              }

          //Auto send notification
          // $this->notificationUtil->autoSendNotification($business_id, 'new_sale', $transaction, $transaction->contact);
        }
      }
      DB::commit();
    }
    catch (\Exception $e) {
      DB::rollBack();

      \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
      session()->flash(Message::SAVE_FAILURE_VALUE, trans('message.item_saved_fail'));
      return redirect()->back()->withInput()->withErrors([
        Message::ERROR_KEY => trans('message.item_saved_fail'),
      ]);
    }

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return redirect(route('sale.index'));
  }

  /**
   * Show form to create sale.
   *
   * @param Sale $sale
   *
   * @return Response
   */
  public function edit($id)
  {
    if(!Auth::user()->can('sale.edit')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $sale = Transaction::findOrFail($id);
    $details = $sale->sell_lines;
    // $payment = $sale->payment_lines;
    $setting = GeneralSetting::first();

    $title = trans('app.edit');
    $formType = FormType::EDIT_TYPE;
    $clients = Contact::whereIn('type', [ContactType::CUSTOMER, ContactType::BOTH])->orderBy('is_default', 'desc')->orderBy('name')->get();
    $agents = [];

    if (isAdmin() || empty(auth()->user()->staff)) {
      $branches = Branch::getAll();
      $agents = Staff::where('branch_id', old('branch', $sale->location_id))->orderBy('name')->get();
    }

    $provinces = $this->address->getAllProvinces();
    $contact = new Contact;
    $contact->type = ContactType::CUSTOMER;
    $contact->contact_id = $this->generateReferenceNumber($contact->type, $this->setAndGetReferenceCount($contact->type), 'CO');
    $groups = ContactGroup::get();
    return view('sale.edit', compact(
      'agents',
      'branches',
      'clients',
      'formType',
      'sale',
      'title',
      'details',
      // 'payment',
      'contact',
      'provinces',
      'setting',
      'groups'
    ));
  }

  /**
   * Show purchase detail.
   *
   * @param Purchase $purchase
   *
   * @return Response
   */
  public function show(Transaction $sale)
  {
    if(!Auth::user()->can('sale.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    return view('sale.show', compact('sale'));
  }

  /**
   * Sale invoice.
   *
   * @param Sale $sales
   *
   * @return Response
   */
  public function invoice(Transaction $sale)
  {
    if(!Auth::user()->can('sale.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $sale->final_total = $sale->sell_lines->map(function($item) {
      return $item->unit_price * $item->quantity;
    })->sum();

    if($sale->discount_type == 'percentage') {
      $sale->discount_amount = ($sale->final_total * $sale->discount_amount) / 100;
    }

    $sale->depreciation_amount = $sale->invoices->sum('payment_amount') ?? 0;
    $sale->remaining_amount = ($sale->final_total - $sale->discount_amount - $sale->depreciation_amount + $sale->others_charges) ?? 0;

    $invoice_head = $sale->warehouse;

    return view('sale.invoice', compact('sale', 'invoice_head'));
  }

  /**
   * Delete product.
   *
   * @param Product $product
   *
   * @return Response
   */
  public function destroy($id, Request $request)
  {
    if(!Auth::user()->can('sale.delete')) {
      abort(404);
    }
    if (!$request->ajax()) {
      abort(404);
    }

    $sale = Transaction::where('id', $id)->where('type', 'sell')->with(['sell_lines'])->first();

    //Begin transaction
    DB::beginTransaction();

    if(!empty($sale)) {
      if($sale->status == 'draft') {
        $sale->delete();
      }
      else {
        $deleted_sell_lines = $sale->sell_lines;
        $deleted_sell_lines_ids = $deleted_sell_lines->pluck('id')->toArray();
        $this->deleteSellLines($deleted_sell_lines_ids, $sale->location_id);

        $sale->delete();
      }
    }

    // minus count by 1
    $ref = \App\Models\ReferenceCount::where('ref_type', 'sell')->first();
    $ref->decrement('ref_count', 1);
    $ref->save();

    DB::commit();
    session()->flash(Message::SUCCESS_KEY, trans('message.item_deleted_success'));
  }
}
