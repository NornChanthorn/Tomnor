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

          $wave = $request->wave ?? 0;
          $paymentDate = dateIsoFormat($request->payment_date);

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

          $newPaid = $invoice->payment_amount ;
          $depreAmount = $loan->depreciation_amount;
          $existingDepreciation = Depreciation::where('loan_id', $loan->id)->first();

          if($existingDepreciation){
            $oldPaidAmount = $existingDepreciation->paid_amount;
            $updatePaidAmount = $oldPaidAmount + $newPaid;
            $existingDepreciation->paid_amount = $updatePaidAmount;
            $existingDepreciation->outstanding_amount= $depreAmount - $updatePaidAmount ;
            $existingDepreciation->save();
          }
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
