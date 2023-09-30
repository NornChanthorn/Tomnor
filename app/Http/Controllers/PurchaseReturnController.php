<?php

namespace App\Http\Controllers;

use App\Constants\Message;
use App\Constants\FormType;
use App\Constants\ContactType;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Address;
use App\Models\Purchase;
use App\Models\ReferenceCount;

use App\Models\Transaction;

use Illuminate\Http\Request;

use App\Traits\FileHandling;
use App\Traits\ProductUtil;
use App\Traits\TransactionUtil;
use Auth;
use DB;
use \Carbon\Carbon;
class PurchaseReturnController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {
        if(!Auth::user()->can('po.browse')) {
        return back()->with([
            Message::ERROR_KEY => trans('message.no_permission'),
            'alert-type' => 'warning'
        ], 403);
        }

        $purchases = Transaction::where('type', 'purchase_return');
       
        $itemCount = $purchases->count();
        $purchases = $purchases->sortable()->latest()->paginate(paginationCount());
        $offset = offset($request->page);

        $locations = Branch::get();
        $suppliers = Contact::whereIn('type', [ContactType::SUPPLIER, ContactType::BOTH])->orderBy('is_default', 'desc')->orderBy('name')->get();

        return view('purchase-return.index', compact('itemCount', 'offset', 'purchases', 'locations', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        $title = trans('app.create');
        $formType = FormType::EDIT_TYPE;
        $purchase = Transaction::where('type', 'purchase')->findOrFail($id);
        $ref_count = ReferenceCount::where('ref_type','purchase_return')->first()->ref_count;
        $invoice_no= $this->generateReferenceNumber('purchase_return', $ref_count + 1, 'REF');
        return view('purchase-return.form', compact(
            'formType',
            'purchase',
            'title',
            'invoice_no'
        ));
    }
/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $formType = FormType::EDIT_TYPE;
        $warehouses = Branch::getAll();
        $products = Product::getAll();
        $suppliers = Contact::whereIn('type', [ContactType::SUPPLIER, ContactType::BOTH])->get();
        $purchaseStatuses = purchaseStatuses();
        $purchase = Transaction::where('type', 'purchase')->findOrFail($id);
        $purchasedProducts = old('products') ?? []; // When form validation has error
        $ref_count = $this->setAndGetReferenceCount($transaction->type);
        $ref_digits =  str_pad($ref_count, 4, 0, STR_PAD_LEFT);

        $contact = new Contact;
        $contact->type = ContactType::SUPPLIER;
        $provinces = $this->address->getAllProvinces();
         $groups = ContactGroup::get();
        return view('purchase-return.form', compact(
          'formType',
          'products',
          'suppliers',
          'purchase',
          'purchasedProducts',
          'purchaseStatuses',
          'warehouses',
          'contact',
          'provinces',
        'groups'
        ));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        try {
            $id=$request->input('transaction_id');
            $purchase = Transaction::where('type', 'purchase')->findOrFail($id);
            $return_quantities = $request->input('returns');
            $return_total = 0;
            DB::beginTransaction();
            foreach ($purchase->purchase_lines as $purchase_line) {
                $old_return_qty = $purchase_line->quantity_returned;

                $return_quantity = $return_quantities[$purchase_line->id];
                $purchase_line->quantity_returned = $return_quantity;
                $purchase_line->save();
                $return_total += $purchase_line->purchase_price * $purchase_line->quantity_returned;

                //Decrease quantity in variation location details
                if ($old_return_qty != $purchase_line->quantity_returned) {

                    $this->decreaseProductQuantity(
                        $purchase_line->product_id,
                        $purchase_line->variantion_id,
                        $purchase->location_id,
                        $old_return_qty
                    );
                }
                
            }
            $return_total_inc_tax = $return_total;
            $return_transaction_data = [
                'total_before_tax' => $return_total,
                'final_total' => $return_total_inc_tax,
            ];
            if (empty($request->input('ref_no'))) {
                //Update reference count
                $ref_count = $this->setAndGetReferenceCount('purchase-return');
                $return_transaction_data['ref_no'] = $invoice_no= $this->generateReferenceNumber('purchase-return', $ref_count + 1, 'REF');
            }
            $return_transaction = Transaction::where('type', 'purchase_return')
            ->where('return_parent_id', $purchase->id)
            ->first();
            if (!empty($return_transaction)) {
                $return_transaction->update($return_transaction_data);
            } else {
                $return_transaction_data['location_id'] = $purchase->location_id;
                $return_transaction_data['type'] = 'purchase_return';
                $return_transaction_data['status'] = 'final';
                $return_transaction_data['contact_id'] = $purchase->contact_id;
                $return_transaction_data['transaction_date'] = Carbon::now();
                $return_transaction_data['created_by'] = Auth::id();
                $return_transaction_data['return_parent_id'] = $purchase->id;

                $return_transaction = Transaction::create($return_transaction_data);
            }
            $this->updatePaymentStatus($return_transaction->id, $return_transaction->final_total);
            DB::commit();
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
            return redirect(route('purchase-return.index'));
        } catch (\Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
            return redirect()->back();
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
