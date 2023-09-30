
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
            <h3 class="page-heading">{{ trans('app.product_ime') . ' - '.$title }} - {{ trans('app.quantity') }} {{  $ime->count() }}</h3>

            <form id="" class="mb-4" method="post" action="{{ route('product.save-ime') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" name="code" required>
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variantion_id" value="{{ $variantion->id }}">
                </div>
                <button class="btn btn-success" type="submit">{{ trans('app.save') }}</button>
            </form>
            <div class="table-responsive" style="max-height: 500px;
            overflow-y: auto;">
                <table class="table table-bordered" style="">
                    <thead>
                        <tr>
                            <th>
                                {{ trans('app.no_sign') }}
                            </th>
                            <th>
                                {{ trans('app.product_ime') }}
                            </th>
                            <th>
                                {{ trans('app.status') }}
                            </th>
                            <th>
                                {{ trans('app.action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody >
                        @foreach ($ime as $item)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <td>{{ $item->code }}</td>
                                <td>
                                    <span class="badge {{ $item->status=='available' ? "badge-success" : "badge-danger" }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('product.ime-destroy', $item->id) }}" class="btn btn-danger btn-sm mb-1 btn-delete">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
