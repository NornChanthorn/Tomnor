@extends('layouts/backend')

@section('title', trans('app.payment_method'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.payment_method') }}</h3>
    @include('partial/flash-message')
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-6">
             <a href="#" class="btn btn-success mb-1 btn-modal" title="{{ trans('app.create') }}" data-href="{{ route('method-payments.create') }}" data-container=".ime-modal">
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
            <th>{{  trans('app.name') }}</th>
            <th class="text-right">{{ trans('app.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($methodPayments as $item)
                <tr>
                    <td class="text-center">{{ $offset++ }}</td>
                    <td>{{ $item->value }}</td>
                    <td>
                        <a href="javascript::void(0);" class="btn btn-sm btn-info mb-1 btn-modal" title="{{ trans('app.create') }}" data-href="{{ route('method-payments.edit', $item) }}" data-container=".ime-modal">
                          <i class="fa fa-edit"></i></a>
                        @include('partial/button-delete', ['url' => route('method-payments.destroy',$item)])
                    </td>
                </tr>
          @endforeach
        </tbody>
      </table>
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
            confirmPopup($(this).data('url'), 'error', 'DELETE');
        });
        //On display of add contact modal
        $('.ime-modal').on('shown.bs.modal', function(e) {

        });
    });

  </script>
@endsection
