<!DOCTYPE html>
<html lang="km">
<head>
  <title>{{ $generalSetting->site_title }} @hasSection('title') - @endif @yield('title')</title>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="បង់​រំលោះ​ទូរសព្ទ​ដៃ">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
  <link rel="icon" href="{{ asset($generalSetting->site_logo) }}" sizes="32x32">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Battambang">
  <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

  <link rel="stylesheet" href="{{ asset('css/listswap.css') }}">
  <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/planit.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/venobox/venobox.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datepicker/bootstrap-datepicker.min.css') }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>

  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <style>
    .btn-sm { line-height: 1; }
    .select2 { width: 100%!important; }
    .img-thumbnail { padding: 1px; border-radius: 0; }
    .dropdown-menu.dropdown-menu-right { width: 196px; }
  </style>

  @yield('css')
</head>
<body class="app sidebar-mini rtl">
  <!-- Navbar -->
  <header class="app-header">
    <a class="app-header__logo" href="{{ route('dashboard') }}" style="padding: 0; line-height: 50px;">
      {{--<img src="" height="50">--}}
      <h4 class="app-header-title">{{ $generalSetting->site_title }}</h4>
    </a>

    <!-- Sidebar Toggle Button -->
    <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    <!-- Navbar Right Menu -->
    <ul class="app-nav">
      <!-- User Menu -->
      <li class="dropdown">
        <a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu">
          <i class="fa fa-user fa-lg"></i>
        </a>
        <ul class="dropdown-menu settings-menu dropdown-menu-right">
          {{-- Profile --}}
          <li>
            <a class="dropdown-item {{ activeMenu('profile') }}" href="{{ route('user.show_profile', auth()->user()->id) }}">
              <i class="fa fa-user fa-lg"></i> {{ trans('app.profile') }}
            </a>
          </li>

          @permission('app.setting')
          {{-- General setting --}}
          <li>
            <a class="dropdown-item {{ activeMenu('general', 2) }}" href="{{ route('general_setting.index') }}">
              <i class="fa fa-gear fa-lg"></i> {{ trans('app.general_setting') }}
            </a>
          </li>
          @endpermission

          {{-- Logout --}}
          <li>
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
              <i class="fa fa-sign-out fa-lg"></i> {{ trans('app.log_out') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="post" class="d-none">@csrf</form>
          </li>
        </ul>
      </li>
      <!-- End User Menu -->
    </ul>
  </header>

  <!-- Sidebar Menu -->
  <div class="app-sidebar__overlay" data-toggle="sidebar"></div>

  {{-- sidebar --}}
  @include('layouts.partials.aside')

  @yield('content')

  <script src="{{ asset('js/lang/en.js') }}" type="text/javascript" charset="utf-8" async defer></script>
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('js/popper.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>
  <script src="{{ asset('js/pace.min.js') }}"></script>
  <script src="{{ asset('js/jquery.listswap.js') }}"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script src="{{ asset('js/modernizr.min.js') }}"></script>
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
  <script src="{{ asset('js/sweetalert.min.js') }}"></script>
  <script src="{{ asset('plugins/venobox/venobox.min.js') }}"></script>
  <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('js/jQuery.print.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
  <script>
    var emptyOptionElm = '<option value="">{{ trans('app.select_option') }}</option>';
    var sweetAlertTitle = '{{ trans('app.confirmation') }}';
    var sweetAlertText = '{{ trans('message.confirm_perform_action') }}';

    $.ajaxSetup({
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}'
      }
    });

    $(document).ready(function() {
      $(".popup-img").venobox();
    });
  </script>
  @yield('js')
</body>
</html>
