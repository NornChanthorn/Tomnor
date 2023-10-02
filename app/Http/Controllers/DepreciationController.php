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
            $data = Depreciation::with('loan')->get();
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


        //   $penaltyAmount = $this->calcPenaltyAmount($loan->id);
        //   $payoffInterest = null;
        //   $payoffPrincipal = null;
        //   $remainingAmount = null;
        //   if ($repayType == RepayType::DEPRECIATION) {
        //     $title = trans('app.pay_depreciation');
        //     $repayLabel = trans('app.pay_depreciation');
        //     $unpaidSchedules = Schedule::where('loan_id', $loanId)->where('paid_status', 0)->orderBy('payment_date')->get();
        //     $unpaidInterestSchedules = Schedule::where('loan_id', $loanId)->where('paid_status', 0)->get();
        //     foreach ($unpaidSchedules as $unpaid){
        //       $payoffPrincipal += $unpaid->principal - $unpaid->paid_principal;
        //     }
        //     foreach ($unpaidInterestSchedules as $unpaidInterest){
        //       $payoffInterest += $unpaidInterest->interest-$unpaidInterest->paid_interest;
        //     }
        //   }
        //   elseif ($repayType == RepayType::ADVANCE_PAY) {
        //     $title = trans('app.advance_payment');
        //     $repayLabel = trans('app.advance_pay');
        //   }
        //   else {

        //     $title = trans('app.payment');
        //     $repayLabel = trans('app.repay');
        //     $unpaidSchedules = Schedule::where('loan_id', $loanId)->where('paid_status', 0)->orderBy('payment_date')->get();
        //     $unpaidInterestSchedules = Schedule::where('loan_id', $loanId)->where('paid_status', 0)->get();
        //     $amountUnpaid=Schedule::where('loan_id', $loanId)->where('paid_status', 0)->orderBy('payment_date')->first();
        //     $remainingAmount =  ($amountUnpaid->principal + $amountUnpaid->interest)-($amountUnpaid->paid_principal + $amountUnpaid->paid_interest);
        //   }

          return view('payment/depreciation-form', compact(
            // 'loan',
            // 'payoffInterest',
            // 'payoffPrincipal',
            // 'penaltyAmount',
            // 'remainingAmount',
            'repayLabel',
            'repayType',
            'title',
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
        // dd($request->all());
        if(!Auth::user()->can('loan.pay')) {
            return back()->with([
            Message::ERROR_KEY => trans('message.no_permission'),
            'alert-type' => 'warning'
            ], 403);
        }

        $this->validate($request, [
            'repay_type' => Rule::in([RepayType::PAY_DEPRECIATION ]),
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|gt:0',
            'payment_method' => 'required',
            'penalty_amount' => 'nullable|numeric',
        ]);

        // Loan with unpaid schedule (s)
        $loan = Loan::with(['schedules' => function($query) use ($request) {
            $query->where('paid_status', 0);
            $query->orderBy('payment_date');
        }])
        ->whereHas('schedules')
        ->where('status', LoanStatus::ACTIVE)
        ->find($loanId);

        if (empty($loan->schedules)) {
            return redirect(route('repayment.index'));
        }
        $repayType = $request->repay_type;
        $wave = $request->wave ?? 0;
        $paymentDate = dateIsoFormat($request->payment_date);


        // Insert payment info into invoices table
        $invoice = new Invoice();
        $invoice->type              = 'leasing';
        $invoice->user_id           = auth()->user()->id;
        $invoice->loan_id           = $loan->id;
        $invoice->transaction_id    = $loan->transaction_id;
        $invoice->client_id         = $loan->client->id;
        $invoice->payment_amount    = $originalPaymentAmount;
        $invoice->penalty           = $penaltyAmount;
        $invoice->wave              = $wave;
        $invoice->total             = ($originalPaymentAmount + $penaltyAmount - $wave);
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

        session()->flash(Message::SUCCESS_KEY, trans('message.repayment_success'));
        $redirectRoute = ($repayType == RepayType::PAYOFF ? route('repayment.index') : route('repayment.show', [$loan->id, $request->repay_type]));
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
