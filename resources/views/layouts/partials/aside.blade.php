<aside class="app-sidebar">
  <div class="app-sidebar__user">
    <img class="app-sidebar__user-avatar" src="{{ asset('/user.png') }}" alt="">
    <div>
      <p class="app-sidebar__user-name">{{ Auth::user()->name }}</p>
    </div>
  </div>

  <!-- Sidebar Block -->
  <ul class="app-menu">
    {{-- Dashboard --}}
    @permission('dashboard')
    <li>
      <a class="app-menu__item {{ activeMenu('dashboard') }}" href="{{ route('dashboard') }}">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">{{ trans('app.dashboard') }}</span>
      </a>
    </li>

    <li>
        <a class="app-menu__item {{ activeMenu('dashboard2') }}" href="{{ route('dashboard2') }}">
          <i class="app-menu__icon fa fa-dashboard"></i>
          <span class="app-menu__label">{{ trans('app.dashboard2') }}</span>
        </a>
    </li>
    @endpermission


    @permission('supplier.browse','contact.browse')
    <li class="treeview {{ activeTreeviewMenu(['contact']) }}">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-address-book"></i>
        <span class="app-menu__label">{{ trans('app.contact') }}</span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>

      <ul class="treeview-menu">
        @permission('supplier.browse')
        {{-- Supplier --}}
 
        <li>
          <a class="treeview-item {{ Request::get('type')=='supplier' ? 'active' : '' }}" href="{{ route('contact.index', ['type'=>'supplier']) }}">
            <i class="icon fa fa-address-book"></i>{{ trans('app.supplier') }}
          </a>
        </li>
        @endpermission
        {{-- Customer --}}
        @permission('contact.browse')
        <li>
          <a class="treeview-item {{ Request::get('type')=='customer' ? 'active' : '' }}" href="{{ route('contact.index', ['type'=>'customer']) }}">
            <i class="icon fa fa-address-book"></i>{{ trans('app.customer') }}
          </a>
        </li>
        @endpermission
        @permission('contact_group.browse')
        <li>
            <a class="treeview-item {{ activeMenu('contact/group') }}" href="{{ route('contact.group.index') }}">
              <i class="icon fa fa-address-book"></i>{{ trans('app.contact_group') }}
            </a>
        </li>
        @endpermission
      </ul>
    </li>
    @endpermission

    @if(auth()->user()->can(['product.browse', 'product-type.browse', 'brand.browse']))
    {{-- Product management --}}
    <li class="treeview {{ activeTreeviewMenu(['product', 'product-category', 'brand','product-units']) }}">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-product-hunt"></i>
        <span class="app-menu__label">{{ trans('app.product') }}</span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>

      <ul class="treeview-menu">
        @permission('product.browse')
        {{-- Product --}}
        <li>
          <a class="treeview-item {{ activeMenu('product') }}" href="{{ route('product.index') }}">
            <i class="icon fa fa-product-hunt pr-1"></i>{{ trans('app.product') }}
          </a>
        </li>
        @endpermission

        <li>
          <a class="treeview-item {{ activeMenu('product/barcode') }}" href="{{ route('product.barcode') }}">
            <i class="icon fa fa-qrcode pr-1"></i>{{ trans('app.product-barcode') }}
          </a>
        </li>

        <li>
          <a class="treeview-item {{ activeMenu('product/ime') }}" href="{{ route('product.ime') }}">
            <i class="icon fa fa-sticky-note-o pr-1"></i>{{ trans('app.product_ime') }}
          </a>
        </li>
        @permission('product-type.browse')
        {{-- Product category --}}
        <li>
          <a class="treeview-item {{ activeMenu('product-category') }}" href="{{ route('product_category.index') }}">
            <i class="icon fa fa-indent pr-1"></i>{{ trans('app.product_category') }}
          </a>
        </li>
        @endpermission
        @permission('product-type.browse')
        {{-- Product category --}}
        <li>
          <a class="treeview-item {{ activeMenu('product-units') }}" href="{{ route('product-units.index') }}">
            <i class="icon fa fa-indent pr-1"></i>{{ trans('app.unit') }}
          </a>
        </li>
        @endpermission
        @permission('brand.browse')
        {{-- Brand --}}
        <li>
          <a class="treeview-item {{ activeMenu('brand') }}" href="{{ route('brand.index') }}">
            <i class="icon fa fa-bandcamp pr-1"></i>{{ trans('app.brand') }}
          </a>
        </li>
        @endpermission
      </ul>
    </li>
    @endif

    @if(auth()->user()->can(['loan.browse', 'loan-cash.browse', 'customer.browse', 'loan.pay', 'staff.commission', 'loan.calculateloan']))
      <li class="treeview {{ activeTreeviewMenu(['client', 'loan','loan-cash', 'repayment', 'commission-payment','calculateloan']) }}">
        <a class="app-menu__item" href="#" data-toggle="treeview">
          <i class="app-menu__icon fa fa-money"></i>
          <span class="app-menu__label">{{ trans('app.loan') }}</span>
          <i class="treeview-indicator fa fa-angle-left"></i>
        </a>

        <ul class="treeview-menu">
          @permission('customer.browse')
            {{-- Client --}}
            <li>
              <a class="treeview-item {{ activeMenu('client') }}" href="{{ route('client.index') }}">
                <i class="app-menu__icon fa fa-address-book" aria-hidden="true"></i>
                <span class="app-menu__label">{{ trans('app.client') }}</span>
              </a>
            </li>
          @endpermission
          @permission('loan.browse')
            {{-- Loan --}}
            <li>
              <a class="treeview-item {{ activeMenu('loan') }}" href="{{ route('loan.index') }}">
                <i class="icon fa fa-money pr-1"></i>{{ trans('app.loan_product') }}
              </a>
            </li>
            {{-- Loan Cash--}}
           
          @endpermission
          @permission('loan-cash.browse')
            <li>
              <a class="treeview-item {{ activeMenu('loan-cash') }}" href="{{ route('loan-cash.index') }}">
                <i class="icon fa fa-money pr-1"></i>{{ trans('app.loan_cash') }}
              </a>
            </li>
          @endpermission
          @permission('loan.pay')
            {{-- Client payment --}}
            <li>
              <a class="treeview-item {{ activeMenu('repayment') }}" href="{{ route('repayment.index') }}">
                <i class="icon fa fa-credit-card pr-1"></i>{{ trans('app.payment') }}
              </a>
            </li>
          @endpermission
          @permission('staff.commission')
            {{-- Commission payment --}}
            <li>
              <a class="treeview-item {{ activeMenu('commission-payment') }}" href="{{ route('commission-payment.index') }}">
                <i class="icon fa fa-credit-card pr-1"></i>{{ trans('app.commission_payment') }}
              </a>
            </li>
          @endpermission
          @permission('loan.calculateloan')
            {{-- Client payment --}}
            <li>
              <a class="treeview-item {{ activeMenu('calculateloan') }}" href="{{ route('calculateloan') }}">
                <i class="icon fa  fa-calculator pr-1"></i>{{ trans('app.calculate_loan') }}
              </a>
            </li>
          @endpermission
        </ul>
      </li>
    @endif

    @permission('sale.browse')
      <li class="treeview {{ activeTreeviewMenu(['sale']) }}">
        <a class="app-menu__item" href="#" data-toggle="treeview">
          <i class="app-menu__icon fa fa-money"></i>
          <span class="app-menu__label">{{ trans('app.sale') }}</span>
          <i class="treeview-indicator fa fa-angle-left"></i>
        </a>

        <ul class="treeview-menu">
          <li>
            <a class="treeview-item  {{ request()->is('sale/create') ? 'active' : '' }}" href="{{ route('sale.create') }}">
              <i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i>
              <span class="app-menu__label">{{ trans('app.create') }} {{ trans('app.sale') }}</span>
            </a>
          </li>
          <li>
            <a class="treeview-item  {{ request()->is('sale') ? 'active' : '' }}" href="{{ route('sale.index') }}">
              <i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i>
              <span class="app-menu__label">{{ trans('app.sale') }}{{ trans('app.all') }}</span>
            </a>
          </li>
          @foreach (groupContacts()->where('type','customer') as $item)
            <li>
              <a class="treeview-item  {{ request()->is('sale/group/'.$item->id) ? 'active' : '' }}" href="{{ route('sale.contactGroup',$item->id) }}">
                <i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i>
                <span class="app-menu__label">{{ $item->name }}</span>
              </a>
            </li>
          @endforeach
          
        </ul>
      </li>
    
    @endpermission
    @permission('purchase-return.browse')
    <li>
      <a class="app-menu__item {{ activeMenu('sell-return') }}" href="{{ route('sell-return.index') }}">
        <i class="app-menu__icon fa fa-reply" aria-hidden="true"></i>
        <span class="app-menu__label">{{ trans('app.sell-return') }}</span>
      </a>
    </li>
    @endpermission
    @permission('po.browse')
    <li>
      <a class="app-menu__item {{ activeMenu('purchase') }}" href="{{ route('purchase.index') }}">
        <i class="app-menu__icon fa fa-cart-plus" aria-hidden="true"></i>
        <span class="app-menu__label">{{ trans('app.purchase') }}</span>
      </a>
    </li>
    @endpermission
    @permission('purchase-return.browse')
    <li>
      <a class="app-menu__item {{ activeMenu('purchase-return') }}" href="{{ route('purchase-return.index') }}">
        <i class="app-menu__icon fa fa-reply" aria-hidden="true"></i>
        <span class="app-menu__label">{{ trans('app.purchase_return') }}</span>
      </a>
    </li>
    @endpermission
    @permission('stock.transfer.browse')
    <li>
      <a class="app-menu__item {{ activeMenu('transfer') }}" href="{{ route('transfer.index') }}">
        <i class="app-menu__icon fa fa-exchange" aria-hidden="true"></i>
        <span class="app-menu__label">{{ trans('app.stock_transfer') }}</span>
      </a>
    </li>
    @endpermission

    @permission('stock.adjust.browse')
    <li>
      <a class="app-menu__item {{ activeMenu('adjustment') }}" href="{{ route('adjustment.index') }}">
        <i class="app-menu__icon fa fa-adjust" aria-hidden="true"></i>
        <span class="app-menu__label">{{ trans('app.stock_adjustment') }}</span>
      </a>
    </li>
    @endpermission

    @if(auth()->user()->can(['report.loan-approval', 'report.loan-expired', 'report.loan', 'report.financial', 'report.customer', 'report.payment', 'report.agent', 'report.commission-pay', 'report.product-sell']))
      {{-- Reports --}}
      <li class="treeview {{ activeTreeviewMenu('report') }}">
        <a class="app-menu__item" href="#" data-toggle="treeview">
          <i class="app-menu__icon fa fa-book"></i>
          <span class="app-menu__label">{{ trans('app.report') }}</span>
          <i class="treeview-indicator fa fa-angle-left"></i>
        </a>

        <ul class="treeview-menu">
          @permission('report.loan-approval')
          {{-- Loan disbursement --}}
          <li>
            <a class="treeview-item {{ activeMenu('disbursed-loan', 2) }}" href="{{ route('report.disbursed_loan') }}">
              <i class="icon fa fa-list-alt pr-1"></i>{{ trans('app.loan_disbursement') }}
            </a>
          </li>
          @endpermission
          @permission('report.loan-expired')
          {{-- Overdue loan --}}
          <li>
            <a class="treeview-item {{ activeMenu('overdue-loan', 2) }}" href="{{ route('report.overdue_loan') }}">
              <i class="icon fa fa-clock-o pr-1"></i>{{ trans('app.overdue_loan') }}
            </a>
          </li>
          @endpermission
          @permission('report.loan')
          {{-- loan --}}
          <li>
            <a class="treeview-item {{ activeMenu('loan', 2) }}" href="{{ route('report.loan', ReportLoanStatus::PENDING) }}">
              <i class="icon fa fa-money pr-1"></i>{{ trans('app.loan') }}
            </a>
          </li>
          @endpermission
          @permission('report.financial')
          {{-- Financial statement --}}
          <li>
            <a class="treeview-item {{ activeMenu('financial-statement', 2) }}" href="{{ route('report.financial_statement') }}">
              <i class="icon fa fa-credit-card-alt pr-1"></i>{{ trans('app.financial_statement') }}
            </a>
          </li>
          @endpermission
          @permission('report.cashincome')
          {{-- Financial statement --}}
          <li>
            <a class="treeview-item {{ activeMenu('cash-income', 2) }}" href="{{ route('report.cash-income') }}">
              <i class="icon fa fa-money pr-1"></i>{{ trans('app.cash_income_report') }}
            </a>
          </li>
          @endpermission
          @permission('report.cash-recieved')
          {{-- Financial statement --}}
          <li>
            <a class="treeview-item {{ activeMenu('cash-recieved', 2) }}" href="{{ route('report.cash-recieved') }}">
              <i class="icon fa fa-money pr-1"></i>{{ trans('app.cash_recieved_report') }}
            </a>
          </li>
          @endpermission

          @permission('report.customer')
          {{-- Client list --}}
          <li>
            <a class="treeview-item {{ activeMenu(['client-registration', 'loan-portfolio'], 2) }}" href="{{ route('report.client_registration') }}">
              <i class="icon fa fa-address-book pr-1"></i>{{ trans('app.client_registration') }}
            </a>
          </li>
          @endpermission
          @permission('report.payment')
          {{-- Client payment --}}
          <li>
            <a class="treeview-item {{ activeMenu('client-payment', 2) }}" href="{{ route('report.client_payment') }}">
              <i class="icon fa fa-credit-card pr-1"></i>{{ trans('app.payment') }}
            </a>
          </li>
          @endpermission
          @permission('report.agent')
          {{-- Agent and commission --}}
          <li>
            <a class="treeview-item {{ activeMenu(['agent', 'agent-commission'], 2) }}" href="{{ route('report.agent') }}">
              <i class="icon fa fa-address-book pr-1"></i>{{ trans('app.agent') }}
            </a>
          </li>
          @endpermission
          @permission('report.commission-pay')
          {{-- Commission payment --}}
          <li>
            <a class="treeview-item {{ activeMenu('commission-payment', 2) }}" href="{{ route('report.commission_payment') }}">
              <i class="icon fa fa-credit-card pr-1"></i>{{ trans('app.commission_payment') }}
            </a>
          </li>
          @endpermission
          @permission('report.profit-loss')
          {{-- Commission payment --}}
          <li>
            <a class="treeview-item {{ activeMenu('profit-loss', 2) }}" href="{{ route('report.profit-loss') }}">
              <i class="icon fa fa-money pr-1"></i>{{ trans('app.profit_loss') }}
            </a>
          </li>
          @endpermission

          @permission('report.purchase')
          <li>
            <a class="treeview-item {{ activeMenu('purchase', 2) }}" href="{{ route('report.purchase') }}">
              <i class="icon fa fa-cart-plus pr-1"></i>{{ trans('app.purchase_report') }}
            </a>
          </li>
          @endpermission

          @permission('report.sell')
          <li>
            <a class="treeview-item {{ activeMenu('sell', 2) }}" href="{{ route('report.sell') }}">
              <i class="icon fa fa-shopping-cart pr-1"></i>{{ trans('app.sell_report') }}
            </a>
          </li>
          @endpermission

          @permission('report.stock')
          {{-- Commission payment --}}
          <li>
            <a class="treeview-item {{ activeMenu('stock', 2) }}" href="{{ route('report.stock') }}">
              <i class="icon fa fa-database pr-1"></i>{{ trans('app.product_stock_report') }}
            </a>
          </li>
          @endpermission
          @permission('report.product-sell')
          {{-- Product sell --}}
          <li>
            <a class="treeview-item {{ activeMenu('product-sell', 2) }}" href="{{ route('report.product-sell') }}">
              <i class="icon fa fa-product-hunt pr-1"></i>{{ trans('app.product_sell') }}
            </a>
          </li>
          @endpermission

          @permission('report.purchase-sale')
          <li>
            <a class="treeview-item {{ activeMenu('purchase-sale', 2) }}" href="{{ route('report.purchase-sale') }}">
              <i class="icon fa fa-product-hunt pr-1"></i>{{ trans('app.purchase_sale') }}
            </a>
          </li>
          @endpermission
        </ul>
      </li>
    @endif
    @permission('branch.browse')
    {{-- Branch --}}
    <li>
      <a class="app-menu__item {{ activeMenu('branch') }}" href="{{ route('branch.index') }}">
        <i class="app-menu__icon fa fa-code-fork fa-lg" aria-hidden="true"></i>
        <span class="app-menu__label">{{ trans('app.branch') }}</span>
      </a>
    </li>
    @endpermission
    @if(auth()->user()->can(['expense.browse', 'expense_type.browse']))
    {{-- Staff and position --}}
    <li class="treeview {{ activeTreeviewMenu(['expense', 'expense-type']) }}">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-users"></i>
        <span class="app-menu__label">{{ trans('app.expenses') }}</span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>
      <ul class="treeview-menu">
        @permission('expense.browse')
        <li>
          <a class="treeview-item {{ activeMenu('expense') }}" href="{{ route('expense.index') }}">
            <i class="icon fa fa-user pr-1"></i>{{ trans('app.expense') }}
          </a>
        </li>
        @endpermission
        @permission('expense_type.browse')
        <li>
          <a class="treeview-item {{ activeMenu('expense-type') }}" href="{{ route('expense-type.index') }}">
            <i class="icon fa fa-bandcamp pr-1"></i>{{ trans('app.expense_type') }}
          </a>
        </li>
        @endpermission
      </ul>
    </li>
    @endif
    @if(auth()->user()->can(['staff.browse', 'position.browse']))
    {{-- Staff and position --}}
    <li class="treeview {{ activeTreeviewMenu(['staff', 'position']) }}">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-users"></i>
        <span class="app-menu__label">{{ trans('app.staff') }}</span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>
      <ul class="treeview-menu">
        @permission('staff.browse')
        <li>
          <a class="treeview-item {{ activeMenu('staff') }}" href="{{ route('staff.index') }}">
            <i class="icon fa fa-user pr-1"></i>{{ trans('app.staff') }}
          </a>
        </li>
        @endpermission
        @permission('position.browse')
        <li>
          <a class="treeview-item {{ activeMenu('position') }}" href="{{ route('position.index') }}">
            <i class="icon fa fa-bandcamp pr-1"></i>{{ trans('app.position') }}
          </a>
        </li>
        @endpermission
      </ul>
    </li>
    @endif
    @if(auth()->user()->can(['user.browse', 'role.browse']))
    {{-- User and role --}}
    <li class="treeview {{ activeTreeviewMenu(['user', 'role']) }}">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-users"></i>
        <span class="app-menu__label">{{ trans('app.user') }}</span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>
      <ul class="treeview-menu">
        @permission('user.browse')
        <li>
          <a class="treeview-item {{ activeMenu('user') }}" href="{{ route('user.index') }}">
            <i class="icon fa fa-user pr-1"></i>{{ trans('app.user') }}
          </a>
        </li>
        @endpermission
        @permission('role.browse')
        <li>
          <a class="treeview-item {{ activeMenu('role') }}" href="{{ route('role.index') }}">
            <i class="icon fa fa-briefcase pr-1"></i>{{ trans('app.role') }}
          </a>
        </li>
        @endpermission
      </ul>
    </li>
    @endif
    @permission('app.setting')
    {{-- Settings --}}
    <li class="treeview {{ activeTreeviewMenu('setting') }}">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-gears"></i>
        <span class="app-menu__label">{{ trans('app.setting') }}</span>
        <i class="treeview-indicator fa fa-angle-left"></i>
      </a>

      <ul class="treeview-menu">
        {{-- General setting --}}
        <li>
          <a class="treeview-item {{ activeMenu('general', 2) }}" href="{{ route('general_setting.index') }}">
            <i class="icon fa fa-gear pr-1"></i>{{ trans('app.general_setting') }}
          </a>
        </li>
        @permission('payment_method.browse')
        <li>
          <a class="treeview-item {{ activeMenu('method-payments', 2) }}" href="{{ route('method-payments.index') }}">
            <i class="icon fa fa-gear pr-1"></i>{{ trans('app.payment_method') }}
          </a>
        </li>
        @endpermission
        @permission('collateral_type.browse')
        <li>
          <a class="treeview-item {{ activeMenu('collateral-type') }}" href="{{ route('collateral-type.index') }}">
            <i class="icon fa fa-gear pr-1"></i>{{ trans('app.collateral_type') }}
          </a>
        </li>
        @endpermission
      </ul>
    </li>
    @endpermission
  </ul>
</aside>
