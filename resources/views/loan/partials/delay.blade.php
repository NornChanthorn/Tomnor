<div class="tab-pane active table-responsive" role="tabpanel">
    <table class="table table-hover table-bordered">
        <thead>
            <th>
                {{ __('app.installment') }}
            </th>
            <th>
                {{ __('app.frequency') }}
            </th>
            <th>
                {{ __('app.type') }}
            </th>
            <th>
                {{ __('app.note') }}
            </th>
            <th>
                {{ __('app.status') }}
            </th>
            <th>
                {{ __('app.approved_by') }}
            </th>
            <th>
                {{ __('app.approve') }}{{ __('app.note') }}
            </th>
            <th>
                {{ __('app.created_date') }}
            </th>
            <th>
                {{ __('app.action') }}
            </th>
        </thead>
        <tbody>
            @foreach ($loan->scheduleReferences as $item)
                <tr>

                    <td>
                        {{ $item->installment }} {{ __('app.times') }}
                    </td>
                    <td>
                        {{ frequencies($item->frequency) }}
                    </td>

                    <td>
                        {{ updatedSchedules($item->type) }}
                    </td>
                    <td>
                        {{ $item->note }}
                    </td>
                    <td>
                        {{ @$item->is_approved ? __('app.approved') : __('app.pending') }} 
                    </td>
                    <td>
                        {{ @$item->approved_note }}
                    </td>
                    <td>
                        {{ @$item->approved_note }}
                    </td>
                    <td>
                        {{ displayDate($item->created_at) }}
                    </td>
                    <td>
                        @if (@$item->is_approved==false)
                            <a href="javascript::void(0);" class="btn btn-primary mb-1 btn-modal" title="{{ trans('app.approve') }}" data-href="{{ route('loan.getDelayStatus', $item) }}" data-container=".schedule_modal">
                                {{ __('app.approve') }}
                            </a>
                        @endif
                        @if (@$item->is_approved==true)
                            <a href="javascript::void(0);" class="btn btn-primary mb-1 btn-modal" title="{{ trans('app.schedule_history') }}" data-href="{{ route('loan.getScheduleHistory', $item) }}" data-container=".schedule_modal">
                                {{ __('app.schedule_history') }}
                            </a>
                        @endif
                        @if(isAdmin() && @$item->is_approved==false|| Auth::user()->can('loan.delete') && @$item->is_approved==false)
                            {{-- Delete loan --}}
                            <button type="button" id="delete_loan" class="btn btn-danger btn-delete mb-1"
                                data-url="{{ route('loan.deleteDelaySchedule', $item->id) }}">
                                <i class="fa fa-trash-o"></i> {{ trans('app.delete') }}
                            </button>
                        @endif

                        
                    </td>
              
                </tr>
            @endforeach
        </tbody>
    </table>
</div>