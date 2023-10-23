<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <p>Reschedule by id {{$loan->id}}</p>
      <p>Loan Status : {{$loan->status}}</p>
      <p>{{$depreciation->id}}</p>
      <form method="post" id="add_form" class="no-auto-submit" action="{{ route('loan.saveDelaySchedule', $loan) }}">
        @csrf
        <input type="hidden" name="schedule_reference_id"  value="{{ $schedule_reference->id }}">
        <div class="modal-header">
          <h4 class="modal-title">{{ trans('app.reschedule') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-4 form-group">
              <label for="">{{ __("app.loan_amount") }}</label>
              <input type="text" class="form-control date-picker" value="{{ displayDate(now()) }}">
            </div>
            <div class="col-md-4 form-group">
              <label for="">{{ __("app.next_payment_date") }}</label>
              <input type="text" class="form-control date-picker" value="{{ oneMonthIncrement(date('Y-m-d')) }}">
            </div>
            <div class="col-md-4 form-group">
              <label for="">{{ __("app.frequency") }}</label>
              <select name="frequency" id="" class="form-control" required disabled>
                @foreach (frequencies() as $key => $item)
                  <option value="{{ $key }}">
                    {{ $item }}
                  </option>
                @endforeach

              </select>
              <input type="hidden" name="frequency" value="30">
            </div>
            <div class="col-md-4 form-group">
              <label for="">{{ __("app.installment") }}</label>
              <input type="text" name="installment" class="form-control" value="1" min="1" required>
            </div>
            <div class="col-md-4 form-group">
              <label for="">{{ __('app.type') }}</label>
              <select name="type" id="" class="form-control" required disabled>
                @foreach (updatedSchedules() as $key => $item)
                  <option value="{{ $key }}">
                    {{ $item }}
                  </option>
                @endforeach
                  <input type="hidden" name="type" value="de">
              </select>
            </div>
            <div class="col-md-12 form-group">
              <label for="">{{ __('app.note') }}</label>
              <textarea name="note" id="" class="form-control" cols="30" rows="10"></textarea>
            </div>
          </div>
        </div>
        <div class="col-lg-12 text-center">
              <button type="button" id="calculate-payment" class="btn btn-info">
                {{ trans('app.calculate_payment_schedule') }}
              </button>
        </div>
        <br>

        {{-- Payment schedule table --}}
        <div class="row">
            <div class="col-lg-12 table-responsive" id="print-table">
                <table style="display: none;" id="schedule-table" class="table table-bordered table-hover table-striped">
                </table>
            </div>
            <div class="col-lg-12">
                <button type="button" style="display: none;" id="print" class="btn btn-info">
                {{ trans('app.print') }}
                </button>
            </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> @lang('app.save')</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('app.close') }}</button>
        </div>
      </form>
    </div>
</div>
