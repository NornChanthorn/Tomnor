<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Constants\FormType;
use App\Constants\Frequency;
use App\Constants\LoanStatus;
use App\Constants\PaymentScheduleType;
use App\Constants\Message;
use App\Constants\StockTransaction;
use App\Constants\StockType;
use App\Models\Depreciation;
use App\Http\Requests\LoanRequest;
use App\Models\AgentCommission;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Loan;
use App\DownPayment;
use App\Models\LoanProductDetail;
use App\Models\Product;
use App\Models\Schedule;
use App\Models\ScheduleReference;
use App\Models\ScheduleHistory;
use App\Models\Staff;

// use App\Models\ProductWarehouse;
// use App\Models\StockHistory;
use App\Models\Variantion;
use App\Models\Transaction;
use App\Models\GeneralSetting;

use App\Traits\TransactionUtil;
use App\Traits\ProductUtil;

use DB;
use Auth;
use \Carbon\Carbon;

class LoanController extends Controller
{

  use TransactionUtil, ProductUtil;

  public function __construct()
  {
    //$this->middleware('role:' . UserRole::ADMIN)->only('edit');
  }

  /**
   * Display a list of loans;
   *
   * @param Request $request
   *
   * @return Response
   */
  public function index(Request $request)
  {
    if(!Auth::user()->can('loan.browse')) {
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }
    $date = dateIsoFormat($request->date) ?? "";
    $sdate= dateIsoFormat($request->date);
    $agents = [];
    $loans = Loan::where('type','product')->where('status', '!=', LoanStatus::REJECTED)
    ->with(['schedules' => function($query) {
      $query->where('paid_status', 0)->orderBy('payment_date');
    }]);

    if (isAdmin() || empty(auth()->user()->staff)) {
      if (!empty($request->branch)) {
        $loans = $loans->where('branch_id', $request->branch);
        $agents = Staff::where('branch_id', $request->branch)->orderBy('name')->get();
      }

      if (!empty($request->agent)) {
        $loans = $loans->where('staff_id', $request->agent);
      }
    }
    else {
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
        // ->orWhereHas('productDetails', function ($query) use ($searchText) {
        //   $query->orWhereHas('product', function ($query) use ($searchText) {
        //     $query->where('name', 'like', '%' . $searchText . '%');
        //   });
        // });
      });
    }
    $itemCount = $loans->count();
    $loans = $loans->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);

    return view('loan/index', compact('date','agents', 'itemCount', 'loans', 'offset'));
  }

  /**
   * Display form to create loan.
   *
   * @param Loan $loan
   *
   * @return Response
   */
  public function create(Request $request, Loan $loan)
  {
    if(!Auth::user()->can('loan.add')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $now = Carbon::now();
    $count = Loan::whereBetween('created_at', [$now->startOfDay()->toDateTimeString(), $now->endOfDay()->toDateTimeString()])->count();
    $setting = GeneralSetting::first();

    $loan->account_number = nextLoanAccNum();
    $loan->count = ($count+1);
    $loan->register_id = generateRegisterId('', $loan->count);
    $title = trans('app.create');
    $formType = FormType::CREATE_TYPE;
    $branches = Branch::getAll();
    $clients = Client::orderBy('name')->get();
    // $products = Product::orderBy('name')->get();
    $agents = [];
    if (isAdmin() && old('branch') !== null) {
      // When form validation has error
      $agents = Staff::where('branch_id', old('branch'))->orderBy('name')->get();
    }

    $products = Product::join('variantions', 'products.id', '=', 'variantions.product_id')
        ->where('active', 1)
        ->whereNull('variantions.deleted_at')
        ->leftJoin('variantion_location_details', function($join) {
        $join->on('variantions.id', '=', 'variantion_location_details.variantion_id');
    })
      ->where('variantion_location_details.qty_available', '>', 0)
      ->select(
            'products.id as product_id',
            'products.name',
            'products.type',
            'products.enable_stock',
            'products.price',
            'variantions.id as variantion_id',
            'variantions.name as variantion',
            'variantion_location_details.qty_available',
            'variantions.default_sell_price as selling_price',
            'variantions.sub_sku',
            'products.unit'
       );
    $items = [];
    foreach($products->get() as $product) {
      $product->text = $product->name . ($product->variantion=='DUMMY' ? ' - '.$product->variantion : '');
      $product->selling_price = !empty($product->selling_price) ? $product->selling_price : $product->price;
      $items[$product->variantion_id] = $product;
    }

    return view('loan/form', compact(
      'agents',
      'branches',
      'clients',
      'formType',
      'loan',
      'products',
      'items',
      'title',
      'setting'
    ));
  }
  public function calculateloan(Request $request, Loan $loan)
  {
    if(!Auth::user()->can('loan.add')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $now = Carbon::now();
    $count = Loan::whereBetween('created_at', [$now->startOfDay()->toDateTimeString(), $now->endOfDay()->toDateTimeString()])->count();
    $setting = GeneralSetting::first();

    $loan->account_number = nextLoanAccNum();
    $loan->count = ($count+1);
    $loan->register_id = generateRegisterId('', $loan->count);
    $title = trans('app.create');
    $formType = FormType::CREATE_TYPE;
    $branches = Branch::getAll();
    $clients = Client::orderBy('name')->get();
    // $products = Product::orderBy('name')->get();
    $agents = [];
    if (isAdmin() && old('branch') !== null) {
      // When form validation has error
      $agents = Staff::where('branch_id', old('branch'))->orderBy('name')->get();
    }

    $products = Product::join('variantions', 'products.id', '=', 'variantions.product_id')
    ->where('active', 1)
    ->whereNull('variantions.deleted_at')
    ->leftJoin('variantion_location_details', function($join) {
      $join->on('variantions.id', '=', 'variantion_location_details.variantion_id');
    })
    ->where('variantion_location_details.qty_available', '>', 0)
    ->select(
      'products.id as product_id',
      'products.name',
      'products.type',
      'products.enable_stock',
      'products.price',
      'variantions.id as variantion_id',
      'variantions.name as variantion',
      'variantion_location_details.qty_available',
      'variantions.default_sell_price as selling_price',
      'variantions.sub_sku',
      'products.unit'
    );
    $items = [];
    foreach($products->get() as $product) {
      $product->text = $product->name . ($product->variantion=='DUMMY' ? ' - '.$product->variantion : '');
      $product->selling_price = !empty($product->selling_price) ? $product->selling_price : $product->price;
      $items[$product->variantion_id] = $product;
    }

    return view('loan/calculator', compact(
      'agents',
      'branches',
      'clients',
      'formType',
      'loan',
      'products',
      'items',
      'title',
      'setting'
    ));
  }
  /**
   * Display form to edit loan.
   *
   * @param Loan $loan
   *
   * @return Response
   */
  public function edit(Loan $loan)
  {
    if(!Auth::user()->can('loan.edit')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $setting = GeneralSetting::first();
    if (isPaidLoan($loan->id)) {
      return back();
    }

    $loan->count = Loan::count()+1;

    $title = trans('app.edit');
    $formType = FormType::EDIT_TYPE;
    $branches = Branch::getAll();
    $clients = Client::orderBy('name')->get();
    $products = Product::orderBy('name')->get();
    $agents = [];

    // if (isAdmin()) {
    $branchId = old('branch') ?? $loan->branch_id;
    $agents = Staff::where('branch_id', $branchId)->orderBy('name')->get();
    // }

    return view('loan/form', compact(
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
   * Display loan detail.
   *
   * @param Loan $loan
   *
   * @return Response
   */
  public function show(Loan $loan)
  {
    if(!Auth::user()->can('loan.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    $setting = GeneralSetting::first();
    if ($loan->status == LoanStatus::REJECTED) {
      return redirect(route('loan.index'));
    }

    $title = trans('app.detail');
    $formType = FormType::SHOW_TYPE;
    $loan->count = 1;
    $loanId = $loan->id;

    $depreciation = Depreciation::where('loan_id', $loanId)->first();
    return view('loan.show', compact(
      'formType',
      'loan',
      'loanId',
      'title',
      'depreciation',
      'setting'
    ));
  }







  /**
   * Save new or existing loan.
   *
   * @param LoanRequest $request
   * @param Loan $loan
   *
   * @return Response
   */
  public function save(LoanRequest $request, Loan $loan)
  {
    if(!Auth::user()->can('loan.edit') && !Auth::user()->can('loan.add')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $request->request->set('wing_code', str_replace(' ', '', $request->wing_code));
    $validationRules = [
      'form_type' => ['required', Rule::in([FormType::CREATE_TYPE, FormType::EDIT_TYPE])],
      // applegold don't use wing account
      //'wing_code' => ['required', 'numeric', 'digits:8', Rule::unique('loans')->ignore($loan->id)],
      // 'client_code' => ['required', Rule::unique('loans')->ignore($loan->id)],
      'client' => ['required', 'integer'],
      'product_price' => 'nullable|numeric',
      'loan_amount' => 'required|numeric',
      'depreciation_amount' => 'required|numeric',
    ];


    if (isAdmin() || empty(auth()->user()->staff)) {
      $validationRules = array_merge($validationRules, [
        'branch' => 'required|integer',
        'agent' => 'required|integer',
      ]);
    }
    $this->validate($request, $validationRules);

    // If loan has payment data, not allow to update
    if ($loan->id !== null && isPaidLoan($loan->id)) {
      return back();
    }

    // $products = collect($request->products);
    // $variantion = Variantion::findOrFail($products->first()['variantion_id']);
    // if(empty($variantion)) {
    //   return back();
    // }

    $variantionIds = collect($request->products)->pluck('variantion_id')->unique()->toArray();
    $productCount = Variantion::whereIn('id', $variantionIds)->count();

    // Check if IDs of sale product (s) are invalid
    if (count($variantionIds)==0 || ($productCount != count($variantionIds))) {
      session()->flash(Message::SAVE_FAILURE_VALUE, trans('message.invalid_product_data'));
      return back()->withInput()->withErrors([
        Message::ERROR_KEY => trans('message.invalid_product_data'),
      ]);
    }

    // If the client has an active or pending loan, not allow to create new loan
    // if (!$request->allow_multi_loan) {
    //   $currentLoan = Loan::whereIn('status', [LoanStatus::PENDING, LoanStatus::ACTIVE])
    //   ->where('client_id', $request->client)->where('id', '!=', $loan->id)->first();
    //   if (!empty($currentLoan)) {
    //     session()->flash(Message::ERROR_KEY, trans('message.loan_disallowed_cos_of_client') . '<a href="' . route('loan.show', $currentLoan->id) . '">' . $currentLoan->account_number . '</a>');
    //     return back()->withInput($request->all());
    //   }
    // }

    if (isAdmin() || empty(auth()->user()->staff)) {
      $loan->branch_id = $request->branch;
      // dd($request->agent);
      $loan->staff_id = Staff::where('user_id',$request->agent)->first()->id;

    }
    else { // Auto-set branch and agent when staff creates loan
      $staff = auth()->user()->staff;
      $loan->branch_id = $staff->branch->id;
      $loan->staff_id = $staff->id;
    }

    $loan->schedule_type        = $request->schedule_type;
    $loan->loan_amount          = $request->loan_amount;
    $loan->product_price        = $request->product_price;
    $loan->depreciation_amount  = $request->depreciation_amount;
    // $loan->depreciation_percentage  = $request->depreciation_percentage;
    $loan->down_payment_amount  = $request->down_payment_amount;
    $loan->payment_method       = $request->payment_method;
    $loan->interest_rate        = $request->interest_rate;
    $loan->installment          = $request->installment;
    $loan->payment_per_month    = $request->payment_per_month;
    $loan->loan_start_date      = dateIsoFormat($request->loan_start_date);
    $loan->first_payment_date   = dateIsoFormat($request->first_payment_date);
    $loan->note                 = $request->note;

    // Calculate commission amount for agent
    // $startDates = AgentCommission::select('start_date')->where('staff_id', $request->agent)->get();
    $agentCommissions = AgentCommission::where('staff_id', $request->agent)->orderBy('start_date', 'desc')->get();
    if (count($agentCommissions) > 0) {
      $commissionAmount = 0;
    }
    else {
      $commissionAmount = 0;
    }
    $loan->commission_amount = $commissionAmount;

    // Generate loan code and set creator when create new loan
    if ($request->form_type == FormType::CREATE_TYPE) {
      $loan->account_number = nextLoanAccNum();
      $loan->user_id = auth()->user()->id;
      $loan->status = LoanStatus::PENDING;
    }

    // applegold don't use wing code
    $loan->wing_code = 'N/A'; //$request->wing_code;
    $loan->client_code = $request->client_code;
    $loan->client_id = $request->client;

    DB::beginTransaction();

    if ($loan->save()) {
      // Delete old schedules and product line when edit loan
      if ($request->form_type == FormType::EDIT_TYPE) {
        Schedule::where('loan_id', $loan->id)->delete();
        LoanProductDetail::where('loan_id', $loan->id)->delete();
      }

      $paymentSchedules = $this->calcPaymentSchedule($request);
      foreach ($paymentSchedules as $paymentSchedule) {
        $schedule = new Schedule();
        $schedule->loan_id = $loan->id;
        $schedule->payment_date = $paymentSchedule['payment_date'];
        $schedule->principal = $paymentSchedule['principal'];
        $schedule->interest = $paymentSchedule['interest'];
        $schedule->total = $paymentSchedule['principal'] + $paymentSchedule['interest'];
        $schedule->outstanding = $paymentSchedule['outstanding'];
        $schedule->save();
      }
      // dd($request->products);
      $products = [];
      foreach ($request->products as $product) {
        $line = [
          'loan_id'                     => $loan->id,
          'product_id'                  => $product['id'],
          'variantion_id'               => $product['variantion_id'],
          'qty'                         => $product['quantity'],
          'unit_price'                  => $product['price'],
          // 'depreciation_percentage'     => $product['depreciation_percentage'],
          // 'product_ime'                 => $product['sell_line_note']
        ];
        $products[] = new LoanProductDetail($line);
      }

      if (!empty($products)) {
        $loan->productDetails()->saveMany($products);
      }

    }
    DB::commit();

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return redirect(route('loan.show', $loan->id));
  }

  /**
   * Get loan payment schedule from AJAX request.
   *
   * @param LoanRequest $request
   *
   * @return Response
   */
//   public function getPaymentSchedule(Request $request)
//   {
//     // if (!$request->ajax()) {
//     //   return back();
//     // }

//     $paymentSchedules = $this->calcPaymentSchedule($request, true);
//     return response()->json($paymentSchedules);
//   }
  public function getPaymentSchedule(LoanRequest $request)
  {
    if (!$request->ajax()) {
      return back();
    }

    $paymentSchedules = $this->calcPaymentSchedule($request, true);
    return response()->json($paymentSchedules);
  }

  /**
   * Calculate loan payment schedule as flat or decline interest.
   *
   * @param LoanRequest $request
   * @param bool $displayMode
   *
   * @return array
   */
//   private function calcPaymentSchedule(Request $request, $displayMode = false)
//   {
//     $loanStartDate = dateIsoFormat($request->loan_start_date);
//     // If first payment date is empty, increase it one month from loan start date
//     $firstPaymentDate = dateIsoFormat($request->first_payment_date) ?? oneMonthIncrement($loanStartDate);
//     $paymentDay = dateIsoFormat(($request->first_payment_date ?? $loanStartDate), 'd');
//     $paymentDate = $firstPaymentDate;

//     $scheduleType = $request->schedule_type;
//     $isEqualSchedule = ($scheduleType == PaymentScheduleType::EQUAL_PAYMENT);
//     $isDeclineSchedule = ($scheduleType == PaymentScheduleType::DECLINE_INTEREST);
//     $installment = $request->installment;
//     $downPaymentAmount = $outstandingAmount = $request->down_payment_amount;
//     $principal = ($downPaymentAmount / $installment);
//     $firstPayDuration = date_diff(date_create($loanStartDate), date_create($firstPaymentDate))->format('%a');

//     if ($isEqualSchedule) {
//       if ($request->interest_rate > 0) {
//         $loanRate = (($downPaymentAmount * $request->interest_rate) / 100)/30;
//         $totalAmount = pmt($loanRate, $installment, $downPaymentAmount);
//       }
//       else {
//         $interest = 0;
//         $principal = $totalAmount = decimalNumber($principal);
//       }
//     }
//     elseif ($isDeclineSchedule) {
//       $interestRate = $request->interest_rate / 100;
//       $interest = $downPaymentAmount * $interestRate;
//       // Calculate first interest amount of payment schedule
//       $firstPayDuration = date_diff(date_create($loanStartDate), date_create($firstPaymentDate))->format('%a');
//       $firstInterest = ($interest / 30) * $firstPayDuration;
//     }

//     $loopCount = ($scheduleType == PaymentScheduleType::FLAT_INTEREST ? ($installment + 1) : $installment); // For flat interest, plus one installment
//     $scheduleData = [];

//     for ($i = 1; $i <= $loopCount; $i++) {
//       $isFirstLoop = ($i == 1);
//       $isForeLastLoop = ($i == ($loopCount - 1));
//       $paymentDate = ($isFirstLoop ? $paymentDate : oneMonthIncrement($paymentDate, $paymentDay));

//       if ($isEqualSchedule) {
//         if ($request->interest_rate > 0) {
//           if($i==1){
//             $interest = $loanRate *  $firstPayDuration;
//             $principal = $principal;
//           }else{
//             $interest = $loanRate *  30;
//             $principal = $principal;
//           }

//         }else{
//             $interest = 0;
//             $principal;
//         }
//         $outstandingAmount = ($i == $loopCount ? 0 : ($outstandingAmount - $principal));
//       }
//       elseif ($isDeclineSchedule) {
//         $interest = ($isFirstLoop ? $firstInterest : ($outstandingAmount * $interestRate));
//         $totalAmount = ($principal + ($isFirstLoop ? $firstInterest : $interest));
//         $outstandingAmount = ($isForeLastLoop ? $principal : ($outstandingAmount - $principal));
//       }
//       else {
//         $interest = 0;
//         $totalAmount = $principal *  $firstPayDuration;;
//         $outstandingAmount = ($isForeLastLoop ? 0 : ($outstandingAmount - $principal));
//       }

//       $scheduleData[] = [
//         'payment_date' => ($displayMode ? displayDate($paymentDate) : $paymentDate),
//         'principal' => ($isEqualSchedule ? number_format($principal, 2) : decimalNumber($principal, $displayMode)),
//         'interest' => ($isEqualSchedule ? number_format($interest, 2) : decimalNumber($interest, $displayMode)),
//         'total' => ($isEqualSchedule ? number_format($principal, 2) : decimalNumber($principal, $displayMode)) + ($isEqualSchedule ? number_format($interest, 2) : decimalNumber($interest, $displayMode)),
//         'outstanding' => decimalNumber($outstandingAmount, $displayMode),
//       ];
//     }

//     return $scheduleData;
//   }
    private function calcPaymentSchedule(LoanRequest $request, $displayMode = false)
    {
      $loanStartDate = dateIsoFormat($request->loan_start_date);
      // If first payment date is empty, increase it one month from loan start date
      $firstPaymentDate = dateIsoFormat($request->first_payment_date) ?? oneMonthIncrement($loanStartDate);
      $paymentDay = dateIsoFormat(($request->first_payment_date ?? $loanStartDate), 'd');
      $paymentDate = $firstPaymentDate;

      $scheduleType = $request->schedule_type;
      $isEqualSchedule = ($scheduleType == PaymentScheduleType::AMORTIZATION);
      $isDeclineSchedule = ($scheduleType == PaymentScheduleType::DECLINE_INTEREST);
      $installment = $request->installment;
      $downPaymentAmount = $outstandingAmount = $request->down_payment_amount;
      $principal = ($downPaymentAmount / $installment);

      if ($isEqualSchedule) {
          $loanRate = ($request->interest_rate / 12) / 100;
          $amortizationSchedule = [];
          $remainingBalance = $downPaymentAmount;

          for ($i = 1; $i <= $installment; $i++) {
              $interest = $remainingBalance * $loanRate;
              $totalAmount = pmt($loanRate, $installment, $downPaymentAmount);
              $principal = $totalAmount - $interest;
              $amortizationSchedule[] = [
                  'payment_date' => ($displayMode ? displayDate($paymentDate) : $paymentDate),
                  'principal' => decimalNumber($principal, $displayMode),
                  'interest' => decimalNumber($interest, $displayMode),
                  'total' => decimalNumber($totalAmount, $displayMode),
                  'outstanding' => decimalNumber($remainingBalance - $principal, $displayMode),
              ];

              $remainingBalance -= $principal;
              $paymentDate = oneMonthIncrement($paymentDate, $paymentDay);
          }
          return $amortizationSchedule;

      }
      elseif ($isDeclineSchedule) {
        $interestRate = $request->interest_rate / 100;
        $interest = $downPaymentAmount * $interestRate;

        // Calculate first interest amount of payment schedule
        $firstPayDuration = date_diff(date_create($loanStartDate), date_create($firstPaymentDate))->format('%a');
        $firstInterest = ($interest / 30) * $firstPayDuration;
      }

      $loopCount = ($scheduleType == PaymentScheduleType::FLAT_INTEREST ? ($installment + 1) : $installment); // For flat interest, plus one installment
      $scheduleData = [];

      for ($i = 1; $i <= $loopCount; $i++) {
        $isFirstLoop = ($i == 1);
        $isForeLastLoop = ($i == ($loopCount - 1));
        $paymentDate = ($isFirstLoop ? $paymentDate : oneMonthIncrement($paymentDate, $paymentDay));

        if ($isEqualSchedule) {
          if ($request->interest_rate > 0) {
            $interest = round($loanRate * $outstandingAmount);
            $principal = ($totalAmount - $interest);
          }
          $outstandingAmount = ($i == $loopCount ? 0 : ($outstandingAmount - $principal));
        }
        elseif ($isDeclineSchedule) {
          $interest = ($isFirstLoop ? $firstInterest : ($outstandingAmount * $interestRate));
          $totalAmount = ($principal + ($isFirstLoop ? $firstInterest : $interest));
          $outstandingAmount = ($isForeLastLoop ? $principal : ($outstandingAmount - $principal));
        }
        else {
          $interest = 0;
          $totalAmount = $principal;
          $outstandingAmount = ($isForeLastLoop ? 0 : ($outstandingAmount - $principal));
        }

        $scheduleData[] = [
          'payment_date' => ($displayMode ? displayDate($paymentDate) : $paymentDate),
          'principal' => ($isEqualSchedule ? $principal : decimalNumber($principal, $displayMode)),
          'interest' => ($isEqualSchedule ? $interest : decimalNumber($interest, $displayMode)),
          'total' => ($isEqualSchedule ? $totalAmount : decimalNumber($totalAmount, $displayMode)),
          'outstanding' => decimalNumber($outstandingAmount, $displayMode),
        ];
      }

      return $scheduleData;
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
    if(!Auth::user()->can('loan.edit')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    if (!$request->ajax() || !in_array($status, [LoanStatus::ACTIVE, LoanStatus::REJECTED, LoanStatus::PENDING])) {
      abort(404);
    }

    try {
      DB::beginTransaction();

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
        $transaction->payment_status   = 'paid';
        $transaction->others_charges   = $loan->branch->others_charges ?? 0;

        //Update reference count
        $ref_count = $this->setAndGetReferenceCount('sell');
        //Generate reference number
        if (empty($request->invoice_id)) {
          $transaction->invoice_no = $this->generateReferenceNumber('contacts', $ref_count, '', 6);
        }

        $transaction->save();
        // $products[$loan->product_id] = [
        //   'id'            => $loan->product_id,
        //   'name'          => $loan->product->name,
        //   'code'          => $loan->product->code,
        //   'variantion_id' => $loan->variantion_id,
        //   'enable_stock'  => $loan->product->enable_stock,
        //   'quantity'      => 1,
        //   'price'         => @$loan->variantion->default_sell_price ?? @$loan->product->price,
        //   'sub_total'     => $loan->loan_amount
        // ];
        $product_lines = [];
        foreach ($loan->productDetails as $item) {
          $products[$item->product_id] = [
            'id'            => $item->product_id,
            'name'          => $item->product->name,
            'code'          => $item->product->code,
            'variantion_id' => $item->variantion_id,
            'enable_stock'  => $item->product->enable_stock,
            'quantity'      => $item->qty,
            'price'         => $item->unit_price,
            'sub_total'     => ($item->qty*$item->unit_price)
          ];
        }
        $this->createOrUpdateSellLines($transaction, $products, $loan->branch_id);

        // $payment[] = [
        //   'amount'  => $request->paid_amount,
        //   'method'  => 'cash',
        //   'note'    => '',
        //   'paid_on' => Carbon::now()->toDateTimeString(),
        // ];
        // $this->createOrUpdatePaymentLines($transaction, $payment);

        //Check for final and do some processing.
        if ($transaction->status == 'final') {
          //update product stock
          foreach ($products as $product) {
            if ($product['enable_stock']) {
              $this->decreaseProductQuantity($product['id'], $product['variantion_id'], $loan->branch_id, $product['quantity']);
            }
          }
        }

        // update loan after disburse
        $loan->disbursed_date = Carbon::now()->toDateString();
        $loan->transaction_id = $transaction->id;
      }

      $loan->status = $status;
      $loan->changed_by = auth()->user()->id;

      if ($status == LoanStatus::ACTIVE) {
         $loan->approved_date = date('Y-m-d');
         Depreciation::create([
            'loan_id' => $loan->id,
            'c_id' => $loan->client_code,
            'paid_amount' => 0,
            'outstanding_amount' => $loan->depreciation_amount
       ]);
      }
      $loan->save();

      // Create Invoice

      if($transaction->status == 'final' && $status == LoanStatus::ACTIVE){
        $invoice = new Invoice();
        $invoice->type              = 'leasing-dp';
        $invoice->user_id           = auth()->user()->id;
        $invoice->loan_id           = $loan->id;
        $invoice->transaction_id    = $transaction->id;
        $invoice->client_id         = $loan->client->id;
        $invoice->payment_amount    = $loan->depreciation_amount;
        $invoice->total             = $loan->depreciation_amount;
        // $invoice->payment_method    = $loan->payment_method;
        $invoice->payment_date      = date('Y-m-d');
        $invoice->note              = $loan->note;

        $lastInvoiceNum = Invoice::latest()->first()->invoice_number ?? 0;
        $invoice->invoice_number = 'REF-' . str_pad(substr($lastInvoiceNum, 4) + 1, 6, 0, STR_PAD_LEFT);
        $invoice->save();
      }
      DB::commit();

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
    }
    catch(\Exception $e) {
      DB::rollBack();

      \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

      session()->flash(Message::SAVE_FAILURE_VALUE, trans('message.item_saved_fail'));
    }
  }

  /**
   * Disburse product.
   *
   * @param Loan $loan
   *
   * @return Response
   */
  public function disburse(Loan $loan)
  {
    if(!Auth::user()->can('loan.print')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

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
      $transaction->payment_status   = 'paid';
      $transaction->others_charges   = $loan->branch->others_charges ?? 0;

      //Update reference count
      $ref_count = $this->setAndGetReferenceCount('sell');
      //Generate reference number
      if (empty($request->invoice_id)) {
        $transaction->invoice_no = $this->generateReferenceNumber('contacts', $ref_count, '', 6);
      }

      DB::beginTransaction();

      $transaction->save();
      $products[$loan->product_id] = [
        'id'            => $loan->product_id,
        'name'          => $loan->product->name,
        'code'          => $loan->product->code,
        'variantion_id' => $loan->variantion_id,
        'enable_stock'  => $loan->product->enable_stock,
        'quantity'      => 1,
        'price'         => @$loan->variantion->default_sell_price ?? @$loan->product->price,
        'sub_total'     => $loan->loan_amount
      ];
      $this->createOrUpdateSellLines($transaction, $products, $loan->branch_id);

      // $payment[] = [
      //   'amount'  => $request->paid_amount,
      //   'method'  => 'cash',
      //   'note'    => '',
      //   'paid_on' => Carbon::now()->toDateTimeString(),
      // ];
      // $this->createOrUpdatePaymentLines($transaction, $payment);

      //Check for final and do some processing.
      if ($transaction->status == 'final') {
        //update product stock
        foreach ($products as $product) {
          if ($product['enable_stock']) {
            $this->decreaseProductQuantity($product['id'], $product['variantion_id'], $loan->branch_id, $product['quantity']);
          }
        }
      }

      // update loan after disburse
      $loan->disbursed_date = Carbon::now()->toDateString();
      $loan->transaction_id = $transaction->id;
      $loan->save();

      DB::commit();
    }
    else {
      session()->flash(Message::ERROR_KEY, trans('message.loan_already_disbursed'));
      return redirect()->back();
    }

    return redirect()->route('loan.invoice', $loan);
  }

  /**
   * Display loan invoice for printing.
   *
   * @param Loan $loan
   *
   * @return Response
   */
  public function invoice(Loan $loan)
  {
    if(!Auth::user()->can('loan.print')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    // $loan->depreciation_amount = $loan->depreciation_amount + $loan->branch->others_charges;
    $loan->sub_total = $loan->productDetails->map(function($item) {
      return $item->unit_price * $item->qty;
    })->sum() + $loan->branch->others_charges;
    $loan->balance = $loan->sub_total - $loan->depreciation_amount;
    // $loan->depreciation_percentage = ($loan->depreciation_amount/$loan->loan_amount) * 100;



    $sale = Transaction::where('id', $loan->transaction_id)->where('type', 'leasing')->first();
    $sale->final_total = $sale->sell_lines->map(function($item) {
      return $item->unit_price * $item->quantity;
    })->sum();
    // $sale->depreciation_amount = $sale->payment_lines->sum('amount') + $loan->branch->others_charges;
    // $sale->remaining_amount = ($sale->final_total - $sale->depreciation_amount) ?? 0;

    $invoice_head = $loan->branch;

    return view('loan.invoice', compact('loan', 'sale', 'invoice_head'));
  }

  /**
   * Display loan contract for printing.
   *
   * @param Loan $loan
   *
   * @return Response
   */
  public function printContract(Loan $loan)
  {
    if(!Auth::user()->can('loan.print')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    $loanId = $loan->id;
    $data = Depreciation::where('loan_id', $loanId)->first();

    return view('loan/contract', compact('data', 'loanId', 'loan'));
  }

  /**
   * Display payment schedule for printing.
   *
   * @param Loan $loan
   *
   * @return Response
   */
  public function printPaymentSchedule(Loan $loan)
  {
    if(!Auth::user()->can('loan.print')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    return view('partial/payment-schedule', compact('loan'));
  }
  public function editPaymentSchedule(Schedule $schedule)
  {
    if(!isAdmin() && !Auth::user()->can('loan.edit-schedule')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    $title = trans('app.edit');
    return view('loan.edit-schedule', compact('schedule','title'));
  }
  public function updatePaymentSchedule(Request $request, Schedule $schedule)
  {
    if(!isAdmin() && !Auth::user()->can('loan.edit-schedule')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    $schedule->paid_principal  = $request['paid_principal'];
    $schedule->paid_interest  = $request['paid_interest'];
    $schedule->paid_total  = $request['paid_total'];
    $schedule->paid_date  = $request['paid_date'] ? Carbon::parse($request['paid_date'])->toDateTimeString() : null;
    $schedule->paid_penalty  = $request['paid_penalty'];
    $schedule->paid_status  = $request['paid_status'];
    if($schedule->save()){
      return redirect()->route('repayment.show',[$schedule->loan_id,1]);
    }
    else{
      session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
      return back()->withInput($request->all());
    }
  }

  /**
   * Update note of a loan.
   *
   * @param Request $request
   * @param Loan $loan
   *
   * @return Response
   */
  public function updateNote(Request $request, Loan $loan)
  {
    $loan->note = $request->note;
    $loan->save();

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return back()->withInput($request->all());
  }

  /**
   * Delete loan.
   *
   * @param Request $request
   * @param Loan $loan
   *
   * @return void
   */
  public function destroy(Request $request, Loan $loan)
  {
    if(!Auth::user()->can('loan.delete')) {
      session()->flash(Message::SUCCESS_KEY, trans('message.no_permission'));
      // return back()->with([
      //   Message::ERROR_KEY => trans('message.no_permission'),
      //   'alert-type' => 'warning'
      // ], 403);
    }

    // if (!$request->ajax()) {
    //   abort(404);
    // }

    //Begin transaction
    DB::beginTransaction();

    if(!empty($loan->disbursed_date)) {
      $transaction = Transaction::where('id', $loan->transaction_id)->where('type', 'leasing')->with(['sell_lines'])->first();
      if(!empty($transaction)) {
        $deleted_sell_lines = $transaction->sell_lines;
        $deleted_sell_lines_ids = $deleted_sell_lines->pluck('id')->toArray();
        $this->deleteSellLines($deleted_sell_lines_ids, $transaction->location_id);

        $transaction->delete();
      }
    }

    $loan->delete();

    DB::commit();
    session()->flash(Message::SUCCESS_KEY, trans('message.item_deleted_success'));
  }

  public function generateWingCode(Request $request)
  {
    echo generateRegisterId($request->branch, $request->count);
  }
  public function delaySchedule(Request $request, Loan $loan){
    if(!isAdmin() && !Auth::user()->can('loan.delay-schedule')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    $schedule_reference = new  ScheduleReference();
    return view('loan.delay-schedule',compact('loan','schedule_reference'));
  }
  public function delayScheduleSave(Request $request, Loan $loan){
    if(!isAdmin() && !Auth::user()->can('loan.delay-schedule')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    try {
      $schedule_reference_id = $request->schedule_reference_id;
      $scheduleReference = $schedule_reference_id ? ScheduleReference::find($schedule_reference_id) : new  ScheduleReference();
      $scheduleReference->loan_id = $loan->id;
      $scheduleReference->type = $request->type;
      $scheduleReference->installment = $request->installment;
      $scheduleReference->frequency = $request->frequency;
      $scheduleReference->note = $request->note;
      $scheduleReference->save();
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
    return redirect(route('loan.show',$loan));
  }
  public function getDelayStatus(ScheduleReference $scheduleReference){
    if(!isAdmin() && !Auth::user()->can('loan.approved-delay-schedule')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    return view('loan.getDelayStatus',compact('scheduleReference'));
  }
  public function delayStatus(ScheduleReference $scheduleReference){
    if(!isAdmin() && !Auth::user()->can('loan.approved-delay-schedule')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    if($scheduleReference->is_approved==true){
      session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_fail'));
      return redirect(route('loan.show',[$scheduleReference->loan_id,'get'=>'repayment-schedule']));
    }
    $scheduleReference->is_approved = true;
    $scheduleReference->approved_date = now()->toDateTimeString();
    $scheduleReference->approved_note = request()->note;
    if($scheduleReference->save()){

      if($scheduleReference->type == 'de'){
        $schedules = Schedule::where('loan_id',$scheduleReference->loan_id)->where('paid_status',0)->get();
        $lastSchedule = Schedule::where('loan_id',$scheduleReference->loan_id)->where('paid_status',0)->first()->payment_date;
        $day = date_format(date_create($lastSchedule),'d');
        $lastSchedule = strtotime("+".$scheduleReference->installment." months", strtotime($lastSchedule));
        $lastSchedule = date("Y-m-d", $lastSchedule);

        foreach($schedules as $key=> $schedule){
          $scheduleHistory = new ScheduleHistory();
          $scheduleHistory->schedule_reference_id =  $scheduleReference->id;
          $scheduleHistory->loan_id =  $schedule->loan_id;
          $scheduleHistory->payment_date =  $schedule->payment_date;
          $scheduleHistory->principal =  $schedule->principal;
          $scheduleHistory->interest =  $schedule->interest;
          $scheduleHistory->total =  $schedule->total;
          $scheduleHistory->outstanding =  $schedule->outstanding;
          $scheduleHistory->penalty =  $schedule->penalty;
          $scheduleHistory->paid_date =  $schedule->paid_date;
          $scheduleHistory->paid_principal =  $schedule->paid_principal;
          $scheduleHistory->paid_interest =  $schedule->paid_interest;
          $scheduleHistory->paid_total =  $schedule->paid_total;
          $scheduleHistory->paid_penalty =  $schedule->paid_penalty;
          $scheduleHistory->paid_status =  $schedule->paid_status;
          $scheduleHistory->discount_status =  $schedule->discount_status;
          if($scheduleHistory->save()){
            if($key!=0){
              $lastSchedule = oneMonthIncrement($lastSchedule,  $day);
            }

            $schedule->payment_date = $lastSchedule;
            $schedule->save();

          }

        }
      }
      if($scheduleReference->type == 'ip'){
        $schedules = Schedule::where('loan_id',$scheduleReference->loan_id)->where('paid_status',0)->limit($scheduleReference->installment)->get();
        $lastSchedule = Schedule::where('loan_id',$scheduleReference->loan_id)->where('paid_status',0)->latest('payment_date')->first()->payment_date;
        $day = date_format(date_create($lastSchedule),'d');
        // $addDays = date('Y-m-d', strtotime($lastSchedule . ' +'.$scheduleReference->frequency.' day'));
        // dd($addDays);
        foreach($schedules as $schedule){
          $scheduleHistory = new ScheduleHistory();
          $scheduleHistory->schedule_reference_id =  $scheduleReference->id;
          $scheduleHistory->loan_id =  $schedule->loan_id;
          $scheduleHistory->payment_date =  $schedule->payment_date;
          $scheduleHistory->principal =  $schedule->principal;
          $scheduleHistory->interest =  $schedule->interest;
          $scheduleHistory->total =  $schedule->total;
          $scheduleHistory->outstanding =  $schedule->outstanding;
          $scheduleHistory->penalty =  $schedule->penalty;
          $scheduleHistory->paid_date =  $schedule->paid_date;
          $scheduleHistory->paid_principal =  $schedule->paid_principal;
          $scheduleHistory->paid_interest =  $schedule->paid_interest;
          $scheduleHistory->paid_total =  $schedule->paid_total;
          $scheduleHistory->paid_penalty =  $schedule->paid_penalty;
          $scheduleHistory->paid_status =  $schedule->paid_status;
          $scheduleHistory->discount_status =  $schedule->discount_status;
          if($scheduleHistory->save()){
            // if($scheduleReference->frequency==30){
              $lastSchedule = oneMonthIncrement($lastSchedule,  $day);
            // }else{
            //   $lastSchedule = oneMonthIncrement($lastSchedule,  $day);
            // }
            $newSchedule =new Schedule();
            $newSchedule->schedule_reference_id = $schedule->id;
            $newSchedule->loan_id = $schedule->loan_id;
            $newSchedule->payment_date =  $lastSchedule;
            $newSchedule->principal =  $schedule->principal;
            $newSchedule->total =  $schedule->principal;
            $newSchedule->outstanding =  $schedule->outstanding;
            $newSchedule->penalty =  $schedule->penalty;
            $newSchedule->paid_date =  $schedule->paid_date;
            $newSchedule->paid_principal =  $schedule->paid_principal;
            $newSchedule->paid_penalty =  $schedule->paid_penalty;
            $newSchedule->paid_total =  $schedule->paid_principal + $schedule->paid_penalty;
            $newSchedule->paid_status =  $schedule->paid_status;
            $newSchedule->discount_status =  $schedule->discount_status;
            if($newSchedule->save()){
              $schedule->principal=0;
              $schedule->outstanding=0;
              $schedule->total= $schedule->interest;
              $schedule->paid_date;
              $schedule->paid_principal=0;
              $schedule->paid_total=$schedule->paid_interest;
              $schedule->save();
            }
          }
        }
      }
    }
    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return redirect(route('loan.show',[$scheduleReference->loan_id,'get'=>'repayment-schedule']));
  }
  public function getScheduleHistory(ScheduleReference $scheduleReference){
    $schedules = ScheduleHistory::where('schedule_reference_id',$scheduleReference->id)->get();
    return view('loan.partials.schedule-history',compact('scheduleReference','schedules'));
  }
  public function delayScheduleDelete(ScheduleReference $scheduleReference){
    if(!isAdmin() && !Auth::user()->can('loan.deleted-delay-schedule')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }
    if($scheduleReference->is_approved==true){
      return back()->with([
        Message::ERROR_KEY => trans('message.unable_perform_action'),
        'alert-type' => 'warning'
      ], 403);
    }else{
      $scheduleReference->delete();
    }
  }
}
