<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
            <h3 class="page-heading">{{ trans('app.product_ime') . ' - '.$title }}</h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>
                                {{ trans('app.no_sign') }}
                            </th>
                            <th>
                                {{ trans('app.product_name') }}
                            </th>
                            <th>
                                {{ trans('app.location') }}
                            </th>
                            <th>
                                {{ trans('app.name').trans('app.contact') }}
                            </th>
                            <th>
                                {{ trans('app.type') }}
                            </th>
                            <th>
                                {{ trans('app.price') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ime->transaction_ime as $item)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <td>
                                     {{ @$ime->product->name }}
                                     {{ @$ime->variantion->name!='DUMMY' ? ' - '.@$ime->variantion->name : '' }}
                                </td>
                                <td>{{ @$item->location->location }}</td>
                                <td>
                                    @if (@$item->transaction->type=='leasing')
                                        {{  @$item->transaction->customer->name }}
                                    @else
                                        {{ @$item->transaction->client->name }}
                                    @endif
                                       </td>
                                <td>{{ @$item->transaction->type }}</td>
                                <td>
                                    @if (@$item->transaction->type=='purchase')
                                        {{ @$item->purchase->purchase_price }}
                                    @else
                                        {{ @$item->sell->unit_price }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
