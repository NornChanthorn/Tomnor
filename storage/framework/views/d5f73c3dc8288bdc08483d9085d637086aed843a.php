<?php $__env->startSection('title', trans('app.dashboard')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php if($view_data): ?>
    
    <ul class="nav justify-content-end" id="myTab" role="tablist">
        <li class="nav-item mr-2 w-25">
            <select name="location" id="location" class="form-control select2">
                <option value=""><?php echo e(trans('app.all_branches')); ?></option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>" <?php echo e(request('location') == $location->id ? 'selected' : ''); ?>>
                    <?php echo e($location->location); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </li>

        <li class="nav-item mr-2">
            <a class="btn btn-success nav-link active" id="today-tab" data-toggle="tab" href="#today" role="tab" aria-controls="today" aria-selected="true"><?php echo e(trans('app.today')); ?></a>
        </li>
        <li class="nav-item mr-2">
            <a class="btn btn-success nav-link" id="weekly-tab" data-toggle="tab" href="#weekly" role="tab" aria-controls="weekly" aria-selected="true"><?php echo e(trans('app.this_week')); ?></a>
        </li>
        <li class="nav-item mr-2">
            <a class="btn btn-success nav-link" id="month-tab" data-toggle="tab" href="#month" role="tab" aria-controls="month" aria-selected="false"><?php echo e(trans('app.this_month')); ?></a>
        </li>
        <li class="nav-item">
            <a class="btn btn-success nav-link" id="year-tab" data-toggle="tab" href="#year" role="tab" aria-controls="year" aria-selected="false"><?php echo e(trans('app.this_year')); ?></a>
        </li>
    </ul>
    <div class="row mt-4">
        <div class="col-sm-6 col-lg-3">
            <a href="">
                <div class="widget-small primary coloured-icon">
                    <i class="icon fa fa-money fa-3x"></i>
                    <div class="info">
                        <h6><?php echo e(trans('app.total_purchase_amount')); ?></h6>
                        <b id="totalPurchaseAmount">0</b>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="">
                <div class="widget-small info coloured-icon">
                    <i class="icon fa fa-money fa-3x"></i>
                    <div class="info">
                        <h6><?php echo e(trans('app.total_sale_amount')); ?></h6>
                        <b id="totalSellAmount">0</b>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="">
                <div class="widget-small warning coloured-icon">
                    <i class="icon fa fa-money fa-3x"></i>
                    <div class="info">
                        <h6><?php echo e(trans('app.total_due_purchase_amount')); ?></h6>
                        <b id="totalDuePurchaseAmount">0</b>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="">
                <div class="widget-small success coloured-icon">
                    <i class="icon fa fa-money fa-3x"></i>
                    <div class="info">
                        <h6><?php echo e(trans('app.client_due')); ?></h6>
                        <b id="totalDueSaleAmount">0</b>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="">
                <div class="widget-small info coloured-icon">
                    <i class="icon fa fa-money fa-3x"></i>
                    <div class="info">
                        <h6><?php echo e(trans('app.total_net_revenue')); ?></h6>
                        <b id="totalNetRevenueAmount">0</b>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="">
                <div class="widget-small warning coloured-icon">
                    <i class="icon fa fa-product-hunt fa-3x"></i>
                    <div class="info">
                        <h6><?php echo e(trans('app.total_product')); ?></h6>
                        <b id="totalProductQTY">0 PCs</b>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="">
                <div class="widget-small success coloured-icon">
                    <i class="icon fa fa-user-circle fa-3x"></i>
                    <div class="info">
                        <h6><?php echo e(trans('app.total_client')); ?></h6>
                        <b id="totalClient">0</b>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="">
                <div class="widget-small danger coloured-icon">
                    <i class="icon fa fa-money fa-3x"></i>
                    <div class="info">
                        <h6><?php echo e(trans('app.total_expense')); ?></h6>
                        <b id="totalExpenseAmount">0</b>
                    </div>
                </div>
            </a>
        </div>
    </div>
   
    <div class="row">
        <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title mb-0"><?php echo e(trans('app.purchase_sale_year') . ' ' . date('Y')); ?></h5>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <strong>$ <?php echo e(decimalNumber($totalPaidAmount, true)); ?></strong>
                  </p>
                </div>

                
                <div class="position-relative mb-4">
                  <canvas id="income-chart" height="330"></canvas>
                </div>

                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fa fa-square text-interest-chart"></i> <?php echo e(trans('app.sell')); ?>

                  </span>
                  <span>
                    <i class="fa fa-square text-principal-chart"></i> <?php echo e(trans('app.purchase')); ?>

                  </span>
                </div>
              </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><?php echo e(trans('app.stock_price')); ?></h5>
            </div>
            <div class="card-body">
                <div class="position-relative">
                <canvas class="" id="loan-chart" height="300"></canvas>
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?php echo e(trans('app.sale_due_payment')); ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <td><?php echo e(trans('app.no_sign')); ?></td>
                                    <th><?php echo e(trans('app.client_name')); ?></th>
                                    <th><?php echo e(trans('app.invoice_number')); ?></th>
                                    <th><?php echo e(trans('app.amount')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $saleDuePayment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($key+1); ?></td>
                                        <td>
                                            <?php echo e($item->name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($item->invoice_no); ?>

                                        </td>
                                        <td>
                                            $ <?php echo e(decimalNumber($item->due_amount,2)); ?>

                                        </td>
                                    </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?php echo e(trans('app.purchase_due_payment')); ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo e(trans('app.no_sign')); ?></th>
                                    <th><?php echo e(trans('app.name')); ?><?php echo e(trans('app.supplier')); ?></th>
                                    <th><?php echo e(trans('app.reference_number')); ?></th>
                                    <th><?php echo e(trans('app.amount')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $purchaseDuePayment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($key +1); ?></td>
                                        <td>
                                            <?php echo e($item->name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($item->ref_no); ?>

                                        </td>
                                        <td>
                                            $ <?php echo e(decimalNumber($item->final_total-$item->due_amount,2)); ?>

                                        </td>
                                    </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?php echo e(trans('app.alert_quantity')); ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo e(trans('app.no_sign')); ?></th>
                                    <th><?php echo e(trans('app.product_name')); ?></th>
                                    <th><?php echo e(trans('app.branch')); ?></th>
                                    <th><?php echo e(trans('app.quantity')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $alertQty; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($key +1); ?></td>
                                        <td>
                                            <?php echo e($item->pname); ?>

                                        </td>
                                        <td>
                                            <?php echo e($item->bname); ?>

                                        </td>
                                        <td>
                                            <?php echo e($item->qty_available); ?>

                                        </td>

                                    </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?php echo e(trans('app.total_customer_today')); ?> (<?php echo e(KhmerDate(date('d-m-Y'))); ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo e(trans('app.no_sign')); ?></th>
                                    <th><?php echo e(trans('app.invoice_number')); ?></th>
                                    <th><?php echo e(trans('app.client_name')); ?></th>
                                    <th><?php echo e(trans('app.amount')); ?></th>
                                    <th><?php echo e(trans('app.date')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $saleToday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($key +1); ?></td>
                                        <td><?php echo e($item->invoice_no); ?></td>
                                        <td>
                                            <?php echo e($item->name); ?>

                                        </td>
                                        <td>
                                            $ <?php echo e(decimalNumber($item->payment_amount,2)); ?>


                                        </td>
                                        <td>
                                            <?php echo e(displayDate($item->payment_date)); ?>

                                        </td>
                                    </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
      <h4></h4>
    <?php endif; ?>
  </main>
<?php $__env->stopSection(); ?>

<?php if($view_data): ?>
  <?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/Chart.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dashboard2.js')); ?>"></script>
    <script>
        $(function () {
            var ticksStyle = {
            fontFamily: 'Battambang, Verdana, Roboto, Arial, sans-serif',
            fontColor: '#495057',
            fontStyle: '600',
            };
            var mode = 'index';
            var intersect = true;
            var khmerMonths = <?php echo json_encode(khmerMonths()) ?>;
            var totalpurchase = <?php echo json_encode($totalPurchase) ?>;
            var totalsell = <?php echo json_encode($totalSell) ?>;

            var incomeChart = new Chart($('#income-chart'), {
            type: 'bar',
            data: {
                labels: khmerMonths,
                datasets: [
                // Paid interest
                {
                    backgroundColor: '#28a745',
                    borderColor: '#28a745',
                    // data: [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120],
                    data: totalsell,
                },
                // Paid principal
                {
                    backgroundColor: '#007bff',
                    borderColor: '#007bff',
                    // data: [8, 15, 20, 33, 50, 61, 65, 77, 95, 96, 101, 105],
                    data: totalpurchase,
                }
                ]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                mode: mode,
                intersect: intersect,
                },
                hover: {
                mode: mode,
                intersect: intersect,
                },
                legend: {
                display: false
                },
                scales: {
                yAxes: [{
                    // display: false,
                    gridLines: {
                    display: true,
                    lineWidth: '4px',
                    color: 'rgba(0, 0, 0, .2)',
                    zeroLineColor: 'transparent',
                    },
                    ticks: $.extend({
                    beginAtZero: true,

                    // Include a dollar sign in the ticks
                    callback: function (value, index, values) {
                        if (value >= 1000) {
                        value /= 1000;
                        value += 'k';
                        }
                        return '$ ' + value;
                    }
                    }, ticksStyle),
                }],
                xAxes: [{
                    display: true,
                    gridLines: {
                    display: false
                    },
                    ticks: ticksStyle,
                }]
                }
            }
            });

            let loanChart = <?php echo json_encode($stockChartData); ?>;
            var pieChart = new Chart($("#loan-chart"), {
            type: 'pie',
            data: {
                labels: loanChart.labels,
                datasets: [{
                data: loanChart.data,
                backgroundColor: loanChart.colors,
                labels: "",
                }]

            },
            options: {
                legend: {
                //   align: 'start',
                labels: {
                    fontFamily: 'Battambang, sans-serif',
                    fontColor: '#495057',
                    fontStyle: '600'
                }
                }
            }
            });
        });
        
    </script>
  <?php $__env->stopSection(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>