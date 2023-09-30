@extends('layouts/backend')

@section('title', trans('app.purchase'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.product_ime') . ' - '.$title }}</h3>

      @include('partial/flash-message')
      <br>
      <div class="row">
      @if ($qty > $transaction_imes->count())
        <div class="col-6">
          <a href="javascript::void(0);" class="btn btn-success mb-1 btn-modal" title="{{ trans('app.create') }}" data-href="{{ route('product.ime-single') }}" data-container=".ime-modal">
            <i class="fa fa-plus-circle pr-1"></i> {{ trans('app.create') }}
          </a>
          <form method="post" id="purchase-form" class="validated-form no-auto-submit" action="{{ route('product.ime-save') }}" enctype="multipart/form-data">
            @csrf
              <div class="form-group">
                <label for="">{{ $product->name }}  {{ @$variantion->name!='DUMMY' ? ' - '.@$variantion->name : '' }}  {{ trans('app.quantity'). $qty }}</label>
                <input type="text" class="form-control" name="code" required>
                <input type="hidden" name="transaction_id" value="{{ $transaction_id }}">
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="variantion_id" value="{{ $variantion->id }}">
                <input type="hidden" name="location_id" value="{{ $location_id }}">
                <input type="hidden" name="purchase_sell_id" value="{{ $purchase_sell_id }}">
                <input type="hidden" name="type" value="{{ $type }}">
              </div>
            <button class="btn btn-success" type="submit">{{ trans('app.save') }}</button>
  
          </form>
        </div>
        <div class="col-6">
      @else
      <div class="col-12">
      @endif
          @if ($type=='loan')
              @php
                $transaction_id = App\Models\Loan::where('transaction_id',$transaction_id)->first()->id;
              @endphp
          @endif
          <a class="btn btn-sm btn-success mb-4" href="{{ url($type,$transaction_id) }}">{{ trans('app.back') }}</a>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>{{ trans('app.no_sign') }}</th>
                  <th>{{ trans('app.product_name') }}</th>
                  <th>{{ trans('app.product_ime') }}</th>
                  <th>{{ trans('app.action') }}</th>
                </tr>
              </thead>
              @if ($transaction_imes->count()>0)
                @foreach ($transaction_imes as $item)
                    <tbody>
                      <tr>
                        <td>{{ $offset++ }}</td>
                        <td>{{ @$item->ime->product->name }}</td>
                        <td>{{ @$item->ime->code }}</td>
                        <td>
                          @include('partial/button-delete', ['url' => route('product.ime-destroy', $item->ime->id)])
                        </td>
                      </tr>
                    </tbody>
                @endforeach
              @endif
              
            </table>
          </div>

        </div>
      </div>
    </div>
  </main>

  <div class="modal fade ime-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('js')
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