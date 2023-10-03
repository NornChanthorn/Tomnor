<?php

namespace App\Http\Controllers;

use App\Models\Depreciation;
use Doctrine\Deprecations\Deprecation;
use Illuminate\Http\Request;
use Auth;
use App\Models\Loan;
use App\Constants\LoanStatus;
use App\Constants\RepayType;
use App\Models\Invoice;
use App\Constants\Message;
use Illuminate\Validation\Rule;

class DepreciationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->can('loan.pay')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $loan = Loan::where('status', LoanStatus::ACTIVE);

        $title = trans('app.pay_depreciation');
        return view('payment.depreciation-form', compact('title', 'loan'));
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
    public function store(Request $request)
    {


    }

    /**
     * Display the specified resource.
     *
         */

    public function show($loanId, $repayType)
        {
            if(!Auth::user()->can('loan.pay')) {
                return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
            }
            $loan = Loan::where('status', LoanStatus::ACTIVE)->find($loanId);
            //   if (empty($loan) || !in_array($repayType, repayTypes())) {
            //     return redirect(route('repayment.index'));
            //   }
            $data = $loan->id;
            if (empty($loan) ) {
                return redirect(route('repayment.index'));
            }
            if($repayType== RepayType::PAY_DEPRECIATION){
                // $data = 1;
                $title = trans('app.pay_depreciation');
                $repayLabel = trans('app.pay_depreciation');
            }
            if($repayType== RepayType::REPAY){
                $data = 2;
                $title = trans('app.repay');
                $repayLabel = trans('app.pay_depreciation');
            }
          return view('payment/depreciation-form', compact(
            // 'loan',
            // 'payoffInterest',
            // 'payoffPrincipal',
            // 'penaltyAmount',
            // 'remainingAmount',
            'repayLabel',
            'repayType',
            'title',
            'loan',
            'data'
          ));
        }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DownPayment  $downPayment
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, $loanId)
    {
        if(!Auth::user()->can('loan.pay')) {
            return back()->with([
            Message::ERROR_KEY => trans('message.no_permission'),
            'alert-type' => 'warning'
            ], 403);
        }
        $this->validate($request, [
            'repay_type' => Rule::in([RepayType::REPAY, RepayType::PAYOFF, RepayType::PAY_DEPRECIATION]),
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|gt:0',
            'payment_method' => 'required',
            'penalty_amount' => 'nullable|numeric',
        ]);
          if ($request->repay_type == RepayType::ADVANCE_PAY) {
            $this->validate($request, [
              'selected_schedules' => 'required',
            ]);
          }
          // Loan with unpaid schedule (s)
          $loan = Loan::with(['schedules' => function($query) use ($request) {
            $query->where('paid_status', 0);
            if ($request->repay_type == RepayType::ADVANCE_PAY) {
              $query->whereIn('id', $request->selected_schedules);
            }
            $query->orderBy('payment_date');
          }])
          ->whereHas('schedules')
          ->where('status', LoanStatus::ACTIVE)
          ->find($loanId);

          $repayType = $request->repay_type;
        //   if($repayType ==2){
        //     $originalPaymentAmount = $request->payment_amount - $request->penalty_amount + $request->wave;
        //     $penaltyAmount = $request->penalty_amount;
        //   }else{
        //     $paymentAmount = $originalPaymentAmount = $request->payment_amount;
        //     $penaltyAmount = $request->penalty_amount;
        //   }
          $wave = $request->wave ?? 0;
          $paymentDate = dateIsoFormat($request->payment_date);

          // Payoff, advance payment, or simple payment for 3 schedule types: decline, flat, or equal payment
        //   if ($repayType == RepayType::PAYOFF) {
        //     $i = 0;
        //     foreach ($loan->schedules as $schedule) {
        //       if ($i == 0) {
        //         $schedule->paid_interest = $schedule->interest;
        //         $schedule->paid_total = $schedule->total;
        //       }
        //       else {
        //         $schedule->paid_interest = 0;
        //         $schedule->paid_total = $schedule->principal;
        //       }

        //       $schedule->paid_principal = $schedule->principal;
        //       $schedule->paid_date = $paymentDate;
        //       $schedule->paid_status = 1;
        //       $schedule->save();
        //       $i++;
        //     }
        //     $loan->status = LoanStatus::PAID;
        //     $loan->save();
        //   }
        //   elseif ($repayType == RepayType::ADVANCE_PAY) {
        //     $amountToPay = $loan->schedules->sum('principal');
        //     if (decimalNumber($paymentAmount) != decimalNumber($amountToPay)) {
        //       session()->flash(Message::ERROR_KEY, trans('message.selected_schedule_amount_unequal'));
        //       return back()->withInput($request->all());
        //     }

        //     foreach ($loan->schedules as $schedule) {
        //       $schedule->paid_principal = $schedule->principal;
        //       $schedule->paid_total = $schedule->principal;
        //       $schedule->paid_date = $paymentDate;
        //       $schedule->paid_status = 1;
        //       $schedule->save();
        //     }

        //     // Code to change loan status to paid if all schedules are paid
        //     // This payment type isn't used in this system at this time
        //   }
        //   elseif ($repayType == RepayType::REPAY) {
        //     $remainingAmount = ($loan->schedules->sum('total') - $loan->schedules->sum('paid_total'));
        //     if ($paymentAmount >= $remainingAmount) {
        //       session()->flash(Message::ERROR_KEY, trans('message.payment_amount_lt_or_et_remaining_amount', [
        //         'amount' => decimalNumber($remainingAmount),
        //       ]));
        //       return back()->withInput($request->all());
        //     }

        //     // foreach ($loan->schedules as $key => $schedule) {
        //     //   // Pay interest
        //     //   if($loan->schedule_type != PaymentScheduleType::FLAT_INTEREST && $paymentAmount > 0 && $schedule->paid_interest < $schedule->interest) {
        //     //     $paidInterestAmount = $schedule->paid_interest;
        //     //     $newInterestAmount = ($paidInterestAmount + $paymentAmount);

        //     //     if ($newInterestAmount >= $schedule->interest) {
        //     //       if($schedule->interest>0){
        //     //         $schedule->paid_interest = $schedule->interest;
        //     //       }

        //     //       $paymentAmount -= ($schedule->interest - $paidInterestAmount);
        //     //     }
        //     //     else {
        //     //       if($newInterestAmount>0){
        //     //         $schedule->paid_interest = decimalNumber($newInterestAmount);
        //     //       }

        //     //       $paymentAmount = 0;
        //     //     }
        //     //   }
        //     //   // Pay principal
        //     //   if ($paymentAmount > 0 && $schedule->paid_principal < $schedule->principal) {
        //     //     $paidPrincipalAmount = $schedule->paid_principal;
        //     //     $newPrincipalAmount = $paidPrincipalAmount + $paymentAmount;

        //     //     if ($newPrincipalAmount >= $schedule->principal) {
        //     //       $schedule->paid_principal = $schedule->principal;
        //     //       $paymentAmount -= ($schedule->principal - $paidPrincipalAmount);
        //     //     }
        //     //     else {
        //     //       $schedule->paid_principal = decimalNumber($newPrincipalAmount);
        //     //       $paymentAmount = 0;
        //     //     }
        //     //   }
        //     //   if($key==0 && $penaltyAmount > 0){
        //     //     $schedule->paid_penalty = $penaltyAmount;
        //     //     $schedule->paid_total = ($schedule->paid_principal + $schedule->paid_interest + $penaltyAmount);
        //     //   }else{
        //     //     $schedule->paid_total = ($schedule->paid_principal + $schedule->paid_interest);
        //     //   }

        //     //   if (($schedule->paid_total - $schedule->paid_penalty) == $schedule->total) {
        //     //     $schedule->paid_status = 1;
        //     //   }
        //     //   $schedule->paid_date = $paymentDate;
        //     //   $schedule->save();
        //     //   if (decimalNumber($paymentAmount) <= 0.0) {
        //     //     break;
        //     //   }
        //     // }
        //     // Change loan status to paid if all schedules are paid
        //     $unpaidSchedules = Schedule::where('paid_status', 0)->where('loan_id', $loan->id)->get();
        //     if (count($unpaidSchedules) == 0) {
        //       $loan->status = LoanStatus::PAID;
        //       $loan->save();
        //     }
        //   }

          // Insert payment info into invoices table
          $invoice = new Invoice();
          $invoice->type              = 'depreciation';
          $invoice->user_id           = auth()->user()->id;
          $invoice->loan_id           = $loan->id;
          $invoice->transaction_id    = $loan->transaction_id;
          $invoice->client_id         = $loan->client->id;
          $invoice->payment_amount    = $request->payment_amount;
          $invoice->penalty           = 0;
          $invoice->wave              = $wave;
          $invoice->total             = $request->payment_amount;
          $invoice->payment_method    = $request->payment_method;
          $invoice->reference_number  = $request->reference_number;
          $invoice->payment_date      = $paymentDate;
          $invoice->note              = $request->note;
          if (!empty($request->receipt_photo)) {
            $invoice->document = $this->uploadImage($this->imageFolder, $request->receipt_photo);
          }
          $lastInvoiceNum = Invoice::latest()->first()->invoice_number ?? 0;
          $invoice->invoice_number = 'REF-' . str_pad(substr($lastInvoiceNum, 4) + 1, 6, 0, STR_PAD_LEFT);
          $invoice->save();

        //   $existingDepreciation = Depreciation::where('loan_id', $loanId)->first();

            // Depreciation::create([
            //     'loan_id' => $loan->id, // Use the appropriate reference to the invoice
            //     'invoice_id' => $invoice->id, // Assuming you have a 'schedule_id' column
            //     'DepreciationAmount' => $invoice->payment_amount,
            //     'outstanding_amount' => $invoice->payment_amount,
            //     'payment_method' => $invoice->payment_method
            //     // Add other fields as needed
            //   ]);



        session()->flash(Message::SUCCESS_KEY, trans('message.repayment_success'));
        $redirectRoute = ($repayType == RepayType::PAY_DEPRECIATION ? route('payments.paydepreciation', [$loan->id, $request->repay_type]) : route('repayment.show', [$loan->id, $request->repay_type]));
        return redirect($redirectRoute);



    }
    public function edit(DownPayment $downPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DownPayment  $downPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DownPayment $downPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DownPayment  $downPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(DownPayment $downPayment)
    {
        //
    }

    // public function down_payment()
    // {
    //     return $this->hasOne(DownPayment::class);
    // }
}
