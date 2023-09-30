<?php $__env->startSection('title', trans('app.loan')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.loan_report') . ' - ' . reportLoanStatuses($status)); ?></h3>

      <form method="get" action="">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <?php if(isAdmin() || empty(auth()->user()->staff)): ?>
                <div class="col-sm-6 col-md-4 form-group">
                  <label for="branch" class="control-label"><?php echo e(trans('app.branch')); ?></label>
                  <select name="branch" id="branch" class="form-control select2">
                    <option value=""><?php echo e(trans('app.all_branches')); ?></option>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch') == $branch->id ? 'selected' : ''); ?>>
                        <?php echo e($branch->location); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
                <div class="col-sm-6 col-md-4 form-group">
                  <label for="agent" class="control-label"><?php echo e(trans('app.agent')); ?></label>
                  <select name="agent" class="form-control select2">
                    <option value=""><?php echo e(trans('app.agent')); ?></option>
                    <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($agent->id); ?>" <?php echo e(request('agent') == $agent->id ? 'selected' : ''); ?>>
                        <?php echo e($agent->name); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
              <?php endif; ?>
              <div class="col-sm-6 col-md-4">
                <label for="branch" class="control-label"><?php echo e(trans('app.search')); ?></label>
                <input type="text" name="q" class="form-control" value="<?php echo e(request('q') ?? ''); ?>" placeholder="<?php echo e(__('app.search-client-code')); ?>">
              </div>
              <div class="col-lg-12 text-right">
                <?php echo $__env->make('partial.button-search', ['class' => 'btn-lg'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
              </div>
            </div>
          </div>
        </div>
      </form>
      <br>

      <div class="row">
        <div class="col-sm-6 col-lg-3">
          <a href="<?php echo e(route('report.loan', 'all')); ?>">
            <div class="widget-small primary coloured-icon">
              <i class="icon fa fa-money fa-3x"></i>
              <div class="info">
                <h6><?php echo e(trans('app.loan_list')); ?></h6>
                <b><?php echo e($loanCount); ?></b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="<?php echo e(route('report.loan', ReportLoanStatus::PENDING)); ?>">
            <div class="widget-small warning coloured-icon">
              <i class="icon fa fa-money fa-3x"></i>
              <div class="info">
                <h6><?php echo e(trans('app.pending_loan')); ?></h6>
                <b><?php echo e($pendingLoanCount); ?></b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="<?php echo e(route('report.loan', ReportLoanStatus::ACTIVE)); ?>">
            <div class="widget-small primary coloured-icon">
              <i class="icon fa fa-money fa-3x"></i>
              <div class="info">
                <h6><?php echo e(trans('app.active_loan')); ?></h6>
                <b><?php echo e($activeLoanCount); ?></b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="<?php echo e(route('report.overdue_loan')); ?>">
            <div class="widget-small warning coloured-icon">
              <i class="icon fa fa-clock-o fa-3x"></i>
              <div class="info">
                <h6><?php echo e(trans('app.overdue_loan')); ?></h6>
                <b><?php echo e($overdueLoanCount); ?></b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="<?php echo e(route('report.loan', ReportLoanStatus::PAID)); ?>">
            <div class="widget-small success coloured-icon">
              <i class="icon fa fa-money fa-3x"></i>
              <div class="info">
                <h6><?php echo e(trans('app.paid_loan')); ?></h6>
                <b><?php echo e($paidLoanCount); ?></b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="<?php echo e(route('report.loan', ReportLoanStatus::REJECTED)); ?>">
            <div class="widget-small danger coloured-icon">
              <i class="icon fa fa-ban fa-3x"></i>
              <div class="info">
                <h6><?php echo e(trans('app.rejected_loan')); ?></h6>
                <b><?php echo e($rejectedLoanCount); ?></b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="<?php echo e(route('report.client_registration')); ?>">
            <div class="widget-small success coloured-icon">
              <i class="icon fa fa-address-book fa-3x"></i>
              <div class="info">
                <h6><?php echo e(trans('app.client')); ?></h6>
                <b><?php echo e($clientCount); ?></b>
              </div>
            </div>
          </a>
        </div>
      </div>
      <hr>

      <?php echo $__env->make('partial.flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <div class="table-responsive resize-w">
        <table class="table table-hover table-bordered">
          <?php
            $isRejectedLoan = ($status == ReportLoanStatus::REJECTED);
            $statusTitle = reportLoanStatuses($status);

            switch ($status) {
              case ReportLoanStatus::PENDING:
                $labelClass = 'badge badge-warning';
                break;
              case ReportLoanStatus::ACTIVE:
                $labelClass = 'badge badge-info';
                break;
              case ReportLoanStatus::PAID:
                $labelClass = 'badge badge-success';
                break;
              case ReportLoanStatus::REJECTED:
                $labelClass = 'badge badge-danger';
                break;
              default:
                $labelClass = '';
                break;
            }
          ?>

          <thead>
            <tr>
              <th><?php echo e(trans('app.no_sign')); ?></th>
              <th><?php echo e(trans('app.client')); ?></th>
              <th><?php echo e(trans('app.profile_photo')); ?></th>

              <th><?php echo e(trans('app.first_phone')); ?></th>
              <th style="width: 10%"><?php echo e(trans('app.occupation_1')); ?></th>
              <th><?php echo e(trans('app.province')); ?></th>
              <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('account_number', trans('app.loan_code')));?></th>
              <th><?php echo e(trans('app.branch')); ?></th>
              
              <?php if(isAdmin()): ?>
                <th><?php echo e(trans('app.agent')); ?></th>
              <?php endif; ?>

              <th><?php echo e(trans('app.product')); ?></th>
              <th>
                <?php echo e(__('app.loan_amount')); ?>

              </th>
              <th><?php echo e(trans('app.request_date')); ?></th>
              <th><?php echo e(trans('app.status')); ?></th>

              <?php if(isAdmin() && $isRejectedLoan): ?>
                <th><?php echo e(trans('app.action')); ?></th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $filteredLoans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($offset++); ?></td>
                <td><?php echo $__env->make('partial.client-detail-link', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                <td><?php echo $__env->make('partial.client-profile-photo', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>


                <td><?php echo e(@$loan->client->first_phone); ?><?php echo e(@$loan->client->second_phone ? ', '.@$loan->client->second_phone : ''); ?></td>
                <td><?php echo e(@$loan->client->occupation_1); ?></td>
                <td><?php echo e(@$loan->client->province->khmer_name ?? @$loan->client->province->name); ?></td>
                <td>
                  <?php if($isRejectedLoan): ?>
                    <?php echo e($loan->client_code); ?>

                  <?php else: ?>
                    <?php echo $__env->make('partial.loan-detail-link', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php endif; ?>
                </td>
                <td><?php echo e(@$loan->branch->location ?? trans('app.n/a')); ?></td>
                

                <?php if(isAdmin()): ?>
                  <td>
                    <?php if(@$loan->staff): ?>
                      	<?php echo $__env->make('partial.staff-detail-link', ['staff' => $loan->staff], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>   
                    <?php endif; ?>
              
                  </td>
                <?php endif; ?>

                <td>
                  <?php $__currentLoopData = $loan->productDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('partial.product-detail-link', ['product' => $item->product, 'variantion' => $item->variantion->name], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?><br>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </td>
                <td>
                  <?php echo e(num_f($loan->loan_amount)); ?>

                </td>
                <td>
                  <?php echo e(displayDate($loan->created_at)); ?>

                </td>
                <td><label class="<?php echo e($labelClass); ?>"><?php echo e($statusTitle); ?></label></td>

                <?php if(isAdmin() && $isRejectedLoan): ?>
                  <td class="text-center">
                    
                    <button type="button" class="btn btn-primary btn-sm btn-revert mb-1" data-redirect-url="<?php echo e(route('loan.show', $loan->id)); ?>" data-revert-url="<?php echo e(route('loan.change_status', [$loan->id, LoanStatus::PENDING])); ?>">
                      <?php echo e(trans('app.revert')); ?>

                    </button>

                    
                    <button type="button" class="btn btn-danger btn-sm btn-delete mb-1" data-url="<?php echo e(route('loan.destroy', $loan->id)); ?>">
                      <?php echo e(trans('app.delete')); ?>

                    </button>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
        <?php echo $filteredLoans->appends(Request::except('page'))->render(); ?>

      </div>
    </div>
  </main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script src="<?php echo e(asset('js/rejected-loan.js')); ?>"></script>
  <script src="<?php echo e(asset('js/delete-item.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>