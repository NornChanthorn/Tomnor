<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use \Carbon\Carbon;
use App\Models\Loan;
use App\Models\Staff;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Schedule;
use App\Models\Transaction;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use App\Constants\LoanStatus;
use App\Constants\FormType;
use Illuminate\Validation\Rule;
use App\Constants\Message;
use App\Traits\TransactionUtil;
use App\Traits\ProductUtil;
class LoanCashController extends Controller
{
    use TransactionUtil, ProductUtil;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('loan-cash.browse')) {
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $date = dateIsoFormat($request->date) ?? "";
        $sdate= dateIsoFormat($request->date);
        $agents = [];
        $loans = Loan::where('type','cash')->where('status', '!=', LoanStatus::REJECTED);
      
        if (isAdmin() || empty(auth()->user()->staff)) {
            if (!empty($request->branch)) {
            $loans = $loans->where('branch_id', $request->branch);
            $agents = Staff::where('branch_id', $request->branch)->orderBy('name')->get();
            }
    
            if (!empty($request->agent)) {
            $loans = $loans->where('staff_id', $request->agent);
            }
        }else {
            // unsecure pomission
            $staff = auth()->user()->staff;
            if(!empty($staff)) {
            $loans->where('branch_id', $staff->branch->id);
            // $loans->where('staff_id', $staff->id);
            }
        }
        if(!empty($sdate)){
            $loans->where(function ($query) use ($sdate){
                $query->whereHas('schedules', function ($query) use ($sdate) {
                $query->where('payment_date',$sdate);
                });

            });
        }
        if (!empty($request->search)) {
            $searchText = $request->search;
            $loans = $loans->where(function ($query) use ($searchText) {
                $query->where('account_number', 'like', '%' . $searchText . '%')
                  ->orWhere('wing_code', 'like', '%' . $searchText . '%')
                  ->orWhere('client_code', 'like', '%' . $searchText . '%')
                  ->orWhere('account_number', 'like', '%' . $searchText . '%')
                  // Query client
                  ->orWhereHas('client', function ($query) use ($searchText) {
                    $query->where('name', 'like', '%' . $searchText . '%')
                    ->orWhere('id_card_number', 'like', '%' . $searchText . '%')
                    ->orWhere('first_phone', 'like', '%' . $searchText . '%')
                    ->orWhere('second_phone', 'like', '%' . $searchText . '%')
                    ->orWhere('sponsor_name', 'like', '%' . $searchText . '%')
                    ->orWhere('sponsor_phone', 'like', '%' . $searchText . '%');
                });
            });
        }
        $itemCount = $loans->count();
        $loans = $loans ->select(
            'loans.*',
            DB::raw("(SELECT payment_date FROM schedules WHERE paid_status = 0 AND loan_id = loans.id limit 1) as payment_date"),
            DB::raw("(SELECT IF(DATEDIFF(CURDATE(),payment_date)>0,DATEDIFF(CURDATE(),payment_date),0) FROM schedules WHERE paid_status = 0 AND loan_id = loans.id limit 1) as late_payment"),
            DB::raw("(SELECT total FROM schedules WHERE paid_status = 0 AND loan_id = loans.id limit 1) as total_amount"),
            DB::raw("(SELECT paid_total FROM schedules WHERE paid_status = 0 AND loan_id = loans.id limit 1) as total_paid_amount")
          )->sortable()->latest('created_at')->paginate(paginationCount());
        $offset = offset($request->page);
    
        return view('loan-cash.index', compact('date','agents', 'itemCount', 'loans', 'offset'));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Loan $loan)
    {
      if(!Auth::user()->can('loan-cash.add')) {
        return back()->with([
          Message::ERROR_KEY => trans('message.no_permission'),
          'alert-type' => 'warning'
        ], 403);
      }
     
        $title = trans('app.loan_cash').' -  '.trans('app.create');
        $formType = FormType::CREATE_TYPE;
        $branches = Branch::getAll();
        $clients = Client::orderBy('name')->get();
        $agents=[];
        $loan->account_number = nextLoanAccNum();
        return view('loan-cash.form',compact('title','loan','formType','branches','agents'));
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, Loan $loan)
    {
      if(!Auth::user()->can('loan-cash.edit') && !Auth::user()->can('loan-cash.add')) {
        return back()->with([
          Message::ERROR_KEY => trans('message.no_permission'),
          'alert-type' => 'warning'
        ], 403);
      }
     
        $validationRules = [
            'form_type' => ['required', Rule::in([FormType::CREATE_TYPE, FormType::EDIT_TYPE])],
            'client_id' => ['required', 'integer'],
            'loan_amount' => 'required|numeric',
        ];
        if (isAdmin() || empty(auth()->user()->staff)) {
            $validationRules = array_merge($validationRules, [
              'branch_id' => 'required|integer',
              'agent' => 'required|integer',
            ]);
        }
        $this->validate($request, $validationRules);
        //
        try{
            DB::beginTransaction();
            if (isAdmin() || empty(auth()->user()->staff)) {
                $loan->branch_id = $request->branch_id;
                // dd($request->agent);
                $loan->staff_id = Staff::where('user_id',$request->agent)->first()->id;
            
            }  else { // Auto-set branch and agent when staff creates loan
                $staff = auth()->user()->staff;
                $loan->branch_id = $staff->branch->id;
                $loan->staff_id = $staff->id;
            }
            if ($request->form_type == FormType::CREATE_TYPE) {
                $loan->type='cash';
                $loan->status = LoanStatus::PENDING;
    
            }
            if($loan->user_id==null){
              $loan->user_id = auth()->user()->id;
            }
            if($loan->account_number==null){
              $loan->account_number = nextLoanAccNum(); 
            }
            $loan->client_id = $request->client_id;
            $loan->loan_amount = $request->loan_amount;
            $loan->schedule_type = $request->schedule_type;
            $loan->client_code = $request->client_code;
            $loan->installment = $request->installment;
            $loan->frequency = $request->frequency;
            $loan->interest_rate = $request->interest_rate;
            $loan->loan_start_date      = dateIsoFormat($request->loan_start_date);
            $loan->first_payment_date   = addDays(dateIsoFormat($request->loan_start_date), $loan->frequency);
            $loan->note = $request->note;
            $loan->save();
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
        return redirect(route('loan-cash.show', $loan->id));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!Auth::user()->can('loan-cash.browse')) {
          return back()->with([
              Message::ERROR_KEY => trans('message.no_permission'),
              'alert-type' => 'warning'
          ], 403);
        }
        $loan = Loan::find($id);
        if($loan->type=='product'){
            return redirect()->route('loan.show',$id);
        }
        if ($loan->status == LoanStatus::REJECTED) {
        return redirect(route('loan-cash.index'));
        }
        $title = trans('app.detail');
        $formType = FormType::SHOW_TYPE;
        $loan->count = 1;
        $setting = GeneralSetting::first();
        return view('loan-cash.show', compact(
        'formType',
        'loan',
        'title',
        'setting'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        if(!Auth::user()->can('loan-cash.edit')) {
            return back()->with([
              Message::ERROR_KEY => trans('message.no_permission'),
              'alert-type' => 'warning'
            ], 403);
        }
        $loan = Loan::find($id);
        $setting = GeneralSetting::first();
        if (isPaidLoan($loan->id)) {
            return back();
        }
      
        $loan->count = Loan::count()+1;
    
        $title = trans('app.edit');
        $formType = FormType::EDIT_TYPE;
        $branches = Branch::getAll();
        $clients = Client::orderBy('name')->get();
        $agents = [];
      
        $branchId = old('branch') ?? $loan->branch_id;
        $agents = Staff::where('branch_id', $branchId)->orderBy('name')->get();

      
        return view('loan-cash.form', compact(
            'agents',
            'branches',
            'clients',
            'formType',
            'loan',
            'title',
            'setting'
        ));
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
    /**
   * Change loan status from AJAX request.
   *
   * @param Loan $loan
   * @param string $status Loan status to be changed to
   *
   * @return \Illuminate\Http\Response
   */
  public function changeStatus(Request $request, Loan $loan, $status)
  {
    if(!Auth::user()->can('loan-cash.approval') && !Auth::user()->can('loan-cash.reject')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    // if (!$request->ajax() || !in_array($status, [LoanStatus::ACTIVE, LoanStatus::REJECTED, LoanStatus::PENDING])) {
    //   abort(404);
    // }

    // try {
    //   DB::beginTransaction();

      if(empty($loan->transaction_id)) {
        $transaction = new Transaction;
        $transaction->location_id      = $loan->branch_id;
        $transaction->created_by       = $loan->user_id;
        $transaction->transaction_date = Carbon::now()->toDateTimeString();
        $transaction->contact_id       = $loan->client_id;
        $transaction->final_total      = $loan->loan_amount;
        $transaction->type             = 'leasing';
        $transaction->status           = 'final';
        $transaction->ref_no           = '';
        $transaction->discount_type    = 'fixed';
        $transaction->discount_amount  = 0;
        $transaction->shipping_charges = 0;
        $transaction->payment_status   = 'due';
        $transaction->others_charges   = $loan->branch->others_charges ?? 0;

        //Update reference count
        $ref_count = $this->setAndGetReferenceCount('sell');
        //Generate reference number
        if (empty($request->invoice_id)) {
          $transaction->invoice_no = $this->generateReferenceNumber('contacts', $ref_count, '', 6);
        }

        $transaction->save();
        // update loan after disburse
        $loan->disbursed_date = Carbon::now()->toDateString();
        $loan->transaction_id = $transaction->id;
      }

      $loan->status = $status;
      $loan->changed_by = auth()->user()->id;

      if ($status == LoanStatus::ACTIVE) {
        $loan->approved_date = date('Y-m-d');
      }
      if($loan->save()){
        
        if ($status == LoanStatus::ACTIVE) {
            $paymentSchedules = $this->calcPaymentSchedule($loan->id);
            
            // dd($paymentSchedules);
            foreach ($paymentSchedules as $paymentSchedule) {
              $schedule = new Schedule();
              $schedule->loan_id = $loan->id;
              $schedule->payment_date = $paymentSchedule['payment_date'];
              $schedule->principal = $paymentSchedule['principal'];
              $schedule->interest = $paymentSchedule['interest'];
              $schedule->total = $paymentSchedule['total'];
              $schedule->outstanding = $paymentSchedule['outstanding'];
              $schedule->save();
            }
        }
        
      }
      
      // DB::commit();
      return $paymentSchedule;
      if ($status == LoanStatus::ACTIVE) {
        $message = trans('message.loan_approved');
      }
      elseif ($status == LoanStatus::REJECTED) {
        $message = trans('message.loan_rejected');
      }
      else {
        $message = trans('message.loan_reverted');
      }

      session()->flash(Message::SUCCESS_KEY, $message);
    // }
    // catch(\Exception $e) {
    //   DB::rollBack();

    //   \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

    //   session()->flash(Message::SAVE_FAILURE_VALUE, trans('message.item_saved_fail'));
    // }
  }
}
