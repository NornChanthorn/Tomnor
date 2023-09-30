<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ trans('app.schedule_history') }}</h4>
          <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body table-responsive">
          <table class="table table-borderless table-hover">
            <thead>
              <tr>
                <th>{{ trans('app.payment_date') }}</th>
                <th>{{ trans('app.payment_amount') }}</th>
                <th>{{ trans('app.total') }}</th>
                <th>{{ trans('app.principal') }}</th>
                <th>{{ trans('app.interest') }}</th>
                <th>{{ trans('app.outstanding') }}</th>
                <th>{{ trans('app.paid_date') }}</th>
                <th>{{ trans('app.paid_principal') }}</th>
                <th>{{ trans('app.paid_interest') }}</th>
                <th>{{ trans('app.penalty_amount') }}</th>
                <th>{{ trans('app.paid_amount') }}</th>
            </tr>
            </thead>
            <tbody>
              @foreach ($schedules as $schedule)
                <tr>
                    <td>{{ displayDate($schedule->payment_date) }}</td>
                    <td>$ {{ decimalNumber($schedule->principal,true) }}</td>
                    <td>$ {{ decimalNumber($schedule->total, true) }}</td>
                    <td>$ {{ decimalNumber($schedule->principal, true) }}</td>
                    <td>$ {{ decimalNumber($schedule->interest, true) }}</td>
                    <td>$ {{ decimalNumber($schedule->outstanding) }}</td>
                    <td>{{ displayDate($schedule->paid_date) }}</td>
                    <td>{{ $schedule->paid_principal ? '$ ' . decimalNumber($schedule->paid_principal, true) : '' }}</td>
                    <td>{{ $schedule->paid_interest ? '$ ' . decimalNumber($schedule->paid_interest, true) : '' }}</td>
                    <td>{{ $schedule->paid_penalty ? '$ ' . decimalNumber($schedule->paid_penalty, true) : '' }}</td>
                    <td>{{ $schedule->paid_total ? '$ ' . decimalNumber($schedule->paid_total, true) : '' }}</td>
                </tr>
            @endforeach
            </tbody>
          </table>
        </div>
  
        <div class="modal-footer no-print">
          <button type="button" class="btn btn-primary" aria-label="Print" onclick="$(this).closest('div.modal-content').print();">
            <i class="fa fa-print"></i> @lang( 'app.print' )
          </button>
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('app.close') }}</button>
        </div>
    </div>
</div>