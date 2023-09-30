<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\Message;

use App\Models\Contact;
use App\Models\Transaction;
use App\Models\Invoice;

use DB;
use \Carbon\Carbon;
use App\Traits\TransactionUtil;
use App\Traits\ProductUtil;

class PaymentController extends Controller
{
  use TransactionUtil, ProductUtil;
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request, $id)
  {
    $transaction = Transaction::find($id);

    if($request->ajax()) {
      if($transaction->payment_status != 'paid') {
        $paidAmount = $this->getTotalPaid($transaction->id);
        $payOffAmount = $transaction->final_total - $paidAmount;
        $payOffAmount = ($payOffAmount > 0) ? $payOffAmount : 0;

        $payment = new Invoice;
        $payment->amount = decimalNumber($payOffAmount);
        $payment->paid_on = Carbon::now()->format('d-m-Y');

        $view = view('payment.add-payment', compact('transaction', 'payment'))->render();
        $output = [
          'status' => 'due',
          'view' => $view,
        ];
      }
      else {
        $output = [
          'status' => 'paid',
          'view' => '',
          'msg' => trans('message.amount_already_paid')
        ];
      }

      return response()->json($output);
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function save(Request $request, $id)
  {
    try {
      $transaction = Transaction::find($id);
      if($transaction->payment_status != 'paid') {
        $inputs = $request->only(['payment_amount', 'payment_method']);

        $inputs['type']           = $transaction->type;
        $inputs['user_id']        = auth()->user()->id;
        $inputs['client_id']      = $transaction->contact_id;
        $inputs['transaction_id'] = $transaction->id;
        $inputs['payment_date']   = Carbon::parse($request->payment_date)->toDateTimeString();
        $inputs['payment_amount'] = str_replace(',', '', $inputs['payment_amount']) ?? 0;
        $inputs['total']          = str_replace(',', '', $inputs['payment_amount']) ?? 0;
        $inputs['note']           = $request->payment_note;

        $prefix_type = 'purchase';
        if (in_array($transaction->type, ['sell', 'sell_return'])) {
          $prefix_type = 'sell';
        } 
        elseif ($transaction->type == 'expense') {
          $prefix_type = 'expense';
        }

        DB::beginTransaction();

        //Generate reference number
        $lastInvoiceNum = Invoice::latest()->first()->invoice_number ?? 0;
        $ref_count = (int)substr($lastInvoiceNum, 4) + 1; // $this->setAndGetReferenceCount($prefix_type)
        $inputs['invoice_number'] = $this->generateReferenceNumber($prefix_type, $ref_count, 'REF', 6);

        $tp = Invoice::create($inputs);
        
        //update payment status
        $this->updatePaymentStatus($transaction->id, $transaction->final_total);
        DB::commit();
      }
    } 
    catch(\Exception $e) {
      DB::rollBack();

      \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

      session()->flash(Message::SAVE_FAILURE_VALUE, trans('message.item_saved_fail'));
      return redirect()->back()->withInput()->withErrors([
        Message::ERROR_KEY => trans('message.item_saved_fail'),
      ]);
    }

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return redirect()->back();
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    $transaction = Transaction::find($id);
    $payments = $transaction->invoices;

    if($request->ajax() && !empty($transaction)) {
      return view('payment.show', compact('transaction', 'payments'));
    }
    return view('payment.show', compact('transaction', 'payments'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function editPaymentDate(Request $request, Invoice $payment)
  {
      return view('payment.edit-payment-date',compact('payment'));
  }
  public function savePaymentDate(Request $request, Invoice $payment)
  {
      $payment->payment_date  = $request['payment_date'] ? Carbon::parse($request['payment_date'])->toDateTimeString() : "";
      $payment->save();
      session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
      return redirect()->back();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    if (request()->ajax()) {
      try {

          $payment = Invoice::find($id);

          DB::beginTransaction();

          if (!empty($payment->transaction_id)) {
              $this->deletePayment($payment->id);
          } else { //advance payment
              $adjusted_payments = Invoice::where('parent_id', 
                                          $payment->id)
                                          ->get();

              $total_adjusted_amount = $adjusted_payments->sum('total');

              //Get customer advance share from payment and deduct from advance balance
              $total_customer_advance = $payment->total - $total_adjusted_amount;
              if ($total_customer_advance > 0) {
                  $this->updateContactBalance($payment->client_id, $total_customer_advance , 'deduct');
              }

              //Delete all child payments
              foreach ($adjusted_payments as $adjusted_payment) {
                  //Make parent payment null as it will get deleted
                  $adjusted_payment->parent_id = null;
                  $this->deletePayment($adjusted_payment);
              }

              //Delete advance payment
              $payment->delete();
          }
          
          DB::commit();

          $output = ['success' => true,
                          'message' => __('message.payment_deleted_success')
                      ];
      } catch (\Exception $e) {
          DB::rollBack();

          \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
          
          $output = ['success' => false,
                          'message' => __('message.something_went_wrong')
                      ];
      }

      return $output;
    }
  }
  
    /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function getPayContactDue(Request $request,$contact_id)
  {
    if(request()->ajax()) {
      $due_payment_type = request()->input('type');
      $query = Contact::where('contacts.id',$contact_id)
              ->join('transactions AS t','contacts.id', '=', 't.contact_id');
      if($due_payment_type=='purchase'){
        $query->select(
          DB::raw("SUM(if(t.type = 'purchase', final_total,0)) as total_purchase"),
          DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(total) FROM invoices WHERE invoices.transaction_id=t.id), 0)) as total_paid"),
          'contacts.name',
          'contacts.supplier_business_name',
          'contacts.mobile',
          'contacts.id as contact_id'
        );
      } elseif ($due_payment_type == 'purchase_return') {
        $query->select(
          DB::raw("SUM(if(t.type = 'purchase_return', final_total,0)) as total_purchase_return"),
          DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM invoices WHERE invoices.transaction_id=t.id), 0)) as total_return_paid"),
          'contacts.name',
          'contacts.supplier_business_name',
          'contacts.id as contact_id'
        );
      } elseif ($due_payment_type == 'sell') {
        $query->select(
          DB::raw("SUM(if(t.type = 'sell', final_total,0)) as total_invoice"),
          DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*total,total)) FROM invoices WHERE invoices.transaction_id=t.id), 0))) as total_paid"),
          'contacts.name',
          'contacts.mobile',
          'contacts.id as contact_id'
        );
      } elseif ($due_payment_type == 'sell_return') {
        $query->select(
            DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
            DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(total) FROM invoices WHERE invoices.transaction_id=t.id), 0)) as total_return_paid"),
            'contacts.name',
            'contacts.mobile',
            'contacts.id as contact_id'
        );
      }
      //Query for opening balance details
      $query->addSelect(
          DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
          DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(total) FROM invoices WHERE invoices.transaction_id=t.id), 0)) as opening_balance_paid")
      );
      $contact_details = $query->first();
      $payment_line = new Invoice();
      if ($due_payment_type == 'purchase') {
          $contact_details->total_purchase = empty($contact_details->total_purchase) ? 0 : $contact_details->total_purchase;
          $payment_line->amount = $contact_details->total_purchase - $contact_details->total_paid;
      } elseif ($due_payment_type == 'purchase_return') {
          $payment_line->amount = $contact_details->total_purchase_return -
                              $contact_details->total_return_paid;
      } elseif ($due_payment_type == 'sell') {
          $contact_details->total_invoice = empty($contact_details->total_invoice) ? 0 : $contact_details->total_invoice;

          $payment_line->amount = $contact_details->total_invoice -
                              $contact_details->total_paid;
      } elseif ($due_payment_type == 'sell_return') {
          $payment_line->amount = $contact_details->total_sell_return -
                              $contact_details->total_return_paid;
      }

      //If opening balance due exists add to payment amount
      $contact_details->opening_balance = !empty($contact_details->opening_balance) ? $contact_details->opening_balance : 0;
      $contact_details->opening_balance_paid = !empty($contact_details->opening_balance_paid) ? $contact_details->opening_balance_paid : 0;
      $ob_due = $contact_details->opening_balance - $contact_details->opening_balance_paid;
      if ($ob_due > 0) {
          $payment_line->amount += $ob_due;
      }

      $contact_details->total_paid = empty($contact_details->total_paid) ? 0 : $contact_details->total_paid;
      $payment_line->paid_on = Carbon::now()->toDateTimeString();
      if($payment_line->amount > 0){

        $view = view('payment.getPayContactDue')->with(compact('contact_details', 'payment_line', 'due_payment_type', 'ob_due'))->render();
        $output = [
          'status' => 'due',
          'view' => $view,
        ];
      }
      else {
        $output = [
          'status' => 'paid',
          'view' => '',
          'message' => trans('message.amount_already_paid')
        ];
      }
      return response()->json($output);
    }

    
  }
  /**
   * Adds Payments for Contact due
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function postPayContactDue(Request  $request)
  {
      // if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {
      //     abort(403, 'Unauthorized action.');
      // }
      try {
          DB::beginTransaction();
          
          $payment =  $this->payContact($request);
          // dd($payment);
          DB::commit();
          $output = ['success' => true,
                          'message' => __('message.amount_already_paid')
                      ];
      } catch (\Exception $e) {
          DB::rollBack();
          \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
          
          $output = ['success' => false,
                        'message' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                    ];
      }

      return redirect()->back()->with(['status' => $output]);
  }
  public function viewPayment($payment_id)
  {
      if (request()->ajax()) {
          $single_payment_line = Invoice::findOrFail($payment_id);

          $transaction = null;
          if (!empty($single_payment_line->transaction_id)) {
              $transaction = Transaction::where('id', $single_payment_line->transaction_id)
                              ->with(['client', 'warehouse'])
                              ->first();
          } else {
              $child_payment = Invoice::where('parent_id', $payment_id)
                      ->with(['transaction', 'transaction.client', 'transaction.warehouse'])
                      ->first();
              $transaction = $child_payment->transaction;
          }
          
          return view('payment.single_payment_view')
                  ->with(compact('single_payment_line', 'transaction'));
      }
      
  }


}
