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
            {{--Loan Amountc--}}
            <div class="col-md-4 form-group">
              <label for="">{{ __("app.loan_amount") }}</label>
              <input type="text" class="form-control date-picker" value="{{ $totalAmount}}" disabled >
            </div>
            {{-- Interest rate --}}
            <div class="col-lg-4 form-group">
              <label for="interest_rate" class="control-label">
                <span id="rate_text">{{ trans('app.interest_rate') }}</span> (%)
                <span id="rate_sign" class="required"></span>
              </label>
              <input type="text" name="interest_rate" id="interest_rate" class="form-control decimal-input"
                value="{{ old('interest_rate') ?? $loan->interest_rate }}" required min="0" >
            </div>
            <div class="col-md-4 form-group">
              <label for="">{{ __("app.first_payment_date") }}</label>
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
            {{-- Payment schedule type --}}
            <div class="col-lg-4 form-group">
              <label for="schedule_type" class="control-label">
                {{ trans('app.payment_schedule_type') }}
              </label>
              <select name="schedule_type" id="schedule_type" class="form-control select2 select2-no-search" required
              >
                <option value="{{ PaymentScheduleType::AMORTIZATION }}">
                  {{ trans('app.equal_payment') }} amortization
                </option>
                <option value="{{PaymentScheduleType::DECLINE_INTEREST}}">
                  {{ trans('app.down_interest_payment') }} decline interest
                </option>
                <option value="{{PaymentScheduleType::FLAT_INTEREST}}">
                  {{ trans('app.flat_interest') }} flat interest
                </option>

                {{--<option value="">{{ trans('app.select_option') }}</option>
                @foreach (paymentScheduleTypes() as $typeKey => $typeTitle)
                <option value="{{ $typeKey }}"
                  {{ !is_null(old('schedule_type')) ? (old('schedule_type') == $typeKey ? 'selected' : '') : ($loan->schedule_type == $typeKey ? 'selected' : '') }}>
                  {{ $typeTitle }}
                </option>

                @endforeach--}}
              </select>
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
