<div class="tab-pane active table-responsive" role="tabpanel">
    @if(Auth::user()->can('collateral.add'))
        @include('partial/anchor-create', ['href' => route('collateral-create',$loan), 'class' => 'mb-2 mt-2 pull-right'])
    @endif
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>{{ __('app.no_sign') }}</th>
                <th>{{ __('app.name') }}</th>
                <th>{{ __('app.collateral_type') }}</th>
                <th>{{ __('app.value') }}</th>
                <th>{{ __('app.note') }}</th>
                <th>{{ __('app.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loan->collaterals as $collateral)
                <tr>
                    <td>
                        {{ no_f($loop->iteration) }}
                    </td>
                    <td>
                        {{ $collateral->name }}
                    </td>
                    <td>
                        {{ collateralTypes($collateral->type_id) }}
                    </td>
                    <td>
                        {{ num_f($collateral->value)}}
                    </td>
                    <td>
                        {!! $collateral->note !!}
                    </td>
                    <td>
                        <a class="btn btn-sm btn-success mb-1" href="{{ asset($collateral->files) }}" target="_blank">
                        <i class="fa fa-download"></i></a>
                        @if(Auth::user()->can('collateral_type.edit'))
                            @include('partial.anchor-edit', [
                                'href' => route('collateral-edit', $collateral),
                            ])
                        @endif
                        @if(Auth::user()->can('collateral.delete'))
                            <a href="javascript:void(0);" title="{{ __('app.delete') }}" data-url="{{ route('collateral.destroy', $collateral) }}"  data-redirect="{{ route('loan-cash.show',[$loan,'get'=>'collaterals']) }}" class="btn btn-danger btn-sm mb-1 btn-delete"><i class="fa fa-trash-o"></i></a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>