<?php

namespace App\Http\Controllers;

use App\Models\VariantionLocationDetails;
use Illuminate\Http\Request;
use Auth;

use App\Constants\UserRole;
use App\Constants\FormType;
use App\Constants\Message;

use App\Models\ExtendedProperty;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\Branch;
use App\Models\Unit;
use App\Models\Barcode;
use App\Models\Variantion;
use App\Models\Transaction;
use App\Models\GeneralSetting;

use App\Http\Requests\ProductRequest;
use App\Traits\FileHandling;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use \Carbon\Carbon;

use App\Traits\ProductUtil;
use App\Traits\TransactionUtil;

class ProductController extends Controller
{
  use FileHandling;

  use ProductUtil;

  use TransactionUtil;

  /** @var string  Folder name to store image */
  private $imageFolder = 'product';

  public function __construct()
  {
    //$this->middleware('role:'. UserRole::ADMIN);
  }

  /**
   * Display a listing of the resource.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function index(Request $request)
  {
    if(!Auth::user()->can('product.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $products = Product::with(['variations']);
    if(!empty($request->location)) {
      $location = $request->location;
      $product_list = ProductWarehouse::select('product_id')->where('warehouse_id', $location)->get();
      $products = Product::whereIn('id', $product_list);
    }

    if (!empty($request->brand)) {
      $products = $products->where('brand', $request->brand);
    }

    if(!empty($request->type)) {
      $products = $products->where('type', $request->type);
    }

    if (!empty($request->search)) {
      $products = $products->where(function ($query) use ($request) {
        $searchText = $request->search;
        $query->where('name', 'like', '%' . $searchText . '%')
        ->orWhere('sku', 'like', '%' . $searchText . '%')
        ->orWhere('code', 'like', '%' . $searchText . '%');
        // ->orWhere('price', $searchText);
      });
    }

    if(!empty($request->prod_type)) {
      $products = $products->where('category_id', $request->prod_type);
    }
    // dd($products->get());

    $itemCount = $products->count();
    $products = $products->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);
    $brands = brands();
    $locations= Branch::allWarehouses();
    $productCategories = ExtendedProperty::allProductCategories();
    // dd($products);

    return view('product/index', compact('brands', 'locations', 'itemCount', 'offset', 'products', 'productCategories'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @param Product $product
   *
   * @return Response
   */
  public function create(Product $product)
  {
    if(!Auth::user()->can('product.add')) {
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $title = trans('app.create');
    $formType = FormType::CREATE_TYPE;
    $productCategories = ExtendedProperty::allProductCategories();
    $brands = ExtendedProperty::allBrands();
    $units = Unit::get();

    $latestProduct = Product::latest()->first();
    $code = (int)($latestProduct == null ? 0 : $latestProduct->code);
    $code = $code + 1;
    $code = str_pad($code, 8, "0", STR_PAD_LEFT);

    $locations=Branch::all();

    return view('product/form', compact(
      'brands', 'formType', 'product', 'productCategories', 'title', 'units', 'code', 'locations'
    ));
  }

  public function save(ProductRequest $request, Product $product)
  {
    if(!Auth::user()->can('product.add') && !Auth::user()->can('product.edit')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    try {
      if($request->enable_stock) {
        $this->validate($request, [
          'alert_quantity' => 'required|integer|min:0'
        ]);
      }
      if($request->product_type == 'single') {
        $this->validate($request, [
          'cost' => 'nullable|numeric|min:0',
          'price' => 'required|numeric|min:0',
        ]);
      }

      $productCode = preg_replace('/\s+/', '', $request->product_code);

      $product->name            = $request->name;
      $product->category_id     = $request->category;
      $product->brand           = $request->brand;
      $product->code            = $productCode;
      $product->sku             = $productCode;
      $product->type            = $request->product_type;
      $product->cost            = $request->cost ?? 0;
      $product->price           = $request->price ?? 0;
      $product->alert_quantity  = $request->alert_quantity ?? 0;
      $product->description     = $request->description;
      $product->enable_stock    = $request->enable_stock ?? 0;
      $product->enable_sr_no    = $request->enable_sr_no ?? 0;
      $product->barcode_type    = "C128";
      $product->unit            = Unit::find($request->unit)->short_name;
      $product->unit_id         = $request->unit;

      if (!empty($request->photo)) {
        $product->photo = $this->uploadImage($this->imageFolder, $request->photo);
      }

      DB::beginTransaction();

      // dd($product);
      $product->save();



      if($product->type == 'single') {

        $variantion = Variantion::where('product_id', $product->id)->first() ?? new Variantion;

        $variantion->name                   = "DUMMY";
        $variantion->product_id             = $product->id;
        $variantion->sub_sku                = $product->sku ?? $product->code;
        $variantion->default_purchase_price = $product->cost;
        $variantion->default_sell_price     = $product->price;
        $variantion->profit_percent         = 0;
        $variantion->save();

      }
      else {
        if (!empty($request->input('variant'))) {
          $input_variations = $request->input('variant');
          $this->updateVariableProductVariations($product->id, $input_variations);
        }
      }

        $pvids = [];
        foreach($request->location_id as $vlc_id){
            foreach ($product->variations as $pv){
                array_push($pvids, $pv->id);
                $pvld = VariantionLocationDetails::where('product_id', $product->id)
                    ->where('variantion_id', $pv->id)
                    ->where('location_id', $vlc_id)
                    ->first();

                if($pvld == null){
                    $location = new VariantionLocationDetails();
                    $location->product_id = $product->id;
                    $location->variantion_id = $pv->id;
                    $location->location_id = $vlc_id;
                    $location->save();
                }
            }
        }

        VariantionLocationDetails::where('product_id', $product->id)
            ->whereIn('variantion_id', $pvids)
            ->whereNotIn('location_id', $request->location_id)
            ->delete();

      $this->setAndGetReferenceCount('product_code');

      DB::commit();
    }
    catch(\Exception $e) {
      \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

      session()->flash(Message::SAVE_FAILURE_VALUE, trans('message.item_saved_fail'));
      return redirect()->back()->withInput()->withErrors([
        Message::ERROR_KEY => trans('message.item_saved_fail'.$e),
      ]);
    }

    if ($request->input('submit_type') == 'with_opening_stock') {
      return redirect()->action('OpeningStockController@add', [$product->id]);
    }
    elseif ($request->input('submit_type') == 'with_adding_another') {
      return redirect()->action('ProductController@create');
    }

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return redirect()->route('product.index');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param Product $product
   *
   * @return Response
   */
  public function edit(Product $product)
  {
    if(!Auth::user()->can('product.edit')) {
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $title = trans('app.edit');
    $formType = FormType::EDIT_TYPE;
    $productCategories = ExtendedProperty::allProductCategories();
    $brands = ExtendedProperty::allBrands();
    $units = Unit::get();

    $code = (int)Product::latest()->first()->code;
    $code = $code + 1;
    $code = str_pad($code, 8, "0", STR_PAD_LEFT);

    $locations=Branch::all();

    return view('product/form', compact(
      'brands', 'formType', 'product', 'productCategories', 'title', 'units', 'code', 'locations'
    ));
  }

  /**
   * Display the specified resource.
   *
   * @param Product $product
   *
   * @return \Illuminate\Http\Response
   */
  public function show(Product $product)
  {
    if(!Auth::user()->can('product.browse')) {
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $title = trans('app.detail');
    $formType = FormType::SHOW_TYPE;

    return view('product/form', compact('formType', 'product', 'title'));
  }

  /**
   * Save new or existing product.
   *
   * @param ProductRequest $request
   * @param Product $product
   *
   * @return Response
   */

  /**
   * Show a listing of warehouses of a product.
   *
   * @param Product $product
   *
   * @return Response
   */
  public function warehouseList(Product $product)
  {
    if(!Auth::user()->can('product.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $stocks = ProductWarehouse::where('product_id', $product->id)->get();
    $itemCount = $stocks->count();
    return view('product.warehouse', compact('itemCount', 'product', 'stocks'));
  }

  /**
   * Show a listing of warehouses of a product.
   *
   * @param Product $product
   *
   * @return Response
   */
  public function stockLevel(Request $request)
  {
    if(!Auth::user()->can('product.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    $location_filter = '';
    $products = Variantion::join('products as p', 'p.id', '=', 'variantions.product_id')
    ->join('variantion_location_details as vld', 'variantions.id', '=', 'vld.variantion_id');

    if(!empty($request->location)) {
      $products = $products->where('vld.location_id', $request->location);
      $location_filter .= "AND transactions.location_id='{$request->location}' ";
    }

    if (!empty($request->brand)) {
      $products = $products->where('brand', $request->brand);
    }

    if(!empty($request->type)) {
      $products = $products->where('p.type', $request->type);
    }
    else {
      $products = $products->whereIn('p.type', ['single', 'variant']);
    }

    if (!empty($request->search)) {
      $products = $products->where(function ($query) use ($request) {
        $searchText = $request->search;
        $query->where('p.name', 'like', '%' . $searchText . '%')
        ->orWhere('p.sku', 'like', '%' . $searchText . '%')
        ->orWhere('variantions.sub_sku', 'like', '%' . $searchText . '%')
        ->orWhere('p.code', 'like', '%' . $searchText . '%');
        // ->orWhere('price', $searchText);
      });
    }

    if(!empty($request->prod_type)) {
      $products = $products->where('category_id', $request->prod_type);
    }

    $products = $products->select(
      'p.code as code',
      'p.name as name',
      'p.type',
      'p.id as product_id',
      'p.unit',
      'p.price',
      'p.enable_stock as enable_stock',
      'variantions.default_sell_price as unit_price',
      'variantions.name as variantion_name',
      'variantions.sub_sku as variantion_sku',
      'variantions.id as variantion_id',
    DB::raw("(SELECT SUM(PL.quantity) FROM transactions
              JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
              WHERE transactions.status='received' AND transactions.type IN ('purchase','opening_stock') {$location_filter}
              AND PL.variantion_id=variantions.id) as total_purchased"),
    DB::raw("(SELECT SUM(TSL.quantity) FROM transactions
              JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
              WHERE transactions.status='final' AND transactions.type IN ('sell', 'leasing') {$location_filter}
              AND TSL.variantion_id=variantions.id) as total_sold"),

              DB::raw("(SELECT SUM(IF(transactions.type='purchase_transfer', PL.quantity, 0) ) FROM transactions
              JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
              WHERE transactions.status='received' AND transactions.type='purchase_transfer' {$location_filter}
              AND (PL.variantion_id=variantions.id)) as total_transfered_in"),


              DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions
            JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
            WHERE transactions.status='final' AND transactions.type='sell_transfer' {$location_filter}
            AND (TSL.variantion_id=variantions.id)) as total_transfered_out"),
    DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions
              JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
              WHERE transactions.status='received' AND transactions.type='stock_adjustment' {$location_filter}
              AND (SAL.variantion_id=variantions.id)) as total_adjusted"),
    DB::raw("SUM(vld.qty_available) as stock")
    )->whereNull('p.deleted_at')
    ->groupBy('variantions.id');

    $products = $products->orderBy('stock', 'desc')->paginate(paginationCount());
    $offset = offset($request->page);
    $brands = brands();
    $locations= Branch::get();
    $productCategories = ExtendedProperty::allProductCategories();
    $itemCount = $products->total();

    return view('product/index_stock', compact('brands', 'locations', 'itemCount', 'offset', 'products', 'productCategories'));
  }

  /**
   * Delete product.
   *
   * @param Product $product
   *
   * @return Response
   */
  public function destroy(Product $product, Request $request)
  {
    if(!Auth::user()->can('product.delete')) {
      // return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
      session()->flash(Message::ERROR_KEY, trans('message.no_permission'));
    }

    if (!$request->ajax()) {
      session()->flash(Message::ERROR_KEY, trans('message.unable_perform_action'));
    }
    // dd(count($product->sellDetails));
    if(count($product->loanDetials) > 0 ||  count($product->sellDetails)>0 || count($product->purchaseDetails)>0){
      session()->flash(Message::ERROR_KEY, trans('message.in_used_action'));
    }else {
      // $product->variantions->delete();
      foreach($product->variantions as $va){
        $va->delete();
      }
      foreach($product->variantionLocationDetails as $vl){
        $vl->delete();
      }
      $product->delete();
      session()->flash(Message::SUCCESS_KEY, trans('message.item_deleted_success'));
    }


  }

  /**
   * Get product form parts.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function getVariationValueRow(Request $request)
  {
    $variation_index = $request->input('variation_row_index');
    $value_index = $request->input('value_index') + 1;
    $row_type = $request->input('row_type', 'add');
    $productCode = $request->input('product_code');

    return response()->json([
      'data' => view('product.partials.variation_value_row')->with(compact('variation_index', 'value_index', 'row_type', 'productCode'))->render()
    ]);
  }

  public function getProductBarcode()
  {
    $barcodes = Barcode::orderBy('is_default', 'desc')->get();

    return view('product.print_barcode', compact("barcodes"));
  }

  public function getProductBarcodeSuggestion(Request $request)
  {
    $query = $request->get('query');

    $products = Product::where('name', 'LIKE', "%{$query}%")
    ->orWhere('code', 'LIKE', "%{$query}%")
    ->orWhere('sku', 'LIKE', "%{$query}%")
    ->where('active', 1)
    ->limit(10)->get()->map(function($product) {
      return [
        'id'        => $product->id,
        'label'     => $product->name,
        'code'      => $product->code!=null ? $product->code : '',
        'price'     => $product->price ?? 0,
        'cost'      => $product->cost ?? 0,
        'quantity'  => 1
      ];
    });

    return response()->json($products, 200, [], JSON_UNESCAPED_UNICODE);
  }

  public function getProductBarcodeGenerate(Request $request)
  {
    $status = false;
    $message = '';
    $html = '';

    try {
      $items              = $request->item;
      $barcodeSetting     = $request->barcode_setting;
      $displayName        = $request->product_name ? true : false;
      $displayVariantion  = $request->product_variantion ? true : false;
      $displayPrice       = $request->product_price ? true : false;
      $displayUnit        = $request->product_unit ? true : false;
      $displayCategory    = $request->product_category ? true : false;
      $total_qty          = collect($items)->sum('quantity');

      $barcode = Barcode::find($barcodeSetting);
      if($barcode->is_continuous) {
        $rows = ceil($total_qty/$barcode->stickers_in_one_row) + 0.4;
        $barcode->paper_height = $barcode->top_margin + ($rows*$barcode->height) + ($rows*$barcode->row_distance);
      }

      $products = Product::with(['variations', 'unit', 'category'])->whereIn('id', collect($items)->pluck('product_id'))->get()->map(function($item) use($items) {
        $item->quantity = $items[$item->id]['quantity'];
        return $item;
      });
      $html = view('product.partials.barcode_preview', [
        'products' => $products,
        'barcode' => $barcode,
        'request' => [
          "displayName" => $displayName,
          "displayPrice" => $displayPrice,
          "displayVariantion" => $displayVariantion,
          "displayUnit" => $displayUnit,
          "displayCategory" => $displayCategory
        ],
      ])->render();

      $status = true;
    }
    catch(\Exception $e) {
      $message = 'I am wrong!!!';
    }

    return response()->json([
      'html' => $html,
      'status' => $status,
      'message' => $message
    ]);
  }

  public function getProductsSuggestion(Request $request)
  {
    // dd($request->all());
    $search = $request->get('query');
    $branch = $request->get('branch');
    $type = $request->get('type');
    $setting = GeneralSetting::first();
    if(auth()->user()->staff) {
      $branch = auth()->user()->staff->branch_id;
    }
    $products = Product::leftJoin('variantions', 'products.id', '=', 'variantions.product_id')
    ->where('active', 1)
    ->whereNull('products.deleted_at')
    ->whereNull('variantions.deleted_at')
    ->leftJoin('variantion_location_details as vld', 'vld.variantion_id', '=', 'variantions.id');

    $product_oversale = clone $products;

    if(!empty($branch)){
      $products->where('vld.location_id', '=', $branch);
    }

    if(!empty($search)) {
      $products->where(function ($query) use ($search) {
        $query->where('products.name', 'like', '%' . $search .'%');
        $query->orWhere('sku', 'like', '%' . $search .'%');
        $query->orWhere('sub_sku', 'like', '%' . $search .'%');
        $query->orWhere('code', 'like', '%' . $search . '%');
        $query->orWhere('variantions.name', 'like', '%' . $search . '%');
      });
    }

    // if($type == 'transfer' || ($type == 'sale' && $setting->enable_over_sale == 0)){
    // if($type == 'sale' && $setting->enable_over_sale == 0){
    //   $products->where('vld.qty_available', '>', 0);
    // }

    $product_selectId = clone $products;
    $product_selectVariantId = clone $products;

    $products->select(
      'products.id as id',
      'products.name',
      'products.type',
      'products.sku',
      'products.code',
      'products.enable_stock',
      'products.enable_sr_no',
      'variantions.id as variantion_id',
      'variantions.name as variantion',
      'variantions.product_id as product_id',
      'variantions.default_purchase_price as cost',
      'variantions.default_sell_price as price',
      'vld.qty_available',
      'variantions.sub_sku',
      'products.unit'
    );

    $result = $products->get()->map(function($item) {
      $item->label = $item->name . ($item->variantion!='DUMMY' ? ' - '.$item->variantion : '') .' ('.number_format($item->qty_available).' '.$item->unit.')';
      return $item;
    });

    // if($type == 'sale'){
      $existing_product_id = array();
      $existing_variantion_id = array();
      foreach($result as $rs){
        array_push($existing_product_id, $rs->id);
        array_push($existing_variantion_id, $rs->variantion_id);
      }
      if(count($existing_product_id) > 0 && count($existing_variantion_id) > 0){
        $product_oversale->where(function ($query) use ($existing_product_id, $existing_variantion_id) {
          $query->whereNotIn('products.id', $existing_product_id);
          $query->whereNotIn('variantions.id', $existing_variantion_id);
        });
      }
      if(!empty($search)) {
        $product_oversale->where(function ($query) use ($search) {
          $query->where('products.name', 'like', '%' . $search .'%');
          $query->orWhere('sku', 'like', '%' . $search .'%');
          $query->orWhere('sub_sku', 'like', '%' . $search .'%');
          $query->orWhere('code', 'like', '%' . $search . '%');
          $query->orWhere('variantions.name', 'like', '%' . $search . '%');
        });
      }
      $product_oversale->distinct()->select(
        'products.id as id',
        'products.name',
        'products.type',
        'products.sku',
        'products.code',
        'products.enable_stock',
        'products.enable_sr_no',
        'variantions.id as variantion_id',
        'variantions.name as variantion',
        'variantions.product_id as product_id',
        'variantions.default_purchase_price as cost',
        'variantions.default_sell_price as price',
        // 'vld.qty_available',
        'variantions.sub_sku',
        'products.unit'
      );
      $result_1 = $product_oversale->get()->map(function($item) {
        $item->label = $item->name . ($item->variantion!='DUMMY' ? ' - '.$item->variantion : '') .' ('.number_format($item->qty_available).' '.$item->unit.')';
        $item->qty_available = 0;
        return $item;
      });

      $finalResult = array_merge($result->toArray(), $result_1->toArray());

      sort($finalResult);
      return response()->json($finalResult);
    // }

    return response()->json($result);
  }

  public function installVariantionToLoan()
  {
    $loans = \App\Models\Loan::whereNull('transaction_id')->get();
    foreach($loans as $key => $loan) {
      $products = [];

      $transaction = new Transaction;
      $transaction->location_id      = $loan->branch_id;
      $transaction->created_by       = $loan->user_id;
      $transaction->transaction_date = Carbon::parse($loan->approved_date)->toDateTimeString();
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

      //Check for final and do some processing.
      if ($transaction->status == 'final') {
        //update product stock
        foreach ($products as $product) {
          if ($product['enable_stock']) {
            $this->decreaseProductQuantity($product['id'], $product['variantion_id'], $loan->branch_id, $product['quantity']);
          }
        }
      }

      $loan->transaction_id = $transaction->id;
      $loan->disbursed_date = $loan->approved_date;
      $loan->save();

      DB::commit();
    }

    // http://www.amazonphoneshops.com/product/run-stock/
    dd($loans);
  }

  public function installStock()
  {
    $variantions = Variantion::get();
    foreach($variantions as $variantion) {
      foreach(Branch::get() as $branch) {
        $this->updateProductQuantity($branch->id, $variantion->product_id, $variantion->id, 1);
      }
    }

    dd($variantions);
  }
}
