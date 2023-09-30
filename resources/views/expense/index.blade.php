@extends('layouts/backend')

@section('title', trans('app.expense'))

@section('content')
<main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.expense') }}</h3>
      @include('partial/flash-message')
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-6">
              <a href="javascript::void(0);" class="btn btn-success mb-1 btn-modal" title="{{ trans('app.create') }}" data-href="{{ route('expense.create') }}" data-container=".expense-modal">
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
              <th class="text-center">{{ trans('app.reference_number') }}</th>
              <th class="text-center">{{ trans('app.amount') }}</th>
              <th class="text-center">{{ trans('app.note') }}</th>
              <th class="text-center">{{ trans('app.category') }}</th>
              <th class="text-center">{{ trans('app.date') }}</th>
              <th class="text-right">{{ trans('app.action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($expenses as $expense)
            <tr>
                <td class="text-center">{{ $offset++ }}</td>
                <td class="text-center"> {{ $expense->refno }}</td>
                <td class="text-center">$ {{ decimalNumber($expense->amount,2) }}</td>
                <td>{!! $expense->note !!}</td>
                <td class="text-center"> {{ $expense->category->value }}</td>
                <td class="text-center">{{  displayDate($expense->expense_date)  }}</td>
                <td>
                    <a href="javascript::void(0);" class="btn btn-sm btn-success mb-1 btn-modal" title="{{ trans('app.detail') }}" data-href="{{ route('expense.show',$expense->id) }}" data-container=".expense-modal">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="javascript::void(0);" class="btn btn-sm btn-primary mb-1 btn-modal" title="{{ trans('app.edit') }}" data-href="{{ route('expense.edit',$expense->id) }}" data-container=".expense-modal">
                      <i class="fa fa-edit"></i>
                    </a>
                    @include('partial/button-delete', ['url' => route('expense.destroy', $expense->id)])
                </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        {!! $expenses->appends(Request::except('page'))->render() !!}
      </div>
    </div>
</main>

<div class="modal fade expense-modal" tabindex="-0" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('js')
    <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
    <script src="{{ asset('js/mask.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="{{ asset('js/date-time-picker.js') }}"></script>
    <script>
      $(document).ready(function() {
        $(".date-picker").datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true,
          orientation: 'bottom right'
        });
      });
    </script>
    <script type="text/javascript">
        var contactExist = "{{ trans('message.customer_already_exists') }}";

        $(document).ready( function() {
            $(".btn-delete").on('click', function() {
                confirmPopup($(this).data('url'), 'error', 'DELETE');
            });

            //On display of add contact modal
            $('.expense-modal').on('shown.bs.modal', function(e) {
                $(".date-picker").datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                orientation: 'bottom right'
                });
            });
        });
    </script>
@endsection
