@extends('layouts/backend')

@section('title', trans('app.product_ime'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.product_ime') }}</h3>
    @include('partial/flash-message')
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-6">
             <a href="javascript::void(0);" class="btn btn-success mb-1 btn-modal" title="{{ trans('app.create') }}" data-href="{{ route('product.ime-single') }}" data-container=".ime-modal">
              <i class="fa fa-plus-circle pr-1"></i> {{ trans('app.create') }}
            </a>
          </div>
          <div class="col-md-6 text-right">
            <form method="get" action="">
              @include('partial.search-input-group')
            </form>
          </div>
        </div>
      </div>
    </div>
    <br>
    @include('partial.item-count-label')
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <th class="text-center">{{ trans('app.no_sign') }}</th>
            <th>{{  trans('app.product_ime') }}</th>
            <th>{{  trans('app.status') }}</th>
            <th class="text-right">{{ trans('app.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($imes as $ime)
            <tr style="background: {{ @$ime->transaction_ime ? "rgba(144, 253, 144, 0.192)" : "rgb(253 154 144 / 19%)" }}">
                <td class="text-center">{{ $offset++ }}</td>
                <td>{{ $ime->code }}</td>
                <td><span class="badge {{ $ime->status=='available' ? "badge-success" : "badge-danger" }}">{{ ucfirst($ime->status) }}</span></td>
                <td>
                    <a href="javascript::void(0);" class="btn btn-sm btn-primary mb-1 btn-modal" title="{{ trans('app.view_detail') }}" data-href="{{ route('product.ime-show',$ime->id) }}" data-container=".ime-modal">
                        <i class="fa fa-eye"></i>
                    </a>
                    @include('partial/button-delete', ['url' => route('product.ime-destroy', $ime->id)])
                </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      {!! $imes->appends(Request::except('page'))->render() !!}
    </div>
  </div>
</main>

<div class="modal fade ime-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('js')
  <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
  <script src="{{ asset('js/mask.js') }}"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>

  <script type="text/javascript">
    $(document).ready( function() {
        $(".btn-delete").on('click', function() {
            confirmPopup($(this).data('url'), 'error', 'GET');
        });
        //On display of add contact modal
        $('.ime-modal').on('shown.bs.modal', function(e) {

        });
    });

  </script>
@endsection
