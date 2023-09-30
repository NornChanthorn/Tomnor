@extends('layouts/backend')

@section('title', __('app.add_opening_stock'))

@section('content')
  <main class="app-content">
    <!-- Content Header (Page header) -->
    <h3 class="page-heading">{{__('app.add_opening_stock')}}</h3>

    <!-- Main content -->
    <section class="content">
      {!! Form::open(['url' => action('OpeningStockController@save'), 'method' => 'post', 'id' => 'add_opening_stock_form' ]) !!}
        {!! Form::hidden('product_id', $product->id); !!}

        @include('opening_stock.form-part')

        <div class="tile text-right">
          @include('partial.button-save')
          {{-- <button type="submit" class="btn btn-primary">{{__('app.save')}}</button> --}}
        </div>
      {!! Form::close() !!}
    </section>
  </main>
@stop

@section('js')
  <script src="{{ asset('js/jquery-number.min.js') }}"></script>
  <script src="{{ asset('js/number.js') }}"></script>
  <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
  <script src="{{ asset('js/mask.js') }}"></script>
  <script src="{{ asset('js/opening_stock.js') }}"></script>
@endsection
