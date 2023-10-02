<?php

Route::redirect('/', 'dashboard');
Auth::routes();

if (Config::get('app.WRONG_STOCK')==true){
  Route::get('updated-qty','UpdatedQTYLocationController@updatedqty')->name('updated-qty');
}
Route::get('dashboard-suggestion', 'DashboardController@getDashboardsSuggestion')->name('dashboard-suggestion');
Route::middleware(['auth'])->group(function () {
  Route::get('dashboard', 'DashboardController@index')->name('dashboard');
  Route::get('dashboard2', 'DashboardController@index2')->name('dashboard2');

  // Address
  Route::prefix('address')->name('address.')->group(function () {
    Route::post('{id}/get-sub-addresses', 'AddressController@getSubAddresses')->name('get_sub_addresses');
  });

  // Client
  Route::prefix('client')->name('client.')->group(function() {
    Route::get('list', "ClientController@getClients")->name('list');
  });
  Route::post('client/save/{client?}', 'ClientController@save')->name('client.save');
  Route::resource('client', 'ClientController')->only(resourceRouteMethods());

  // Supplier
  Route::prefix('contact')->name('contact.')->group(function() {
    Route::get('list', "ContactController@getSuppliers")->name('list');
    Route::get('client-list', "ContactController@getClients")->name('client-list');
    Route::post('check-contact', "ContactController@checkContact")->name('check-contact');
    Route::get('group', "ContactGroupController@index")->name('group.index');
    Route::get('group/create', "ContactGroupController@create")->name('group.create');
    Route::get('group/{group}', "ContactGroupController@edit")->name('group.edit');
    Route::POST('group/save/{group?}', "ContactGroupController@save")->name('group.save');
    // Route::get('group/{group}/edit', "ContactGroupController@edit")->name('group.edit');
    Route::delete('group/{group}', "ContactGroupController@destroy")->name('group.destroy');
  });
  Route::post('contact/save/{contact?}', 'ContactController@save')->name('contact.save');
  Route::resource('contact', 'ContactController')->only(resourceRouteMethods());

  // Branch
  Route::prefix('branch')->name('branch.')->group(function () {
    Route::post('save/{branch?}', 'BranchController@save')->name('save');
    Route::get('{branch}/product', 'BranchController@productList')->name('list_product');
  });
  Route::resource('branch', 'BranchController')->only(resourceRouteMethods());

  // Staff
  Route::prefix('staff')->name('staff.')->group(function () {
    Route::post('save/{staff?}', 'StaffController@save')->name('save');
    Route::post('ajax/{branchId}/get-agents', 'StaffController@getAgents')->name('get_agents');
    Route::get('{staff}/commission', 'StaffController@commission')->name('commission');
    Route::post('{staff}/save-commission', 'StaffController@saveCommission')->name('save_commission');
  });
  Route::resource('staff', 'StaffController')->only(resourceRouteMethods());
  // Position
  Route::post('position/save', 'PositionController@save')->name('position.save');
  Route::resource('position', 'PositionController')->only(resourceRouteMethods(false));
  //Expense
  Route::prefix('expense')->name('expense.')->group(function () {
    Route::post('save/{expense?}', 'ExpenseController@save')->name('save');
    // Route::post('ajax/{branchId}/get-agents', 'StaffController@getAgents')->name('get_agents');
  });
  Route::resource('expense', 'ExpenseController')->only(resourceRouteMethods());
  Route::prefix('expense-type')->name('expense-type.')->group(function () {
    Route::get('/', 'ExpenseController@type')->name('index');
    Route::get('/create', 'ExpenseController@typeCreate')->name('create');
    Route::get('/edit/{id}', 'ExpenseController@typeEdit')->name('edit');
    Route::post('save', 'ExpenseController@typeSave')->name('save');
    // Route::post('ajax/{branchId}/get-agents', 'StaffController@getAgents')->name('get_agents');
  });
  Route::prefix('collateral-type')->name('collateral-type.')->group(function () {
    Route::get('/', 'CollateralController@typeIndex')->name('index');
    Route::get('create', 'CollateralController@typeCreate')->name('create');
    Route::get('edit/{id}', 'CollateralController@typeEdit')->name('edit');
    Route::post('save', 'CollateralController@typeSave')->name('save');
  });



  // Product
  Route::prefix('product')->name('product.')->group(function () {
    Route::post('save/{product?}', 'ProductController@save')->name('save');
    Route::get('{product}/warehouse', 'ProductController@warehouseList')->name('list_warehouse');
    Route::get('/stock', 'ProductController@stockLevel')->name('product_stock');

    Route::get('get_variation_value_row', 'ProductController@getVariationValueRow')->name('get_variation_value_row');
    Route::get('barcode', 'ProductController@getProductBarcode')->name('barcode');
    Route::get('ime', 'ImeController@getIme')->name('ime');
    Route::get('ime-single', 'ImeController@ime_single')->name('ime-single');
    Route::get('ime-show/{id}', 'ImeController@show')->name('ime-show');
    Route::get('ime-qty', 'ImeController@show_ime')->name('show_ime');
    Route::get('ime-create', 'ImeController@create')->name('ime-create');
    Route::post('ime-save', 'ImeController@save')->name('ime-save');
    Route::post('save-ime', 'ImeController@save_ime')->name('save-ime');
    Route::get('ime-delete/{id}', 'ImeController@destroy')->name('ime-destroy');
    Route::get('barcode-suggestion', 'ProductController@getProductBarcodeSuggestion')->name('barcode-suggestion');
    Route::post('barcode-generate', 'ProductController@getProductBarcodeGenerate')->name('barcode-generate');

    Route::get('product-variantion', 'ProductController@getProductsSuggestion')->name('product-variantion');

    // just update variantion to loan
    Route::get('run-variantion', 'ProductController@installVariantionToLoan');
    Route::get('run-stock', 'ProductController@installStock');
  });
  Route::resource('product', 'ProductController')->only(resourceRouteMethods());
  // Route::get('product/{id}/delete', 'ProductController@destroy')->name('pos.create');

  Route::get('/opening-stock/add/{product_id}', 'OpeningStockController@add')->name('opening-stock.add');
  Route::post('/opening-stock/save', 'OpeningStockController@save');

  // Product category
  Route::post('product-category/save/{productCategory?}', 'ProductCategoryController@save')->name('product_category.save');
  Route::resource('product-category', 'ProductCategoryController')->only(resourceRouteMethods(false))->names(setResourceRouteNames('product_category'));

  Route::post('product-units/save/{unit?}', 'ProductUnitsController@save')->name('product-units.save');
  Route::resource('product-units', 'ProductUnitsController')->only(resourceRouteMethods(false))->names(setResourceRouteNames('product-units'));

  // Brand
  Route::post('brand/save', 'BrandController@save')->name('brand.save');
  Route::resource('brand', 'BrandController')->only(resourceRouteMethods(false));

  // Stock transfer
  Route::prefix('transfer')->name('transfer.')->group(function () {
    Route::post('save', 'TransferController@save')->name('save');
    Route::post('ajax/{warehouseId}/get-products', 'TransferController@getProducts')->name('get_products');
  });
  Route::resource('transfer', 'TransferController')->only(resourceRouteMethods());

  // Stock Adjustment
  Route::prefix('adjustment')->name('adjustment.')->group(function () {
    Route::post('save', 'AdjustmentController@save')->name('save');
    Route::post('ajax/{warehouseId}/{productId}/stock-qty', 'AdjustmentController@getStockQuantity')->name('get_stock_quantity');
  });
  Route::resource('adjustment', 'AdjustmentController')->only(resourceRouteMethods(false));

  // Purchase
  Route::prefix('purchase')->name('purchase.')->group(function () {
    Route::post('save', 'PurchaseController@save')->name('save');

    Route::get('{purchase}/invoice', 'PurchaseController@invoice')->name('invoice');
  });
  Route::resource('purchase', 'PurchaseController')->only(resourceRouteMethods());

  // Purchase Return
  Route::prefix('purchase-return')->name('purchase-return.')->group(function () {
    Route::get('/', 'PurchaseReturnController@index')->name('index');
    Route::get('/add/{id?}', 'PurchaseReturnController@add')->name('add');
    // Route::get('/create', 'PurchaseReturnController@create')->name('create');
    Route::POST('/save', 'PurchaseReturnController@save')->name('save');
  });

  // Sale
  Route::prefix('sale')->name('sale.')->group(function () {
    Route::post('{saleType}/save', 'SaleController@save')->name('save');
    Route::get('{sale}/invoice', 'SaleController@invoice')->name('invoice');
    Route::get('/group/{group_id}', 'SaleController@contactGroup')->name('contactGroup');
  });
  Route::resource('sale', 'SaleController')->only(resourceRouteMethods());

   // Sell Return
   Route::prefix('sell-return')->name('sell-return.')->group(function () {
    Route::get('/', 'SellReturnController@index')->name('index');
    Route::get('/add/{id?}', 'SellReturnController@add')->name('add');
    // Route::get('/create', 'PurchaseReturnController@create')->name('create');
    Route::POST('/save', 'SellReturnController@save')->name('save');
  });
  // Loan
  Route::prefix('loan')->name('loan.')->group(function () {
    Route::post('save/{loan?}', 'LoanController@save')->name('save');
    Route::post('ajax/payment-schedule', 'LoanController@getPaymentSchedule')->name('get_payment_schedule');
    Route::get('{schedule}/payment-schedule/edit', 'LoanController@editPaymentSchedule')->name('edit_payment_schedule');
    Route::post('{schedule}/payment-schedule/update', 'LoanController@updatePaymentSchedule')->name('update_payment_schedule');
    Route::post('change-status/{loan}/{status}', 'LoanController@changeStatus')->name('change_status');

    Route::get('{loan}/disburse', 'LoanController@disburse')->name('disburse');
    Route::get('{loan}/contract', 'LoanController@printContract')->name('print_contract');
    Route::get('{loan}/payment-schedule', 'LoanController@printPaymentSchedule')->name('print_payment_schedule');
    Route::get('{loan}/invoice', 'LoanController@invoice')->name('invoice');
    Route::get('{loan}/delay-schedule', 'LoanController@delaySchedule')->name('delaySchedule');
    Route::post('{loan}/delay-schedule', 'LoanController@delayScheduleSave')->name('saveDelaySchedule');

    Route::get('{scheduleReference}/get-delay-status', 'LoanController@getDelayStatus')->name('getDelayStatus');
    Route::post('{scheduleReference}/delayStatus', 'LoanController@delayStatus')->name('saveDelayStatus');
    Route::get('{scheduleReference}/get-schedule-history', 'LoanController@getScheduleHistory')->name('getScheduleHistory');
    Route::delete('delete/{scheduleReference}/delay-schedule', 'LoanController@delayScheduleDelete')->name('deleteDelaySchedule');

    Route::post('{loan}/update-note', 'LoanController@updateNote')->name('update_note');
    Route::post('wing-code', 'LoanController@generateWingCode')->name('wing-code');
  });
  Route::resource('loan', 'LoanController')->only(resourceRouteMethods());
  Route::get('/calculateloan', 'LoanController@calculateloan')->name('calculateloan');
  Route::prefix('loan')->name('loan.')->group(function () {
  });
  Route::resource('loan-cash', 'LoanCashController')->only(resourceRouteMethods());
  Route::prefix('loan-cash')->name('loan-cash.')->group(function () {
    Route::post('save/{loan?}', 'LoanCashController@save')->name('save');
    Route::POST('change-status/{loan}/{status}', 'LoanCashController@changeStatus')->name('change_status');
  });
  Route::get('collateral/create/{loan_id}', 'CollateralController@create')->name('collateral-create');
  Route::get('collateral/{id}/edit', 'CollateralController@edit')->name('collateral-edit');
  Route::POST('collateral/{loan_id?}', 'CollateralController@save')->name('collateral-save');
  Route::DELETE('collateral/{collateral}', 'CollateralController@destroy')->name('collateral.destroy');
  // Repayment
  Route::prefix('repayment')->name('repayment.')->group(function () {
    Route::get('/', 'RepaymentController@index')->name('index');
    Route::get('/list', 'RepaymentController@listRepayment')->name('list');
    Route::get('{id}/{repayType}', 'RepaymentController@show')->name('show');
    Route::post('{id}/save', 'RepaymentController@save')->name('save');
  });


  // Payment
  Route::prefix('payments')->name('payments.')->group(function() {
    Route::get('create/{id}', 'PaymentController@create')->name('create');
    Route::get('pay-contact-due/{contact_id}', 'PaymentController@getPayContactDue')->name('getPayContactDue');
    Route::post('pay-contact-due', 'PaymentController@postPayContactDue')->name('savePayContactDue');
    Route::get('show/{id}', 'PaymentController@show')->name('show');
    Route::get('/view-payment/{payment_id}', 'PaymentController@viewPayment')->name('viewPayment');
    Route::get('payment-date/{payment}', 'PaymentController@editPaymentDate')->name('editPaymentDate');
    Route::post('payment-date/{payment}', 'PaymentController@savePaymentDate')->name('savePaymentDate');

    Route::post('save/{id}', 'PaymentController@save')->name('save');
    Route::delete('delete/{id}', 'PaymentController@destroy')->name('destroy');
    Route::get('/{id}/{repayType}', 'DepreciationController@show')->name('paydepreciation');
    Route::get('/{id}/save', 'DepreciationController@save')->name('savedepreciation');
  });
  // Route::resource('payments', 'PaymentController')->only(resourceRouteMethods());

  // Agent commission payment
  Route::prefix('commission-payment')->name('commission-payment.')->group(function () {
    Route::get('', 'CommissionPaymentController@index')->name('index');
    Route::post('save', 'CommissionPaymentController@save')->name('save');
    Route::post('{staffId}/get-commission', 'CommissionPaymentController@getAgentCommissionInfo')->name('get_agent_commission_info');
  });

  // User
  Route::get('profile/{user}', 'UserController@showProfile')->name('user.show_profile');
  Route::post('save-profile/{user}', 'UserController@saveProfile')->name('user.save_profile');
  Route::post('user/save/{user?}', 'UserController@save')->name('user.save');
  Route::resource('user', 'UserController')->only(resourceRouteMethods(false));
  Route::resource('role', 'RoleController');

  // Report
  Route::prefix('report')->name('report.')->group(function () {
    Route::get('loan/{status}', 'ReportController@loan')->name('loan');
    Route::get('disbursed-loan', 'ReportController@disbursedLoan')->name('disbursed_loan');
    Route::get('overdue-loan', 'ReportController@overdueLoan')->name('overdue_loan');
    Route::get('financial-statement', 'ReportController@financialStatement')->name('financial_statement');
    Route::get('client-payment', 'ReportController@clientPayment')->name('client_payment');
    Route::get('cash-income', 'ReportController@cash_income')->name('cash-income');
    Route::get('client-payment/{invoice}/receipt', 'ReportController@clientPaymentReceipt')->name('client_payment_receipt');
    Route::get('client-registration', 'ReportController@clientRegistration')->name('client_registration');
    Route::get('loan-portfolio/{client}', 'ReportController@loanPortfolio')->name('loan_portfolio');
    Route::get('commission-payment', 'ReportController@commissionPayment')->name('commission_payment');

    Route::get('agent', 'ReportController@agent')->name('agent');
    Route::get('agent/{agent}', 'ReportController@agentDetail')->name('agent_detail');
    Route::get('agent-commission', 'ReportController@agentCommission')->name('agent_commission');

    Route::get('profit-loss', 'ReportController@profitLoss')->name('profit-loss');
    Route::get('stock', 'ReportController@productStock')->name('stock');
    Route::get('product-sell', 'ReportController@productSell')->name('product-sell');
    Route::get('product-purchase', 'ReportController@productPurchase')->name('product-purchase');
    Route::get('purchase', 'ReportController@purchase')->name('purchase');
    Route::get('sell', 'ReportController@sell')->name('sell');

    Route::get('purchase-sale', 'ReportController@purchaseSaleReport')->name('purchase-sale');
    Route::get('cash-recieved', 'ReportController@cashRecieved')->name('cash-recieved');
  });

  // Setting
  Route::prefix('setting')->group(function () {
    Route::get('general', 'GeneralSettingController@index')->name('general_setting.index');
    Route::post('general/save', 'GeneralSettingController@save')->name('general_setting.save');
    Route::resource('method-payments', 'MethodPaymentController')->only(resourceRouteMethods());
    Route::post('/method-payments/save/{methodPayment?}', 'MethodPaymentController@store')->name('method_payment.save');
  });
});
