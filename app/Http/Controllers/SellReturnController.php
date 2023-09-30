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
use App\Models\sell;
use App\Models\ReferenceCount;

use App\Models\Transaction;

use Illuminate\Http\Request;

use App\Traits\FileHandling;
use App\Traits\ProductUtil;
use App\Traits\TransactionUtil;
use Auth;
use DB;
use \Carbon\Carbon;


class SellReturnController extends Controller
{
    use FileHandling, ProductUtil, TransactionUtil;

    /** @var Address object */
    protected $address;
  
    /** @var string Folder name to store sell document */
    private $documentFolder = 'sell';
  
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
    
            $sells = Transaction::where('type', 'sell_return');
           
            $itemCount = $sells->count();
            $sells = $sells->sortable()->latest()->paginate(paginationCount());
            $offset = offset($request->page);
    
            $locations = Branch::get();
            $suppliers = Contact::whereIn('type', [ContactType::SUPPLIER, ContactType::BOTH])->orderBy('is_default', 'desc')->orderBy('name')->get();
    
            return view('sell-return.index', compact('itemCount', 'offset', 'sells', 'locations', 'suppliers'));
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
        $sell = Transaction::where('type', 'sell')->findOrFail($id);
        $ref_count = ReferenceCount::where('ref_type','sell_return')->first()->ref_count;
        $invoice_no= $this->generateReferenceNumber('sell_return', $ref_count + 1, 'REF');
        return view('sell-return.form', compact(
            'formType',
            'sell',
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
        //
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
            $sell = Transaction::where('type', 'sell')->findOrFail($id);
            $return_quantities = $request->input('returns');
            $return_total = 0;
            DB::beginTransaction();
            foreach ($sell->sell_lines as $sell_line) {
                $old_return_qty = $sell_line->quantity_returned;

                $return_quantity = $return_quantities[$sell_line->id];
                $sell_line->quantity_returned = $return_quantity;
                $sell_line->save();
                $return_total += $sell_line->unit_price * $sell_line->quantity_returned;
        
                //Decrease quantity in variation location details
                if ($old_return_qty != $sell_line->quantity_returned) {
                    $this->adjustQuantity(
                        $sell->location_id,
                        $sell_line->product_id,
                        $sell_line->variantion_id,
                        $return_quantity
                    );
                }
                
            }
            $return_total_inc_tax = $return_total;
            $return_transaction_data = [
                'total_before_tax' => $return_total,
                'final_total' => $return_total_inc_tax,
            ];
            if (empty($request->invoice_id)) {
                //Update reference count
                $ref_count = $this->setAndGetReferenceCount('sell-return');
                $return_transaction_data['invoice_no'] = $this->generateReferenceNumber('sell-return', $ref_count + 1, 'inv-');
            }
            
            $return_transaction = Transaction::where('type', 'sell_return')
            ->where('return_parent_id', $sell->id)
            ->first();
            if (!empty($return_transaction)) {
                $return_transaction->update($return_transaction_data);
            } else {
                $return_transaction_data['location_id'] = $sell->location_id;
                $return_transaction_data['type'] = 'sell_return';
                $return_transaction_data['status'] = 'final';
                $return_transaction_data['contact_id'] = $sell->contact_id;
                $return_transaction_data['transaction_date'] = Carbon::now();
                $return_transaction_data['created_by'] = Auth::id();
                $return_transaction_data['return_parent_id'] = $sell->id;

                $return_transaction = Transaction::create($return_transaction_data);
            }
            $this->updatePaymentStatus($return_transaction->id, $return_transaction->final_total);
            DB::commit();
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
            return redirect(route('sell-return.index'));
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
