<aside class="app-sidebar">
  <div class="app-sidebar__user">
    <img class="app-sidebar__user-avatar" src="<?php echo e(asset('/user.png')); ?>" alt="">
    <div>
      <p class="app-sidebar__user-name"><?php echo e(Auth::user()->name); ?></p>
    </div>
  </div>

  <!-- Sidebar Block -->
  <ul class="app-menu">
    
    <?php if (\Entrust::can('dashboard')) : ?>
    <li>
      <a class="app-menu__item <?php echo e(activeMenu('dashboard')); ?>" href="<?php echo e(route('dashboard')); ?>">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label"><?php echo e(trans('app.dashboard')); ?></span>
      </a>
    </li>

    <li>
        <a class="app-menu__item <?php echo e(activeMenu('dashboard2')); ?>" href="<?php echo e(route('dashboard2')); ?>">
          <i class="app-menu__icon fa fa-dashboard"></i>
          <span class="app-menu__label"><?php echo e(trans('app.dashboard2')); ?></span>
        </a>
    </li>
    <?php endif; // Entrust::can ?>


    <?php if (\Entrust::can('supplier.browse','contact.browse')) : ?>
    <li class="treeview <?php echo e(activeTreeviewMenu(['contact'])); ?>">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-address-book"></i>
        <span class="app-menu__label"><?php echo e(trans('app.contact')); ?></span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>

      <ul class="treeview-menu">
        <?php if (\Entrust::can('supplier.browse')) : ?>
        
 
        <li>
          <a class="treeview-item <?php echo e(Request::get('type')=='supplier' ? 'active' : ''); ?>" href="<?php echo e(route('contact.index', ['type'=>'supplier'])); ?>">
            <i class="icon fa fa-address-book"></i><?php echo e(trans('app.supplier')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
        
        <?php if (\Entrust::can('contact.browse')) : ?>
        <li>
          <a class="treeview-item <?php echo e(Request::get('type')=='customer' ? 'active' : ''); ?>" href="<?php echo e(route('contact.index', ['type'=>'customer'])); ?>">
            <i class="icon fa fa-address-book"></i><?php echo e(trans('app.customer')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
        <?php if (\Entrust::can('contact_group.browse')) : ?>
        <li>
            <a class="treeview-item <?php echo e(activeMenu('contact/group')); ?>" href="<?php echo e(route('contact.group.index')); ?>">
              <i class="icon fa fa-address-book"></i><?php echo e(trans('app.contact_group')); ?>

            </a>
        </li>
        <?php endif; // Entrust::can ?>
      </ul>
    </li>
    <?php endif; // Entrust::can ?>

    <?php if(auth()->user()->can(['product.browse', 'product-type.browse', 'brand.browse'])): ?>
    
    <li class="treeview <?php echo e(activeTreeviewMenu(['product', 'product-category', 'brand','product-units'])); ?>">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-product-hunt"></i>
        <span class="app-menu__label"><?php echo e(trans('app.product')); ?></span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>

      <ul class="treeview-menu">
        <?php if (\Entrust::can('product.browse')) : ?>
        
        <li>
          <a class="treeview-item <?php echo e(activeMenu('product')); ?>" href="<?php echo e(route('product.index')); ?>">
            <i class="icon fa fa-product-hunt pr-1"></i><?php echo e(trans('app.product')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>

        <li>
          <a class="treeview-item <?php echo e(activeMenu('product/barcode')); ?>" href="<?php echo e(route('product.barcode')); ?>">
            <i class="icon fa fa-qrcode pr-1"></i><?php echo e(trans('app.product-barcode')); ?>

          </a>
        </li>

        <li>
          <a class="treeview-item <?php echo e(activeMenu('product/ime')); ?>" href="<?php echo e(route('product.ime')); ?>">
            <i class="icon fa fa-sticky-note-o pr-1"></i><?php echo e(trans('app.product_ime')); ?>

          </a>
        </li>
        <?php if (\Entrust::can('product-type.browse')) : ?>
        
        <li>
          <a class="treeview-item <?php echo e(activeMenu('product-category')); ?>" href="<?php echo e(route('product_category.index')); ?>">
            <i class="icon fa fa-indent pr-1"></i><?php echo e(trans('app.product_category')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
        <?php if (\Entrust::can('product-type.browse')) : ?>
        
        <li>
          <a class="treeview-item <?php echo e(activeMenu('product-units')); ?>" href="<?php echo e(route('product-units.index')); ?>">
            <i class="icon fa fa-indent pr-1"></i><?php echo e(trans('app.unit')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
        <?php if (\Entrust::can('brand.browse')) : ?>
        
        <li>
          <a class="treeview-item <?php echo e(activeMenu('brand')); ?>" href="<?php echo e(route('brand.index')); ?>">
            <i class="icon fa fa-bandcamp pr-1"></i><?php echo e(trans('app.brand')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
      </ul>
    </li>
    <?php endif; ?>

    <?php if(auth()->user()->can(['loan.browse', 'loan-cash.browse', 'customer.browse', 'loan.pay', 'staff.commission', 'loan.calculateloan'])): ?>
      <li class="treeview <?php echo e(activeTreeviewMenu(['client', 'loan','loan-cash', 'repayment', 'commission-payment','calculateloan'])); ?>">
        <a class="app-menu__item" href="#" data-toggle="treeview">
          <i class="app-menu__icon fa fa-money"></i>
          <span class="app-menu__label"><?php echo e(trans('app.loan')); ?></span>
          <i class="treeview-indicator fa fa-angle-left"></i>
        </a>

        <ul class="treeview-menu">
          <?php if (\Entrust::can('customer.browse')) : ?>
            
            <li>
              <a class="treeview-item <?php echo e(activeMenu('client')); ?>" href="<?php echo e(route('client.index')); ?>">
                <i class="app-menu__icon fa fa-address-book" aria-hidden="true"></i>
                <span class="app-menu__label"><?php echo e(trans('app.client')); ?></span>
              </a>
            </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('loan.browse')) : ?>
            
            <li>
              <a class="treeview-item <?php echo e(activeMenu('loan')); ?>" href="<?php echo e(route('loan.index')); ?>">
                <i class="icon fa fa-money pr-1"></i><?php echo e(trans('app.loan_product')); ?>

              </a>
            </li>
            
           
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('loan-cash.browse')) : ?>
            <li>
              <a class="treeview-item <?php echo e(activeMenu('loan-cash')); ?>" href="<?php echo e(route('loan-cash.index')); ?>">
                <i class="icon fa fa-money pr-1"></i><?php echo e(trans('app.loan_cash')); ?>

              </a>
            </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('loan.pay')) : ?>
            
            <li>
              <a class="treeview-item <?php echo e(activeMenu('repayment')); ?>" href="<?php echo e(route('repayment.index')); ?>">
                <i class="icon fa fa-credit-card pr-1"></i><?php echo e(trans('app.payment')); ?>

              </a>
            </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('staff.commission')) : ?>
            
            <li>
              <a class="treeview-item <?php echo e(activeMenu('commission-payment')); ?>" href="<?php echo e(route('commission-payment.index')); ?>">
                <i class="icon fa fa-credit-card pr-1"></i><?php echo e(trans('app.commission_payment')); ?>

              </a>
            </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('loan.calculateloan')) : ?>
            
            <li>
              <a class="treeview-item <?php echo e(activeMenu('calculateloan')); ?>" href="<?php echo e(route('calculateloan')); ?>">
                <i class="icon fa  fa-calculator pr-1"></i><?php echo e(trans('app.calculate_loan')); ?>

              </a>
            </li>
          <?php endif; // Entrust::can ?>
        </ul>
      </li>
    <?php endif; ?>

    <?php if (\Entrust::can('sale.browse')) : ?>
      <li class="treeview <?php echo e(activeTreeviewMenu(['sale'])); ?>">
        <a class="app-menu__item" href="#" data-toggle="treeview">
          <i class="app-menu__icon fa fa-money"></i>
          <span class="app-menu__label"><?php echo e(trans('app.sale')); ?></span>
          <i class="treeview-indicator fa fa-angle-left"></i>
        </a>

        <ul class="treeview-menu">
          <li>
            <a class="treeview-item  <?php echo e(request()->is('sale/create') ? 'active' : ''); ?>" href="<?php echo e(route('sale.create')); ?>">
              <i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i>
              <span class="app-menu__label"><?php echo e(trans('app.create')); ?> <?php echo e(trans('app.sale')); ?></span>
            </a>
          </li>
          <li>
            <a class="treeview-item  <?php echo e(request()->is('sale') ? 'active' : ''); ?>" href="<?php echo e(route('sale.index')); ?>">
              <i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i>
              <span class="app-menu__label"><?php echo e(trans('app.sale')); ?><?php echo e(trans('app.all')); ?></span>
            </a>
          </li>
          <?php $__currentLoopData = groupContacts()->where('type','customer'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
              <a class="treeview-item  <?php echo e(request()->is('sale/group/'.$item->id) ? 'active' : ''); ?>" href="<?php echo e(route('sale.contactGroup',$item->id)); ?>">
                <i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i>
                <span class="app-menu__label"><?php echo e($item->name); ?></span>
              </a>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          
        </ul>
      </li>
    
    <?php endif; // Entrust::can ?>
    <?php if (\Entrust::can('purchase-return.browse')) : ?>
    <li>
      <a class="app-menu__item <?php echo e(activeMenu('sell-return')); ?>" href="<?php echo e(route('sell-return.index')); ?>">
        <i class="app-menu__icon fa fa-reply" aria-hidden="true"></i>
        <span class="app-menu__label"><?php echo e(trans('app.sell-return')); ?></span>
      </a>
    </li>
    <?php endif; // Entrust::can ?>
    <?php if (\Entrust::can('po.browse')) : ?>
    <li>
      <a class="app-menu__item <?php echo e(activeMenu('purchase')); ?>" href="<?php echo e(route('purchase.index')); ?>">
        <i class="app-menu__icon fa fa-cart-plus" aria-hidden="true"></i>
        <span class="app-menu__label"><?php echo e(trans('app.purchase')); ?></span>
      </a>
    </li>
    <?php endif; // Entrust::can ?>
    <?php if (\Entrust::can('purchase-return.browse')) : ?>
    <li>
      <a class="app-menu__item <?php echo e(activeMenu('purchase-return')); ?>" href="<?php echo e(route('purchase-return.index')); ?>">
        <i class="app-menu__icon fa fa-reply" aria-hidden="true"></i>
        <span class="app-menu__label"><?php echo e(trans('app.purchase_return')); ?></span>
      </a>
    </li>
    <?php endif; // Entrust::can ?>
    <?php if (\Entrust::can('stock.transfer.browse')) : ?>
    <li>
      <a class="app-menu__item <?php echo e(activeMenu('transfer')); ?>" href="<?php echo e(route('transfer.index')); ?>">
        <i class="app-menu__icon fa fa-exchange" aria-hidden="true"></i>
        <span class="app-menu__label"><?php echo e(trans('app.stock_transfer')); ?></span>
      </a>
    </li>
    <?php endif; // Entrust::can ?>

    <?php if (\Entrust::can('stock.adjust.browse')) : ?>
    <li>
      <a class="app-menu__item <?php echo e(activeMenu('adjustment')); ?>" href="<?php echo e(route('adjustment.index')); ?>">
        <i class="app-menu__icon fa fa-adjust" aria-hidden="true"></i>
        <span class="app-menu__label"><?php echo e(trans('app.stock_adjustment')); ?></span>
      </a>
    </li>
    <?php endif; // Entrust::can ?>

    <?php if(auth()->user()->can(['report.loan-approval', 'report.loan-expired', 'report.loan', 'report.financial', 'report.customer', 'report.payment', 'report.agent', 'report.commission-pay', 'report.product-sell'])): ?>
      
      <li class="treeview <?php echo e(activeTreeviewMenu('report')); ?>">
        <a class="app-menu__item" href="#" data-toggle="treeview">
          <i class="app-menu__icon fa fa-book"></i>
          <span class="app-menu__label"><?php echo e(trans('app.report')); ?></span>
          <i class="treeview-indicator fa fa-angle-left"></i>
        </a>

        <ul class="treeview-menu">
          <?php if (\Entrust::can('report.loan-approval')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('disbursed-loan', 2)); ?>" href="<?php echo e(route('report.disbursed_loan')); ?>">
              <i class="icon fa fa-list-alt pr-1"></i><?php echo e(trans('app.loan_disbursement')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('report.loan-expired')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('overdue-loan', 2)); ?>" href="<?php echo e(route('report.overdue_loan')); ?>">
              <i class="icon fa fa-clock-o pr-1"></i><?php echo e(trans('app.overdue_loan')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('report.loan')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('loan', 2)); ?>" href="<?php echo e(route('report.loan', ReportLoanStatus::PENDING)); ?>">
              <i class="icon fa fa-money pr-1"></i><?php echo e(trans('app.loan')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('report.financial')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('financial-statement', 2)); ?>" href="<?php echo e(route('report.financial_statement')); ?>">
              <i class="icon fa fa-credit-card-alt pr-1"></i><?php echo e(trans('app.financial_statement')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('report.cashincome')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('cash-income', 2)); ?>" href="<?php echo e(route('report.cash-income')); ?>">
              <i class="icon fa fa-money pr-1"></i><?php echo e(trans('app.cash_income_report')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('report.cash-recieved')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('cash-recieved', 2)); ?>" href="<?php echo e(route('report.cash-recieved')); ?>">
              <i class="icon fa fa-money pr-1"></i><?php echo e(trans('app.cash_recieved_report')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>

          <?php if (\Entrust::can('report.customer')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu(['client-registration', 'loan-portfolio'], 2)); ?>" href="<?php echo e(route('report.client_registration')); ?>">
              <i class="icon fa fa-address-book pr-1"></i><?php echo e(trans('app.client_registration')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('report.payment')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('client-payment', 2)); ?>" href="<?php echo e(route('report.client_payment')); ?>">
              <i class="icon fa fa-credit-card pr-1"></i><?php echo e(trans('app.payment')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('report.agent')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu(['agent', 'agent-commission'], 2)); ?>" href="<?php echo e(route('report.agent')); ?>">
              <i class="icon fa fa-address-book pr-1"></i><?php echo e(trans('app.agent')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('report.commission-pay')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('commission-payment', 2)); ?>" href="<?php echo e(route('report.commission_payment')); ?>">
              <i class="icon fa fa-credit-card pr-1"></i><?php echo e(trans('app.commission_payment')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('report.profit-loss')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('profit-loss', 2)); ?>" href="<?php echo e(route('report.profit-loss')); ?>">
              <i class="icon fa fa-money pr-1"></i><?php echo e(trans('app.profit_loss')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>

          <?php if (\Entrust::can('report.purchase')) : ?>
          <li>
            <a class="treeview-item <?php echo e(activeMenu('purchase', 2)); ?>" href="<?php echo e(route('report.purchase')); ?>">
              <i class="icon fa fa-cart-plus pr-1"></i><?php echo e(trans('app.purchase_report')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>

          <?php if (\Entrust::can('report.sell')) : ?>
          <li>
            <a class="treeview-item <?php echo e(activeMenu('sell', 2)); ?>" href="<?php echo e(route('report.sell')); ?>">
              <i class="icon fa fa-shopping-cart pr-1"></i><?php echo e(trans('app.sell_report')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>

          <?php if (\Entrust::can('report.stock')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('stock', 2)); ?>" href="<?php echo e(route('report.stock')); ?>">
              <i class="icon fa fa-database pr-1"></i><?php echo e(trans('app.product_stock_report')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
          <?php if (\Entrust::can('report.product-sell')) : ?>
          
          <li>
            <a class="treeview-item <?php echo e(activeMenu('product-sell', 2)); ?>" href="<?php echo e(route('report.product-sell')); ?>">
              <i class="icon fa fa-product-hunt pr-1"></i><?php echo e(trans('app.product_sell')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>

          <?php if (\Entrust::can('report.purchase-sale')) : ?>
          <li>
            <a class="treeview-item <?php echo e(activeMenu('purchase-sale', 2)); ?>" href="<?php echo e(route('report.purchase-sale')); ?>">
              <i class="icon fa fa-product-hunt pr-1"></i><?php echo e(trans('app.purchase_sale')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>
        </ul>
      </li>
    <?php endif; ?>
    <?php if (\Entrust::can('branch.browse')) : ?>
    
    <li>
      <a class="app-menu__item <?php echo e(activeMenu('branch')); ?>" href="<?php echo e(route('branch.index')); ?>">
        <i class="app-menu__icon fa fa-code-fork fa-lg" aria-hidden="true"></i>
        <span class="app-menu__label"><?php echo e(trans('app.branch')); ?></span>
      </a>
    </li>
    <?php endif; // Entrust::can ?>
    <?php if(auth()->user()->can(['expense.browse', 'expense_type.browse'])): ?>
    
    <li class="treeview <?php echo e(activeTreeviewMenu(['expense', 'expense-type'])); ?>">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-users"></i>
        <span class="app-menu__label"><?php echo e(trans('app.expenses')); ?></span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>
      <ul class="treeview-menu">
        <?php if (\Entrust::can('expense.browse')) : ?>
        <li>
          <a class="treeview-item <?php echo e(activeMenu('expense')); ?>" href="<?php echo e(route('expense.index')); ?>">
            <i class="icon fa fa-user pr-1"></i><?php echo e(trans('app.expense')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
        <?php if (\Entrust::can('expense_type.browse')) : ?>
        <li>
          <a class="treeview-item <?php echo e(activeMenu('expense-type')); ?>" href="<?php echo e(route('expense-type.index')); ?>">
            <i class="icon fa fa-bandcamp pr-1"></i><?php echo e(trans('app.expense_type')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
      </ul>
    </li>
    <?php endif; ?>
    <?php if(auth()->user()->can(['staff.browse', 'position.browse'])): ?>
    
    <li class="treeview <?php echo e(activeTreeviewMenu(['staff', 'position'])); ?>">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-users"></i>
        <span class="app-menu__label"><?php echo e(trans('app.staff')); ?></span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>
      <ul class="treeview-menu">
        <?php if (\Entrust::can('staff.browse')) : ?>
        <li>
          <a class="treeview-item <?php echo e(activeMenu('staff')); ?>" href="<?php echo e(route('staff.index')); ?>">
            <i class="icon fa fa-user pr-1"></i><?php echo e(trans('app.staff')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
        <?php if (\Entrust::can('position.browse')) : ?>
        <li>
          <a class="treeview-item <?php echo e(activeMenu('position')); ?>" href="<?php echo e(route('position.index')); ?>">
            <i class="icon fa fa-bandcamp pr-1"></i><?php echo e(trans('app.position')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
      </ul>
    </li>
    <?php endif; ?>
    <?php if(auth()->user()->can(['user.browse', 'role.browse'])): ?>
    
    <li class="treeview <?php echo e(activeTreeviewMenu(['user', 'role'])); ?>">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-users"></i>
        <span class="app-menu__label"><?php echo e(trans('app.user')); ?></span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>
      <ul class="treeview-menu">
        <?php if (\Entrust::can('user.browse')) : ?>
        <li>
          <a class="treeview-item <?php echo e(activeMenu('user')); ?>" href="<?php echo e(route('user.index')); ?>">
            <i class="icon fa fa-user pr-1"></i><?php echo e(trans('app.user')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
        <?php if (\Entrust::can('role.browse')) : ?>
        <li>
          <a class="treeview-item <?php echo e(activeMenu('role')); ?>" href="<?php echo e(route('role.index')); ?>">
            <i class="icon fa fa-briefcase pr-1"></i><?php echo e(trans('app.role')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
      </ul>
    </li>
    <?php endif; ?>
    <?php if (\Entrust::can('app.setting')) : ?>
    
    <li class="treeview <?php echo e(activeTreeviewMenu('setting')); ?>">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-gears"></i>
        <span class="app-menu__label"><?php echo e(trans('app.setting')); ?></span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>

      <ul class="treeview-menu">
        
        <li>
          <a class="treeview-item <?php echo e(activeMenu('general', 2)); ?>" href="<?php echo e(route('general_setting.index')); ?>">
            <i class="icon fa fa-gear pr-1"></i><?php echo e(trans('app.general_setting')); ?>

          </a>
        </li>
        <?php if (\Entrust::can('payment_method.browse')) : ?>
        <li>
          <a class="treeview-item <?php echo e(activeMenu('method-payments', 2)); ?>" href="<?php echo e(route('method-payments.index')); ?>">
            <i class="icon fa fa-gear pr-1"></i><?php echo e(trans('app.payment_method')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
        <?php if (\Entrust::can('collateral_type.browse')) : ?>
        <li>
          <a class="treeview-item <?php echo e(activeMenu('collateral-type')); ?>" href="<?php echo e(route('collateral-type.index')); ?>">
            <i class="icon fa fa-gear pr-1"></i><?php echo e(trans('app.collateral_type')); ?>

          </a>
        </li>
        <?php endif; // Entrust::can ?>
      </ul>
    </li>
    <?php endif; // Entrust::can ?>
  </ul>
</aside>
