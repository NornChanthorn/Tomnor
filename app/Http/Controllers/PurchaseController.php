<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use App\Constants\BranchType;
use App\Constants\Message;
use App\Constants\PurchaseStatus;
use App\Constants\StockTransaction;
use App\Constants\StockType;
use App\Constants\FormType;
use App\Constants\ContactType;
use App\Models\ContactGroup;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\Purchase;
use App\Models\PurchaseLine;
use App\Models\PurchaseDetail;
use App\Models\StockHistory;
use App\Models\Transaction;
use App\Models\Contact;
use App\Models\Address;
use App\Http\Requests\PurchaseRequest;

use Auth;
use DB;
use \Carbon\Carbon;

use App\Traits\FileHandling;
use App\Traits\ProductUtil;
use App\Traits\TransactionUtil;

class PurchaseController extends Controller
{
  use FileHandling, ProductUtil, TransactionUtil;

  /** @var Address object */
  protected $address;

  /** @var string Folder name to store purchase document */
  private $documentFolder = 'purchase';

  public function __construct(Address $address)
  {
    $this->address = $address;
  }

  /**
   * Display a listing of product purchases.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function index(Request $request)
  {
    if(!Auth::user()->can('po.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $purchases = Transaction::where('type', 'purchase');
    if(Auth::user()->staff) {
      $staff = Auth::user()->staff;
      $purchases = $purchases->where('location_id', $staff->branch_id);
    }
    else {
      if(!empty($request->location)) {
        $purchases = $purchases->where('location_id', $request->location);
      }
    }

    if(!empty($request->client)) {
      $purchases = $purchases->where('client_id', $request->client);
    }

    if(!empty($request->status)) {
      $purchases = $purchases->where('status', $request->status);
    }

    if(!empty($request->start) && !empty($request->end)){
      $purchases = $purchases->where('transaction_date', '>=', $request->start)->where('transaction_date', '<=', $request->end);
    }

    $itemCount = $purchases->count();
    $purchases = $purchases->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);

    $locations = Branch::get();
    $suppliers = Contact::whereIn('type', [ContactType::SUPPLIER, ContactType::BOTH])->orderBy('is_default', 'desc')->orderBy('name')->get();

    return view('purchase.index', compact('itemCount', 'offset', 'purchases', 'locations', 'suppliers'));
  }

  /**
   * Show form to create product purchase.
   *
   * @param Purchase $purchase
   *
   * @return Response
   */
  public function create(Purchase $purchase)
  {
    if(!Auth::user()->can('po.add')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $title = trans('app.create');
    $formType = FormType::CREATE_TYPE;
    $warehouses = Branch::getAll();
    $products = Product::getAll();
    $suppliers = Contact::whereIn('type', [ContactType::SUPPLIER, ContactType::BOTH])->get();
    $purchaseStatuses = purchaseStatuses();
    $purchasedProducts = old('products') ?? []; // When form validation has error

    $provinces = $this->address->getAllProvinces();
    $contact = new Contact;
    $contact->type = ContactType::SUPPLIER;
    $contact->contact_id = $this->generateReferenceNumber($contact->type, $this->setAndGetReferenceCount($contact->type), 'CO');
    $groups = ContactGroup::where('type','supplier')->get();
    return view('purchase.form', compact(
      'formType',
      'products',
      'suppliers',
      'purchase',
      'purchasedProducts',
      'purchaseStatuses',
      'title',
      'warehouses',
      'contact',
      'provinces',
	'groups'
    ));
  }

  /**
   * Show form to edit an existing purchase.
   *
   * @param Purchase $purchase
   *
   * @return Response
   */
  public function edit($id)
  {
    if(!Auth::user()->can('po.edit')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $title = trans('app.edit');
    $formType = FormType::EDIT_TYPE;
    $warehouses = Branch::getAll();
    $products = Product::getAll();
    $suppliers = Contact::whereIn('type', [ContactType::SUPPLIER, ContactType::BOTH])->get();
    $purchaseStatuses = purchaseStatuses();
    $purchase = Transaction::where('type', 'purchase')->where('id', $id)->first();
    $purchasedProducts = old('products') ?? []; // When form validation has error

    $contact = new Contact;
    $contact->type = ContactType::SUPPLIER;
    $provinces = $this->address->getAllProvinces();
    $groups = ContactGroup::where('type','supplier')->get();
    return view('purchase.form', compact(
      'formType',
      'products',
      'suppliers',
      'purchase',
      'purchasedProducts',
      'purchaseStatuses',
      'title',
      'warehouses',
      'contact',
      'provinces',
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
  public function show(Transaction $purchase)
  {
    if(!Auth::user()->can('po.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    return view('purchase.show', compact('purchase'));
  }

  /**
   * Save new purchase.
   *
   * @param PurchaseRequest $request
   *
   * @return Response
   */
  public function save(PurchaseRequest $request)
  {
    if(!Auth::user()->can('po.add') && !Auth::user()->can('po.edit')) {
      return back()->with(
        [
          Message::ERROR_KEY => trans('message.no_permission'), 
          'alert-type' => 'warning'
        ],
        403
      );
    }

    $formType = $request->form_type;

    $productIds = collect($request->products)->pluck('id')->unique()->toArray();
    $purchasedProductCount = Product::whereIn('id', $productIds)->count();

    // Check if IDs of purchased product (s) are invalid
    if (count($productIds)==0 || ($purchasedProductCount != count($productIds))) {
      return back()->withInput()->withErrors([
        Message::ERROR_KEY => trans('message.invalid_product_data'),
      ]);
    }

    // If exist or new
    if($formType == FormType::EDIT_TYPE){
      if(!empty($request->purchase_id)) {
        $transaction = Transaction::findOrFail($request->purchase_id);
        if(($transaction->ref_no != $request->invoice_id) && $request->invoice_id != null){
          $request->validate([
            'invoice_id' => ['nullable', Rule::unique('transactions', 'ref_no')],
          ]);
        }
      }
    }
    else {
      $transaction = new Transaction;
    }
    $supplier = Contact::find($request->supplier);
    $transaction->discount_amount  = $request->discount ?? 0;
    $transaction->shipping_charges = $request->shipping_cost ?? 0;
    $transaction->total_before_tax = collect($request->products)->map(function($item) {
        return $item['quantity'] * $item['purchase_price'];
      })->sum();
    $transaction->final_total      = ($transaction->total_before_tax - $transaction->discount_amount) + $transaction->shipping_charges;
    $transaction->discount_type    = 'percentage';
    $transaction->location_id      = $request->warehouse;
    $transaction->created_by       = auth()->user()->id;
    $transaction->type             = 'purchase';
    $transaction->status           = $request->status;
    $transaction->transaction_date = Carbon::parse($request->purchase_date)->toDateTimeString();
    $transaction->payment_status   = 'due';
    $transaction->exchange_rate    = '1';
    $transaction->ref_no           = $request->invoice_id;
    $transaction->additional_notes = $request->note;
    $transaction->contact_id       = $request->supplier;
    $transaction->contact_group_id = $supplier->contact_group_id;


    // upload document
    if (isset($request->document)) {
      $transaction->document = $this->uploadFile($this->documentFolder, $request->document);
    }

    DB::beginTransaction();

    //Update reference count
    $ref_count = $this->setAndGetReferenceCount($transaction->type);
    //Generate reference number
    if (empty($request->invoice_id)) {
      $transaction->ref_no = $this->generateReferenceNumber('opening_stock', $ref_count);
    }

    $transaction->save();

    try {
      //code...
      // create purchase line
      $this->createOrUpdatePurchaseLines($transaction, $request->products);
      $total_cost = floatval(isset($request->total_cost) ? $request->total_cost : 0);
      if($formType==FormType::CREATE_TYPE && $total_cost>0) {
        //Add Purchase payments
        $payments[] = [
          'amount' => $request->total_cost,
          'method' => $request->payment_method,
          'note' => $request->note,
          'paid_on' => Carbon::now()->toDateTimeString(),
        ];
        $this->createOrUpdatePaymentLines($transaction, $payments);
      }

      //update payment status
      $this->updatePaymentStatus($transaction->id, $transaction->final_total);
    } catch (\Exception $e) {
      //throw $th;
      return back()->withInput()->withErrors([
        Message::ERROR_KEY => $e->getMessage(),
      ]);
    }

    DB::commit();

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return redirect(route('purchase.index'));
  }

  /**
   * Purchase invoice.
   *
   * @param Purchase $purchases
   *
   * @return Response
   */
  public function invoice(Transaction $purchase)
  {
    if(!Auth::user()->can('po.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $purchase->final_total = $purchase->purchase_lines->map(function($item) {
      return $item->purchase_price * $item->quantity;
    })->sum();
    $purchase->depreciation_amount = $purchase->invoices->sum('payment_amount') ?? 0;
    $purchase->remaining_amount = ($purchase->final_total - $purchase->depreciation_amount) ?? 0;

    $invoice_head = $purchase->warehouse;

    return view('purchase.invoice', compact('purchase', 'invoice_head'));
  }

  /**
   * Delete Purchase.
   *
   * @param Product $purchase
   *
   * @return Response
   */
  public function destroy($id, Request $request)
  {
    // if(!Auth::user()->can('po.delete')) {
    //   abort(404);
    // }
    // if (!$request->ajax()) {
    //   abort(404);
    // }

    $purchase = Transaction::where('id', $id)->where('type', 'purchase')->with(['purchase_lines'])->first();
    $delete_purchase_lines = $purchase->purchase_lines;

    //Begin transaction
    DB::beginTransaction();

    if(!empty($purchase)) {
      if($purchase->status == 'odered') {
        $purchase->delete();
      }
      else {
        //Delete purchase lines first
        $delete_purchase_line_ids = [];
        foreach ($delete_purchase_lines as $purchase_line) {
          $delete_purchase_line_ids[] = $purchase_line->id;
          $this->decreaseProductQuantity($purchase_line->product_id, $purchase_line->variantion_id, $purchase->location_id, $purchase_line->quantity);
        }
        PurchaseLine::where('transaction_id', $purchase->id)->whereIn('id', $delete_purchase_line_ids)->delete();

        //Update mapping of purchase & Sell.
        // $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($transaction_status, $transaction, $delete_purchase_lines);
      }

      //Delete Transaction
      $purchase->delete();
      session()->flash(Message::SUCCESS_KEY, trans('message.item_deleted_success'));
    }
    else {
      session()->flash(Message::ERROR_KEY, trans('message.unable_perform_action'));
    }

    DB::commit();
  }
}
