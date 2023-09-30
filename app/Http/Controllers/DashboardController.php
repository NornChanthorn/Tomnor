<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Constants\LoanStatus;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Variantion;
use App\Models\Loan;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\Schedule;
use App\Models\VariantionLocationDetails;
use Illuminate\Http\Request;
use DB;
use Auth;

class DashboardController extends Controller
{
  /**
   * Display dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $view_data = false;
    if(Auth::user()->can('dashboard')) {
      $view_data = true;

      $clientCount = Client::count();

      $loanCount          = Loan::count() ?? 0;
      $activeLoanCount    = Loan::where('status', LoanStatus::ACTIVE)->count();
      $rejectedLoanCount  = Loan::where('status', LoanStatus::REJECTED)->count();
      $paidLoanCount      = Loan::where('status', LoanStatus::PAID)->count();
      $pendingLoanCount   = Loan::where('status', LoanStatus::PENDING)->count();
      $overdueLoanCount   = Loan::where('status', LoanStatus::ACTIVE)
      ->whereHas('schedules', function ($query) {
        $query->where('paid_status', 0)->whereRaw('payment_date < CURDATE()');
      })->count();

      $activeLoanPercent = number_format($activeLoanCount * 100 / ($loanCount == 0 ? 1 : $loanCount));
      $pendingLoanPercent = number_format($pendingLoanCount * 100 / ($loanCount  == 0 ? 1 : $loanCount));
      $paidLoanPercent = number_format($paidLoanCount * 100 / ($loanCount == 0 ? 1 : $loanCount));
      $rejectedLoanPercent = number_format($rejectedLoanCount * 100 / ($loanCount  == 0 ? 1 : $loanCount));
      $loanChartData = [
        'colors' => [
            '#ffc107',
            '#007bff',
            '#28a745',
            '#dc3545'
        ],
        'labels' => [
          trans('app.pending').'('.$pendingLoanPercent.'%)',
          trans('app.active').'('.$activeLoanPercent.'%)',
          trans('app.paid').'('.$paidLoanPercent.'%)',
          trans('app.rejected').'('.$rejectedLoanPercent.'%)'
        ],
        'data' => [ $pendingLoanPercent, $activeLoanPercent, $paidLoanPercent, $rejectedLoanPercent ]
      ];
      // dd($loanChartData);

      // Calculate monthly principals and interests of current year for graph chart
      $schedulesPerYear = Schedule::whereHas('loan', function ($query) {
        $query->whereIn('status', [LoanStatus::ACTIVE, LoanStatus::PAID]);
      })->whereRaw(date('Y') . ' = YEAR(paid_date)')->get();

      $paidInterests = $paidPrincipals = array_map(function () { return 0; }, range(1, 12));
      foreach ($schedulesPerYear as $schedule) {
        $monthArrIndex = (substr($schedule->paid_date, 5, 2) - 1);
        $paidInterests[$monthArrIndex] += $schedule->paid_interest;
        $paidPrincipals[$monthArrIndex] += $schedule->paid_principal;
      }

      $totalPaidAmount = (array_sum($paidPrincipals) + array_sum($paidInterests));
      $paidPrincipals = array_map(function ($value) { return decimalNumber($value); }, $paidPrincipals);
      $paidInterests = array_map(function ($value) { return decimalNumber($value); }, $paidInterests);
      return view('dashboard', compact(
        'clientCount',
        'overdueLoanCount',
        'paidInterests',
        'paidPrincipals',
        'totalPaidAmount',
        'view_data',
        'paidLoanCount',
        'activeLoanCount',
        'rejectedLoanCount',
        'pendingLoanCount',
        'loanCount',
        'loanChartData'
      ));
    }

    return view('dashboard', compact('view_data'));
  }
  public function index2()
  {
    $view_data = false;
    if(Auth::user()->can('dashboard')) {
      $view_data = true;
      $variantions = DB::table('variantions')
      ->join('variantion_location_details as vld', 'variantions.id', '=', 'vld.variantion_id')
      ->select('variantions.default_purchase_price as cost', 'variantions.default_sell_price as price', 'vld.qty_available as qty')
      ->get();
      $totalPrice=0;
      $totalCost=0;

      foreach ($variantions as $variantion) {
          $totalPrice +=$variantion->qty * $variantion->price;
          $totalCost +=$variantion->qty * $variantion->cost;
      }
      $totalProfit= $totalPrice - $totalCost;

      $stockChartData = [
          'colors' => ['#DC143C', '#0080FF', '#D2D2D2'],
          'labels' => [
            trans('app.purchase_price').' $ '.decimalNumber($totalCost,2),
            trans('app.selling_price').' $ '.decimalNumber($totalPrice,2),
            trans('app.estimated_profit').' $ '.decimalNumber($totalProfit,2),
          ],
          'data' => [ decimalNumber($totalCost), decimalNumber($totalPrice), decimalNumber($totalProfit) ]
      ];

      $saleDuePayment = Transaction::join('contacts','contacts.id','=','transactions.contact_id')
                        ->join('invoices','invoices.transaction_id','=','transactions.id')
                        ->where('transactions.type','sell')
                        ->where('transactions.payment_status','partial')
                        ->select('contacts.name as name', 'invoices.total as due_amount','transactions.invoice_no')->latest('transactions.created_at')->limit(10)->get();

      $purchaseDuePayment = Transaction::join('contacts','contacts.id','=','transactions.contact_id')
                        ->join('invoices','invoices.transaction_id','=','transactions.id')
                        ->where('transactions.type','purchase')
                        ->where('transactions.payment_status','partial')
                        ->select('contacts.name as name', 'invoices.total as due_amount','transactions.ref_no','transactions.final_total')
                        ->latest('transactions.created_at')->limit(10)->get();

      $alertQty   =   Variantion::join('products as p','variantions.product_id','=','p.id')
                      ->join('variantion_location_details as vld','variantions.id','=','vld.variantion_id')
                      ->join('branches as b','b.id','=','vld.location_id')
                      ->where('p.alert_quantity','>=','vld.qty_available')
                      ->select('p.name as pname','b.name as bname','vld.qty_available')->limit(10)->get();
      $saleToday  =   Invoice::join('transactions as t', 'invoices.transaction_id','=','t.id')
                      ->join('contacts','contacts.id','=','t.contact_id')
                      ->whereDate('invoices.payment_date',date("Y-m-d"))->where('t.type','sell')
                      ->select('contacts.name as name', 'invoices.total as payment_amount', 't.invoice_no', 'invoices.payment_date')->limit(10)->get();
      $purchase = Transaction::with(['invoices'])->whereIn('type', ['purchase'])->whereRaw(date('Y') . ' = YEAR(transaction_date)');
      $purchasePerYear = $purchase->get();

      $totalPurchase = array_map(function () { return 0; }, range(1, 12));
      foreach ($purchasePerYear as $purchase){
        $monthArrIndex = (substr($purchase->transaction_date, 5, 2) - 1);
        $totalPurchase[$monthArrIndex] += $purchase->final_total;
      }

      $sale = Transaction::with(['invoices'])->whereIn('type', ['leasing', 'sell'])->whereRaw(date('Y') . ' = YEAR(transaction_date)');
      $salePerYear = $sale->get();
      $totalSell = array_map(function () { return 0; }, range(1, 12));
      foreach ($salePerYear as $sale){
        $monthArrIndex = (substr($sale->transaction_date, 5, 2) - 1);
        $totalSell[$monthArrIndex] += $sale->final_total;
        
      }

      $totalPaidAmount = (array_sum($totalSell) + array_sum($totalPurchase));
      $totalPurchase = array_map(function ($value) { return decimalNumber($value); }, $totalPurchase);
      $totalSell = array_map(function ($value) { return decimalNumber($value); }, $totalSell);
      
      $totalProfit = array_map(function () { return 0; }, range(1, 12));
      foreach($totalSell as $index => $value){
        $totalProfit[$index] += $totalSell[$index] - $totalPurchase[$index];
      }
      $totalProfit = array_map(function ($value) { return decimalNumber($value); }, $totalProfit);
      $locations= Branch::latest()->get();

      return view('dashboard2',compact(
          'stockChartData',
          'purchaseDuePayment',
          'saleDuePayment',
          'saleToday',
          'alertQty',
          'totalPaidAmount',
          'totalSell',
          'totalPurchase',
          'locations',
          'view_data'

      ));
    }

    return view('dashboard2', compact('view_data'));
  }
  public function getDashboardsSuggestion(Request $request){
   //Total Today
   $result=[];
   //Total Today
   $purchase = Transaction::with(['invoices'])->whereIn('type', ['purchase']);
   $sale = Transaction::with(['invoices'])->whereIn('type', ['leasing', 'sell']);
   $totalExpenseAmount = Expense::whereNull('deleted_at');
   $totalClient = Contact::where("is_default",0)->where('type','customer');
   $totalProductQTY = VariantionLocationDetails::leftJoin('products as p','p.id','=','variantion_location_details.product_id')
   ->leftJoin('variantions as v','v.id','=','variantion_location_details.variantion_id');
   
   if($request->get('location')){
     $location = $request->get('location');
     $purchase=$purchase->where('location_id',$location);
     $sale = $sale->where('location_id',$location);
     $totalProductQTY = $totalProductQTY->where('variantion_location_details.location_id',$location);
   }
   if($request->type=='today'){
     $purchase = $purchase->where('transaction_date', Carbon::today());
     $sale = $sale->where('transaction_date', Carbon::today());
     $totalExpenseAmount = $totalExpenseAmount->where('created_at', Carbon::today());
     $totalClient = $totalClient->whereDate('created_at',Carbon::today()); 

   }elseif($request->get('type')=='weekly'){
     $purchase = $purchase->whereBetween('transaction_date',[Carbon::now()->subDays(7),Carbon::now()]);
     $sale = $sale->whereBetween('transaction_date',[Carbon::now()->subDays(7),Carbon::now()]);
     $totalExpenseAmount = $totalExpenseAmount->whereBetween('created_at',[Carbon::now()->subDays(7),Carbon::now()]);
     $totalClient = $totalClient->whereBetween('created_at',[Carbon::now()->subDays(7),Carbon::now()]); 
   }elseif($request->get('type')=='monthly'){
     $purchase = $purchase->whereMonth('transaction_date',Carbon::now()->month)->whereYear('transaction_date',Carbon::now()->year);
     $sale = $sale->whereMonth('transaction_date',Carbon::now()->month)->whereYear('transaction_date',Carbon::now()->year);
     $totalExpenseAmount = $totalExpenseAmount->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at',Carbon::now()->year);
     $totalClient = $totalClient->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at',Carbon::now()->year); 
   }elseif($request->get('type')=='yearly'){
     $purchase = $purchase->whereYear('transaction_date',Carbon::now()->year);
     $sale = $sale->whereYear('transaction_date',Carbon::now()->year);
     $totalExpenseAmount = $totalExpenseAmount->whereYear('created_at',Carbon::now()->year);
     $totalClient = $totalClient->whereYear('created_at',Carbon::now()->year); 
   }

   $purchase = $purchase->get();

   $totalPurchaseAmount = $purchase->sum('final_total');

   $totalDuePurchaseAmount =  $totalPurchaseAmount - $purchase->map(function($item) {
     return $item->invoices->sum('payment_amount');
   })->sum();
   
   $sale = $sale->get();
   
   $totalSellAmount = $sale->sum('final_total');

   $totalDueSaleAmount = $totalSellAmount - $sale->map(function($item) {
     return $item->invoices->sum('payment_amount');
   })->sum();

   $totalNetRevenueAmount = $sale->map(function($item) {
     return $item->invoices->sum('payment_amount');
   })->sum();
   $totalProfit = $totalSellAmount - $totalPurchaseAmount;
   $totalExpenseAmount=$totalExpenseAmount->sum('amount');

   $totalClient=$totalClient->count();

   $totalProductQTY = $totalProductQTY->sum('variantion_location_details.qty_available');

   $result = [
     'totalSellAmount' =>  decimalNumber($totalSellAmount,2),
     'totalPurchaseAmount' => decimalNumber($totalPurchaseAmount,2),
     'totalDuePurchaseAmount'  => decimalNumber($totalDuePurchaseAmount,2),
     'totalNetRevenueAmount' =>  decimalNumber($totalNetRevenueAmount,2),
     'totalDueSaleAmount' => decimalNumber($totalDueSaleAmount,2),
     'totalProductQTY' => number_format($totalProductQTY),
     'totalClient'  => number_format($totalClient),
     'totalExpenseAmount'  =>  decimalNumber($totalExpenseAmount,2),
     'totalProfit' => decimalNumber($totalProfit,2),
   ];
   return response()->json($result);  

  }
}
