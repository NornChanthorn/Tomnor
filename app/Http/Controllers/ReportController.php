<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

use App\Constants\DurationType;
use App\Constants\LoanStatus;
use App\Constants\ReportLoanStatus;
use App\Constants\ContactType;

use App\Models\AgentCommission;
use App\Models\ContactGroup;
use App\Models\Branch;
use App\Models\CommissionPayment;
use App\Models\Client;
use App\Models\Loan;
use App\Models\Invoice;
use App\Models\Staff;
use App\Models\Schedule;
use App\Models\Variantion;
use App\Models\VariantionLocationDetails;
use App\Models\Transaction;
use App\Models\PurchaseLine;
use App\Models\TransactionSellLine;
use App\Models\Contact;

use App\Traits\AgentUtil;
use App\Traits\TransactionUtil;

use Auth;
use DB;
use \Carbon\Carbon;

class ReportController extends Controller
{
  use AgentUtil;

  use TransactionUtil;

  protected $commissionPayment, $invoice;

  public function __construct(CommissionPayment $commissionPayment, Invoice $invoice)
  {
    //$this->middleware('role:'. UserRole::ADMIN)->except('overdueLoan', 'clientPayment');

    $this->commissionPayment = $commissionPayment;
    $this->invoice = $invoice;
  }

  /**
   * Display a listing of disbursed loans.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function disbursedLoan(Request $request)
  {
    if(!Auth::user()->can('report.loan-approval')){
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $loanCount = 0;
    $branchTitle = Branch::find($request->branch)->location ?? trans('app.all_branches');
    $agentName = Staff::find($request->agent)->name ?? trans('app.all_agents');
    $startDate = dateIsoFormat($request->start_date) ?? date('Y-m-d');
    $endDate = dateIsoFormat($request->end_date) ?? date('Y-m-d');
    $disbursedLoans = Loan::whereIn('status', [LoanStatus::ACTIVE, LoanStatus::PAID])
    ->whereBetween('approved_date', [$startDate, $endDate]);
    $agents = [];

    if ($request->branch !== null) {
      $disbursedLoans = $disbursedLoans->where('branch_id', $request->branch);
      $agents = Staff::where('branch_id', $request->branch)->orderBy('name')->get();
    }

    if ($request->agent !== null) {
      $disbursedLoans = $disbursedLoans->where('staff_id', $request->agent);
    }

    $totalLoanAmount = $disbursedLoans->sum('loan_amount');
    $totalDepreciation = $disbursedLoans->sum('depreciation_amount');
    $totalDownPayment = $disbursedLoans->sum('down_payment_amount');
    $itemCount = $disbursedLoans->count();
    $disbursedLoans = $disbursedLoans->sortable()->orderBy('client_code', 'desc')->paginate(paginationCount());
    $offset = offset($request->page);
    $branches = Branch::getAll();

    return view('report.disbursed-loan', compact(
      'agentName',
      'agents',
      'branches',
      'disbursedLoans',
      'branchTitle',
      'endDate',
      'itemCount',
      'loanCount',
      'offset',
      'startDate',
      'totalDepreciation',
      'totalDownPayment',
      'totalLoanAmount'
    ));
  }

  /**
   * Display a listing of overdue loans.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function overdueLoan(Request $request)
  {
    if(!Auth::user()->can('report.loan-expired')){
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }
    $date = dateIsoFormat($request->date) ?? date('Y-m-d');
    $sdate=dateIsoFormat($request->date);
    $agents = [];
    $overdueLoans = Loan::where('status', LoanStatus::ACTIVE)
    ->with(['schedules' => function ($query) {
      $query->where('paid_status', 0)->orderBy('payment_date');
    }])
    ->whereHas('schedules', function ($query) {
      $query->where('paid_status', 0)->whereRaw('payment_date < CURDATE()');
    });

    if (isAdmin() || empty(auth()->user()->staff)) {
      if (!empty($request->branch)) {
        $overdueLoans = $overdueLoans->where('branch_id', $request->branch);
        $agents = Staff::where('branch_id', $request->branch)->orderBy('name')->get();
      }

      if (!empty($request->agent)) {
        $overdueLoans = $overdueLoans->where('staff_id', $request->agent);
      }
    }
    else {
      $staff = auth()->user()->staff;
      if(!empty($staff)) {
        $overdueLoans = $overdueLoans->where('branch_id', $staff->branch->id)->where('staff_id', $staff->id);
      }
    }
    if(!empty($sdate)){
      $overdueLoans->where(function ($query) use ($sdate){
        $query->whereHas('schedules', function ($query) use ($sdate) {
          $query->where('payment_date',$sdate);
        });

      });
      
    }
    if (!empty($request->search)) {
      $searchText = $request->search;

      $overdueLoans = $overdueLoans->where(function ($query) use ($searchText) {
        $query->where('account_number', 'like', '%' . $searchText . '%')
        ->orWhere('wing_code', 'like', '%' . $searchText . '%')
        ->orWhere('client_code', 'like', '%' . $searchText . '%')

        // Query client
        ->orWhereHas('client', function ($query) use ($searchText) {
          $query->where('name', 'like', '%' . $searchText . '%')
          ->orWhere('id_card_number', 'like', '%' . $searchText . '%')
          ->orWhere('first_phone', 'like', '%' . $searchText . '%')
          ->orWhere('second_phone', 'like', '%' . $searchText . '%')
          ->orWhere('sponsor_name', 'like', '%' . $searchText . '%')
          ->orWhere('sponsor_phone', 'like', '%' . $searchText . '%');
        });

        // Query product
        // ->orWhereHas('product', function ($query) use ($searchText) {
        //   $query->where('name', 'like', '%' . $searchText . '%');
        // });
      });
    }
    if(!empty($request->sort)){
      $sort = $request->sort;
    }else{
      $sort = 'dec';
    }
    $overdueLoans  = $overdueLoans->select(
      'loans.*',
      DB::raw("(SELECT payment_date FROM schedules WHERE paid_status = 0 AND loan_id = loans.id ORDER BY payment_date limit 1) as payment_date"),
      DB::raw("(SELECT IF(DATEDIFF(CURDATE(),payment_date)>0,DATEDIFF(CURDATE(),payment_date),0) FROM schedules WHERE paid_status = 0 AND loan_id = loans.id ORDER BY payment_date limit 1) as late_payment"),
      DB::raw("(SELECT total FROM schedules WHERE paid_status = 0 AND loan_id = loans.id ORDER BY payment_date limit 1) as total_amount"),
      DB::raw("(SELECT paid_total FROM schedules WHERE paid_status = 0 AND loan_id = loans.id ORDER BY payment_date limit 1) as total_paid_amount")
    );
    $itemCount = $overdueLoans->count();
    $overdueLoans = $overdueLoans->sortable()->orderBy('late_payment',$sort)->paginate(paginationCount());
    $offset = offset($request->page);

    return view('report/overdue-loan', compact('date','agents', 'itemCount', 'offset', 'overdueLoans'));
  }

  /**
   * Display a listing of loans with a specific type: Pending, Active, Paid, and others.
   *
   * @param Request $request
   * @param string $status
   *
   * @return Response
   */
  public function loan(Request $request, $status)
  {
    if(!Auth::user()->can('report.loan')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    // if (!in_array($status, array_keys(reportLoanStatuses()))) {
    //   abort(404);
    // }

    $activeStatus = $status;
    $filteredLoans = Loan::query();
    if($status != 'all') {
      $filteredLoans = Loan::where('status', $status);
    }

    if($request->get('q')) {
      $searchQuery = $request->get('q');
      $filteredLoans = $filteredLoans->where(function($query) use($searchQuery) {
        $query->orWhere('account_number', 'LIKE', "%{$searchQuery}%")
        ->orWhere('client_code', 'LIKE', "%{$searchQuery}%");
      })->orWhereHas('client', function($query) use($searchQuery) {
        $query->where('name', 'LIKE', "%{$searchQuery}%");
        $query->orWhere('first_phone', 'LIKE', "%{$searchQuery}%");
        $query->orWhere('second_phone', 'LIKE', "%{$searchQuery}%");
      });
    }

    // if(!isAdmin() && auth()->user()->staff) {
    //   $staff = auth()->user()->staff;
    //   $filteredLoans = $filteredLoans->where('branch_id', $staff->branch_id);
    // }
    // else {
    //   if($request->get('branch') && !empty($request->get('branch'))) {
    //     $filteredLoans = $filteredLoans->where('branch_id', $request->get('branch'));
    //   }
    // }

    if($request->get('agent') && !empty($request->get('agent'))) {
      $filteredLoans = $filteredLoans->where('staff_id', $request->get('agent'));
    }
    // dd($filteredLoans->get());

    $itemCount = $filteredLoans->count();
    $filteredLoans = $filteredLoans->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);

    $agents = Staff::getAll();
    $branches = Branch::getAll();

    $clientCount = Client::count();
    $loanCount          = Loan::count();
    $pendingLoanCount   = Loan::where('status', ReportLoanStatus::PENDING)->count();
    $activeLoanCount    = Loan::where('status', ReportLoanStatus::ACTIVE)->count();
    $paidLoanCount      = Loan::where('status', ReportLoanStatus::PAID)->count();
    $rejectedLoanCount  = Loan::where('status', ReportLoanStatus::REJECTED)->count();
    $overdueLoanCount   = Loan::where('status', LoanStatus::ACTIVE)->whereHas('schedules', function ($query) {
      $query->where('paid_status', 0)->whereRaw('payment_date < CURDATE()');
    })->count();

    return view('report/loan', compact(
      'activeLoanCount',
      'itemCount',
      'filteredLoans',
      'offset',
      'paidLoanCount',
      'pendingLoanCount',
      'rejectedLoanCount',
      'status',
      'agents',
      'branches',
      'loanCount',
      'overdueLoanCount',
      'clientCount',
      'activeStatus'
    ));
  }

  /**
   * Display monthly or yearly financial statements
   *
   * @param Request $request
   *
   * @return Response
   */
  public function financialStatement(Request $request)
  {
    if(!Auth::user()->can('report.financial')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $reportType = $request->report_type ?? DurationType::YEARLY;
    if(!in_array($reportType, [DurationType::YEARLY, DurationType::MONTHLY])) {
      return back();
    }

    // Calculate loan summary info
    $activeLoans = Loan::select([
      DB::raw("SUM(loan_amount) AS total_load_amount"), DB::raw("SUM(depreciation_amount) AS total_depreciation_amount"), DB::raw("SUM(down_payment_amount) AS total_down_payment_amount")
    ])->whereIn('status', [LoanStatus::ACTIVE, LoanStatus::PAID])->get();
    $activeSchedules = Schedule::select([
      DB::raw("SUM(interest) AS total_interest"), DB::raw("SUM(paid_interest) AS total_paid_interest"), DB::raw("SUM(paid_principal) AS total_paid_principle")
    ])->where('paid_status',1)->whereHas('loan', function ($query) {
      // $query->whereIn('status', [LoanStatus::ACTIVE, LoanStatus::PAID]);
      $query->orWhere('status', LoanStatus::ACTIVE);
      $query->orWhere('status', LoanStatus::PAID);
    })->get();
    $totalLoanAmount = $activeLoans[0]->total_load_amount;
    $totalDepreciation = $activeLoans[0]->total_depreciation_amount;
    $totalDownPayment = $activeLoans[0]->total_down_payment_amount;
    $totalInterest = $activeSchedules[0]->total_interest;
    $totalPaidInterest = $activeSchedules[0]->total_paid_interest;
    $totalPaidPrincipal = $activeSchedules[0]->total_paid_principle;

    // Calculate detail info for a month or year
    $isYearlyReport = ($reportType == DurationType::YEARLY);
    $filteredYear = $request->year ?? date('Y');
    $branchTitle = Branch::find($request->branch)->location ?? trans('app.all_branches');
    $filteredSchedules = Schedule::whereHas('loan', function ($query) use ($request) {
      $query->whereIn('status', [LoanStatus::ACTIVE, LoanStatus::PAID]);
      if ($request->branch !== null) {
        $query->where('branch_id', $request->branch);
      }
    })->where('paid_status',1)->whereRaw("$filteredYear = YEAR(paid_date)");

    if ($isYearlyReport) {
      $filteredSchedules = $filteredSchedules->get();
      $dataRange = 12;
    }
    else { // Monthly report
      $filteredSchedules = $filteredSchedules->whereRaw("$request->month = MONTH(paid_date)")->get();
      $dataRange = dateIsoFormat($filteredYear . '-' . $request->month, 't');
    }

    $filteredData = array_map(function () {
      return ['paid_interest' => 0, 'paid_principal' => 0,'paid_penalty' => 0, 'paid_total' => 0];
    }, range(1, $dataRange));
    foreach ($filteredSchedules as $schedule) {
      $dataIndex = (substr($schedule->paid_date, ($isYearlyReport ? 5 : 8), 2) - 1);
      $filteredData[$dataIndex]['paid_interest'] += $schedule->paid_interest;
      $filteredData[$dataIndex]['paid_principal'] += $schedule->paid_principal;
      $filteredData[$dataIndex]['paid_penalty'] += $schedule->paid_penalty;
      $filteredData[$dataIndex]['paid_total'] += ($schedule->paid_principal + $schedule->paid_penalty + $schedule->paid_interest);
    }

    $branches = Branch::getAll();
    return view('report.financial-statement', compact(
      'branches', 'branchTitle', 'filteredYear', 'filteredData', 'reportType', 'totalDepreciation', 'totalDownPayment', 'totalInterest', 'totalLoanAmount', 'totalPaidInterest', 'totalPaidPrincipal'
    ));
  }
  /**
   * Display monthly or yearly Cash Income
   *
   * @param Request $request
   *
   * @return Response
   */
  public function cash_income(Request $request)
  {
    if(!Auth::user()->can('report.cashincome')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }
    $startDate = !empty($request->start_date) ? Carbon::createFromFormat('d-m-Y', $request->start_date) : Carbon::now()->startOfDay();
    $startDate = $startDate->format('Y-m-d');
    $endDate = !empty($request->end_date) ? Carbon::createFromFormat('d-m-Y', $request->end_date) : Carbon::now()->endOfDay();
    $endDate = $endDate->format('Y-m-d');
    $totalDownPayment = Invoice::query();
    $totalloanRepayment = Invoice::leftjoin('loans','invoices.loan_id','=','loans.id')->where('invoices.type','leasing');
    $totalSaleAmount = Invoice::leftJoin('transactions','invoices.transaction_id','=','transactions.id')->where('transactions.type','sell');
    $purchaseCustomer = Transaction::join('invoices as i','i.transaction_id','=','transactions.id')
    ->whereBetween('i.payment_date',[$startDate." 00:00:00",$endDate.' 23:59:59'])
    ->where('transactions.type','purchase')->where('transactions.contact_group_id',2);
    if(!empty($request->branch)){
      // $totalDownPayment->where('branch_id',$request->branch);
      $totalloanRepayment->where('branch_id',$request->branch);
      $totalSaleAmount->where('location_id',$request->branch);
      $purchaseCustomer->where('location_id',$request->branch);
    }
   
    $total= new \stdClass;
    $total->downPayment = $totalDownPayment->whereBetween('payment_date', [$startDate." 00:00:00", $endDate.' 23:59:59'])->where('type','leasing-dp')->sum('total');
    $total->loanRepayment = $totalloanRepayment->whereBetween('invoices.payment_date', [$startDate." 00:00:00", $endDate.' 23:59:59'])->sum('payment_amount');
    $total->saleAmount = $totalSaleAmount->whereBetween('invoices.payment_date', [$startDate." 00:00:00", $endDate.' 23:59:59'])->sum('total');
    $branches= Branch::get();
    $location_filter = '';
    if(!empty($request->branch)) {
      $location_filter .= "AND transactions.location_id='{$request->branch}'";
    }
    $purchaseCustomer=$purchaseCustomer->sum('i.payment_amount');

    return view('report.cash-income',compact('startDate','endDate','total','branches','purchaseCustomer'));

  }
  /**
   * Display a listing of client payments.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function clientPayment(Request $request)
  {
    if(!Auth::user()->can('report.payment')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $startDate = !empty($request->start_date) ? Carbon::parse($request->start_date) : Carbon::now();
    $endDate = !empty($request->end_date) ? Carbon::parse($request->end_date) : Carbon::now();

    $payments = $this->invoice;
    $payments = $payments->whereHas('loan', function($query) use($request) {
      if($request->branch) {
        $query->where('branch_id', $request->branch);
      }
      if($request->agent) {
        $query->where('staff_id', $request->agent);
      }
      if($request->q) {
        $query->where('account_number', 'LIKE', "%{$request->q}%");
      }
     
    });
    if($request->type) {
      $payments->where('type', "{$request->type}");
    }
    $payments = $payments->whereBetween('payment_date', [$startDate->startOfDay(), $endDate->endOfDay()]);
    // dd($payments->get());

    $totalAmount = number_format($payments->sum('total'), 2);
    $itemCount = $payments->count();
    $payments = $payments->sortable()->latest()->paginate(paginationCount());

    $agents = Staff::getAll();
    $branches = Branch::getAll();
    $offset = offset($request->page);
    $date = $startDate->format('d-m-Y') . (!empty($request->end_date) ? (' - '.$endDate->format('d-m-Y')) : '');

    return view('report.client-payment', compact('itemCount', 'offset', 'payments', 'totalAmount', 'date', 'branches', 'agents'));
  }

  /**
   * Display receipt of client payment for printing.
   *
   * @param Invoice $invoice
   *
   * @return Response
   */
  public function clientPaymentReceipt(Invoice $invoice)
  {
    return view('partial/receipt', compact('invoice'));
  }

  /**
   * Display client registration report.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function clientRegistration(Request $request)
  {
    if(!Auth::user()->can('report.customer')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }
    $search = $request->get('search');

    $clients = Client::whereHas('loans', function ($query) use($request) {
      $query->whereNotIn('status', [LoanStatus::PENDING, LoanStatus::REJECTED]);
      if($request['agent']) {
        $query->where('staff_id', $request['agent']);
      }
    })->where(function($query) use($search) {
      $query->orWhere('name', 'LIKE', "%{$search}%")
      ->orWhere('id_card_number', 'LIKE', "%{$search}%")
      ->orWhere('first_phone', 'LIKE', "%{$search}%");
    });

    $itemCount = $clients->count();
    $clients = $clients->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);
    $agents = Staff::getAll();

    return view('report/client-registration', compact('agents', 'clients', 'itemCount', 'offset'));
  }

  /**
   * Display loan portfolio report.
   *
   * @param Request $request
   * @param Client $client
   *
   * @return Response
   */
  public function loanPortfolio(Request $request, Client $client)
  {
    $loans = $client->loans()->whereNotIn('status', [LoanStatus::PENDING, LoanStatus::REJECTED])
    ->orderBy('id', 'desc')->get();

    return view('report/loan-portfolio', compact('client', 'loans'));
  }

  /**
   * Display a listing of commission payments.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function commissionPayment(Request $request)
  {
    if(!Auth::user()->can('report.commission-pay')){
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $commissionPayments = $this->commissionPayment;
    if (!empty($request->start_date) && !empty($request->end_date)) {
      $commissionPayments = $commissionPayments->whereBetween('paid_date', [dateIsoFormat($request->start_date), dateIsoFormat($request->end_date)]);
    }

    $itemCount = $commissionPayments->count();
    $commissionPayments = $commissionPayments->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);

    return view('report/commission-payment', compact('commissionPayments', 'itemCount', 'offset'));
  }

  /**
   * Display a listing of agents.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function agent(Request $request)
  {
    if(!Auth::user()->can('report.agent')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $totalCommission = $this->getAgentCommission(); // Commission amount of all agents
    $paidCommission = CommissionPayment::sum('amount');
    $agents = Staff::with(['loans' => function ($query) {
      $query->where('status', LoanStatus::ACTIVE);
    }]);
    $itemCount = $agents->count();
    $agents = $agents->sortable()->orderBy('name')->paginate(paginationCount());
    $offset = offset($request->page);

    return view('report.agent', compact('agents', 'itemCount', 'offset', 'paidCommission', 'totalCommission'));
  }

  /**
   * Display agent detail and related info.
   *
   * @param Request $request
   * @param Staff $agent
   *
   * @return Response
   */
  public function agentDetail(Request $request, Staff $agent)
  {
    if(!Auth::user()->can('report.agent')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $loans = Loan::where('staff_id', $agent->id)->where('status', LoanStatus::ACTIVE);
    $itemCount = $loans->count();
    $loans = $loans->paginate(paginationCount());
    $offset = offset($request->page);

    return view('report.agent-detail', compact('agent', 'itemCount', 'loans', 'offset'));
  }

  /**
   * Display each agent's financial statement.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function agentCommission(Request $request)
  {
    if(!Auth::user()->can('report.agent')){
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $agents = Staff::sortable()->orderBy('name')->paginate(paginationCount());

    // Get each agent's commissions
    foreach ($agents as $agent) {
      $agent->total_commission = $this->getAgentCommission($agent->id);
      $agent->paid_commission = CommissionPayment::where('staff_id', $agent->id)->sum('amount');
    }

    $itemCount = $agents->count();
    $offset = offset($request->page);

    return view('report/agent-commission', compact('agents', 'itemCount', 'offset'));
  }

  public function profitLoss(Request $request)
  {
    if(!Auth::user()->can('report.profit-loss')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $start_date = $request->get('start_date') ? Carbon::createFromFormat('d-m-Y', $request->get('start_date')) : Carbon::now();
    $end_date = $request->get('end_date') ? Carbon::createFromDate('d-m-Y', $request->get('end_date')) : Carbon::now();
    $location_id = $request->get('branch');
    // dd($start_date);

    //For Opening stock date should be 1 day before
    $day_before_start_date = $start_date->subDay()->format('Y-m-d');
    //Get Opening stock
    $opening_stock = $this->getOpeningClosingStock($day_before_start_date, $location_id, true);

    //Get Closing stock
    $closing_stock = $this->getOpeningClosingStock($end_date, $location_id);

    //Get Purchase details
    // $purchase_details = $this->getPurchaseTotals($start_date, $end_date, $location_id);

    //Get Sell details
    // $sell_details = $this->getSellTotals($start_date, $end_date, $location_id);

    $branches = Branch::getAll();
    // $loans = $result->latest()->paginate(paginationCount());
    $loans = [];
    $offset = offset($request->page);
    $query = $request->all();

    return view('report/profit-loss', compact('loans', 'offset', 'query', 'branches'));
  }

  public function productStock(Request $request)
  {
    if(!Auth::user()->can('report.stock')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $startDate = !empty($request->get('start_date')) ? Carbon::createFromFormat('d-m-Y', $request->get('start_date')) : null;
    $endDate = !empty($request->get('end_date')) ? Carbon::createFromFormat('d-m-Y', $request->get('end_date')) : null;
    $location_filter = '';
    $agent_filter = '';

    $query = Variantion::join('products as p', 'p.id', '=', 'variantions.product_id')
      ->join('units', 'p.unit_id', '=', 'units.id')
      ->leftjoin('variantion_location_details as vld', 'variantions.id', '=', 'vld.variantion_id')
      ->whereIn('p.type', ['single', 'variant']);

    if (!empty($request->branch)) {
      $location_id = $request->branch;
      $query->where('vld.location_id', $location_id);

      $location_filter .= "AND transactions.location_id='{$location_id}'";
    }

    if(!empty($request->q)) {
      $query->where('p.code', 'LIKE', "%{$request->q}%")->orWhere('p.name', 'LIKE', "%{$request->q}%")->orWhere(DB::raw('concat(p.name, "-", variantions.name)'), 'LIKE', "%".str_replace(' ', '%', $request->q)."%");
    }

    if(!empty($startDate) && !empty($endDate)) {
      $location_filter .= " AND (transactions.transaction_date>='{$startDate}' AND transactions.transaction_date<='{$endDate}')";
    }
    elseif(!empty($startDate)) {
      $location_filter .= " AND (transactions.transaction_date>='{$startDate}')";
    }
    elseif(!empty($endDate)) {
      $location_filter .= " AND (transactions.transaction_date<='{$endDate}')";
    }

    if(!empty($request->agent)) {
      $agent_filter .= " AND (transactions.created_by='{$request->agent}')";
    }

    $products = $query->select(
      DB::raw("(SELECT SUM(PL.quantity - PL.quantity_sold - PL.quantity_adjusted - PL.quantity_returned) FROM transactions JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id WHERE transactions.status='received' AND transactions.type IN ('purchase', 'opening_stock') {$location_filter} AND PL.variantion_id=variantions.id) as total_purchased"),

      DB::raw("(SELECT SUM(TSL.quantity - TSL.quantity_returned) FROM transactions JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id WHERE transactions.status='final' AND transactions.type IN ('sell', 'leasing') {$location_filter} {$agent_filter} AND TSL.variantion_id=variantions.id) as total_sold"),

      DB::raw("(SELECT SUM(IF(transactions.type='purchase_transfer', PL.quantity, 0) ) FROM transactions JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id WHERE transactions.status='received' AND transactions.type='purchase_transfer' {$location_filter} AND (PL.variantion_id=variantions.id)) as total_transfered_in"),

      DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id WHERE transactions.status='final' AND transactions.type IN ('sell_transfer') {$location_filter} AND (TSL.variantion_id=variantions.id)) as total_transfered_out"),

      DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id WHERE transactions.status='received' AND transactions.type='stock_adjustment' {$location_filter}  AND (SAL.variantion_id=variantions.id)) as total_adjusted"),

      DB::raw("SUM(vld.qty_available) as stock"),
      'p.code as sku',
      'p.name as product',
      'p.type',
      'p.id as product_id',
      'units.short_name as unit',
      'p.enable_stock as enable_stock',
      'variantions.default_sell_price as unit_price',
      'variantions.name as variantion_name',
      'variantions.sub_sku as variantion_sku'
    )->groupBy('variantions.id')->orderBy('p.id', 'desc');

    $filterByBranch = false;
    $report = new \stdClass;
    $report->total_adjusted     = $products->get()->sum('total_adjusted');
    $report->total_purchase     = $products->get()->sum('total_purchased');
    $report->total_sale         = $products->get()->sum('total_sold');
    if (!empty($request->branch)) {
      $filterByBranch = true;
      $report->total_stock        = $products->get()->map(function($item) {
        if($item->stock < 0){
          return 0;
        }
        return $item->stock;
      })->sum();

      $report->total_stock_amount = $products->get()->map(function($item) {
        if($item->stock < 0){
          return 0;
        }
        return abs($item->stock) * $item->unit_price;
      })->sum();

      $report->total_stock_oversale        = $products->get()->map(function($item) {
        if($item->stock > 0){
          return 0;
        }
        return $item->stock;
      })->sum();
      $report->total_stock_amount_oversale = $products->get()->map(function($item) {
        if($item->stock > 0){
          return 0;
        }
        return abs($item->stock) * $item->unit_price;
      })->sum();
    }else {
      $all_location = VariantionLocationDetails::join('variantions', 'variantions.id', '=', 'variantion_location_details.variantion_id')
      ->select(
        'variantion_location_details.location_id',
        'variantion_location_details.product_id',
        'variantion_location_details.variantion_id',
        'variantion_location_details.qty_available as stock',
        'variantions.default_sell_price as unit_price'
      );
      $report->total_stock        = $all_location->get()->map(function($item) {
        if($item->stock < 0){
          return 0;
        }
        return $item->stock;
      })->sum();
      $report->total_stock_amount = $all_location->get()->map(function($item) {
        if($item->stock < 0){
          return 0;
        }
        return abs($item->stock) * $item->unit_price;
      })->sum();

      $report->total_stock_oversale        = $all_location->get()->map(function($item) {
        if($item->stock > 0){
          return 0;
        }
        return $item->stock;
      })->sum();
      $report->total_stock_amount_oversale = $all_location->get()->map(function($item) {
        if($item->stock > 0){
          return 0;
        }
        return abs($item->stock) * $item->unit_price;
      })->sum();
    }

    $loans = $products->paginate(paginationCount());
    $offset = offset($request->page);

    $branches = Branch::getAll();
    $agents = Staff::getAll();
    $query = $request->all();
    $selectedBranch = !empty($request->branch) ? Branch::find($request->branch)->location : trans('app.all_branches');

    // dd($report);

    return view('report/product-stock', compact(
      'loans',
      'offset',
      'query',
      'branches',
      'agents',
      'startDate',
      'endDate',
      'selectedBranch',
      'report',
      'products',
      'filterByBranch'
    ));
  }

  /**
   * Display product sell.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function productSell(Request $request)
  {
    if(!Auth::user()->can('report.product-sell')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $types = ['sell', 'leasing'];
    $result = TransactionSellLine::whereHas('transaction', function($query) use($request) {
      $query->whereIn('type', (!empty($request->type) ? [$request->type] : ['sell']));

      if (!empty($request->start_date) && !empty($request->end_date)) {
        $query->whereBetween('transaction_date', [dateIsoFormat($request->start_date), dateIsoFormat($request->end_date)]);
      }
      if(!empty($request->branch)) {
        $query->where('location_id', $request->branch);
      }
    });

    if(!empty($request->q)) {
      $result->whereHas('product', function($product) use ($request) {
        $product->Where('name', 'LIKE', "%{$request->q}%")->orWhere('code', 'LIKE', "%{$request->q}%");
      });
    }

    $results = $result->get();
    $totalProduct = $results->sum('quantity');
    $totalProductPrice = $results->map(function($item) {
      return $item->unit_price * $item->quantity;
    })->sum();
    $branches = Branch::getAll();
    $itemCount = $results->count();
    $loans = $result->latest()->paginate(paginationCount());
    $offset = offset($request->page);
    $query = $request->all();

    return view('report/product-sell', compact('loans', 'itemCount', 'offset', 'query', 'branches', 'types', 'totalProduct', 'totalProductPrice'));
  }

  public function productPurchase(Request $request)
  {
    if(!Auth::user()->can('report.product-purchase')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $result = PurchaseLine::whereHas('transaction', function($query) use($request) {
      $query->where('type', 'purchase');

      if (!empty($request->start_date) && !empty($request->end_date)) {
        $query->whereBetween('transaction_date', [dateIsoFormat($request->start_date), dateIsoFormat($request->end_date)]);
      }

      if(!empty($request->branch)) {
        $query->where('location_id', $request->branch);
      }
    });

    $results = $result->get();
    $totalProduct = $results->sum('quantity');
    $totalProductPrice = $results->map(function($item) {
      return $item->purchase_price * $item->quantity;
    })->sum();
    $types = ['loan', 'sell'];
    $branches = Branch::getAll();
    $itemCount = $results->count();
    $loans = $result->latest()->paginate(paginationCount());
    $offset = offset($request->page);
    $query = $request->all();

    return view('report/product-purchase', compact('loans', 'itemCount', 'offset', 'query', 'branches', 'types', 'totalProductPrice', 'totalProduct'));
  }

  public function sell(Request $request)
  {
    if(!Auth::user()->can('report.sell')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $startDate = !empty($request->start_date) ? Carbon::createFromFormat('d-m-Y', $request->start_date)->startOfDay() : Carbon::now()->startOfDay();
    $startDate = $startDate->toDateTimeString();
    $endDate = !empty($request->end_date) ? Carbon::createFromFormat('d-m-Y', $request->end_date)->endOfDay() : Carbon::now()->endOfDay();
    $endDate = $endDate->toDateTimeString();

    $result = Transaction::where('type', 'sell')->where('transaction_date', '>=', $startDate)->where('transaction_date', '<=', $endDate);

    if(!empty($request->branch)) {
      $result->where('location_id', $request->branch);
    }

    if(!empty($request->client)) {
      $result->where('contact_id', $request->client);
    }

    if(!empty($request->status)) {
      $result->where('status', $request->status);
    }

    if(!empty($request->payment_status)) {
      $result->where('payment_status', $request->payment_status);
    }

    if(!empty($request->q)) {
      $result->whereHas('sell_lines.product', function($query) use($request) {
        $query->where('name', 'LIKE', "%{$request->q}%")->orWhere('code', 'LIKE', "%{$request->q}%");
      });
    }

    // summery
    $results = $result->get()->map(function($item) {
      $paid_amount = $item->invoices->sum('payment_amount');
      return [
        'products' => $item->sell_lines->sum('quantity'),
        'total_amount' => $item->final_total,
        'paid_amount' => $paid_amount,
        'due_amount' => ($item->final_total - $paid_amount)
      ];
    });
    $summeries = new \stdClass();
    $summeries->items = $results->sum('products');
    $summeries->total_amount = $results->sum('total_amount');
    $summeries->paid_amount = $results->sum('paid_amount');
    $summeries->due_amount = $results->sum('due_amount');

    $branches = Branch::getAll();
    $clients = Contact::whereIn('type', [ContactType::CUSTOMER, ContactType::BOTH])->orderBy('is_default', 'desc')->orderBy('name')->get();
    $loans = $result->latest()->paginate(paginationCount());
    $offset = offset($request->page);
    $query = $request->all();
    $selectedBranch = !empty($request->branch) ? Branch::find($request->branch)->location : trans('app.all_branches');

    return view('report.sell', compact(
      'loans',
      'offset',
      'query',
      'branches',
      'clients',
      'startDate',
      'endDate',
      'selectedBranch',
      'summeries'
    ));
  }

  public function purchase(Request $request)
  {
    if(!Auth::user()->can('report.purchase')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $startDate = !empty($request->start_date) ? Carbon::createFromFormat('d-m-Y', $request->start_date) : Carbon::now()->startOfDay();
    $startDate = $startDate->toDateTimeString();
    $endDate = !empty($request->end_date) ? Carbon::createFromFormat('d-m-Y', $request->end_date) : Carbon::now()->endOfDay();
    $endDate = $endDate->toDateTimeString();

    $result = Transaction::where('type', 'purchase')->where('transaction_date', '>=', $startDate)->where('transaction_date', '<=', $endDate);

    if(!empty($request->branch)) {
      $result->where('location_id', $request->branch);
    }

    if(!empty($request->status)) {
      $result->where('status', $request->status);
    }
    if(!empty($request->group)) {
      $result->where('contact_group_id', $request->group);
    }
    // summery
    $results = $result->get()->map(function($item) {
      $paid_amount = $item->invoices->sum('payment_amount');
      return [
        'products' => $item->purchase_lines->sum('quantity'),
        'total_amount' => $item->final_total,
        'paid_amount' => $paid_amount,
        'due_amount' => ($item->final_total - $paid_amount)
      ];
    });
    $summeries = new \stdClass();
    $summeries->items = $results->sum('products');
    $summeries->total_amount = $results->sum('total_amount');
    $summeries->paid_amount = $results->sum('paid_amount');
    $summeries->due_amount = $results->sum('due_amount');

    $branches = Branch::getAll();
    $loans = $result->latest()->paginate(paginationCount());
    $offset = offset($request->page);
    $query = $request->all();
    $selectedBranch = !empty($request->branch) ? Branch::find($request->branch)->location : trans('app.all_branches');
    $groups = ContactGroup::get();
    return view('report.purchase', compact(
      'loans',
      'offset',
      'query',
      'branches',
      'startDate',
      'endDate',
      'groups',
      'selectedBranch',
      'summeries'));
  }

  public function purchaseSaleReport(Request $request)
  {
    if(!Auth::user()->can('report.purchase-sale')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }

    $startDate = !empty($request->start_date) ? Carbon::createFromFormat('d-m-Y', $request->start_date) : Carbon::now()->startOfDay();
    $startDate = $startDate->toDateTimeString();
    $endDate = !empty($request->end_date) ? Carbon::createFromFormat('d-m-Y', $request->end_date) : Carbon::now()->endOfDay();
    $endDate = $endDate->toDateTimeString();
    $branches = Branch::getAll();
    $selectedBranch = !empty($request->branch) ? Branch::find($request->branch)->location : trans('app.all_branches');

    $purchase = Transaction::with(['invoices'])->whereIn('type', ['purchase'])->where('transaction_date', '>=', $startDate)->where('transaction_date', '<=', $endDate);
    $sale = Transaction::with(['invoices'])->whereIn('type', ['leasing', 'sell'])->where('transaction_date', '>=', $startDate)->where('transaction_date', '<=', $endDate);

    if(!empty($request->branch)) {
      $purchase->where('location_id', $request->branch);
      $sale->where('location_id', $request->branch);
    }

    $purchase = $purchase->get();
    $sale = $sale->get();

    $report = new \stdClass;
    $report->total_purchase = $purchase->sum('final_total');
    $report->total_due_purchase = $report->total_purchase - $purchase->map(function($item) {
      return $item->invoices->sum('payment_amount');
    })->sum();
    $report->total_sale = $sale->sum('final_total');
    $report->total_due_sale = $report->total_sale - $sale->map(function($item) {
      return $item->invoices->sum('payment_amount');
    })->sum();
    $report->summary = $report->total_sale - $report->total_purchase;
    // dd($report);

    return view('report.purchase_sale', compact(
      'report',
      'branches',
      'startDate',
      'endDate',
      'selectedBranch'
    ));
  }
  // Cash Recieved
  public function cashRecieved(Request $request){
    if(!Auth::user()->can('report.cash-recieved')) {
      return back()->with(['message'=>trans('message.no_permission')], 403);
    }
    $query = Invoice::with('transaction','loan');
    $startDate = !empty($request->start_date) ? Carbon::createFromFormat('d-m-Y', $request->start_date) : Carbon::now()->startOfDay();
    $startDate = $startDate->format('Y-m-d');
    $endDate = !empty($request->end_date) ? Carbon::createFromFormat('d-m-Y', $request->end_date) : Carbon::now()->endOfDay();
    $endDate = $endDate->format('Y-m-d');
    // dd($startDate);
    $total_invoice = Invoice::whereIn('type',['sell','leasing','leasing-dp','cop'])->whereBetween('payment_date', [$startDate." 00:00:00",$endDate.' 23:59:59']);
    $results = $query->whereIn('invoices.type',['sell','leasing','leasing-dp','cop'])->where('invoices.total','>',0)->whereBetween('payment_date', [$startDate." 00:00:00",$endDate.' 23:59:59']);
    if(!empty($request->branch)) {
      $location_id =  $request->branch;
      $query->whereHas('transaction', function ($query) use ($location_id) {
        $query->where('location_id', $location_id);
      });
      $total_invoice->whereHas('transaction', function($query) use ($location_id){
        $query->where('location_id',$location_id);
      });
     
    }
    if(!empty($request->type)) {
      $total_invoice->where('type', $request->type);
      $results->where('type', $request->type);
    }

    $summeries = new \stdClass();
    foreach(paymentMethods() as $pk => $pv){
      $summeries->$pk = $total_invoice->get()->where('payment_method',$pk)->sum('total');
    }
   
    $itemCount = $results->count();
    $invoices =  $results->orderBy('payment_date')->paginate(paginationCount());
    $offset = offset($request->page);
    $branches = Branch::getAll();
    $agents = Staff::getAll();
    return view('report.cash-recieved',compact('invoices','offset','itemCount','summeries','branches','agents','startDate','endDate'));
  }

}
