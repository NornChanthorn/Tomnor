@extends('layouts/backend')

@section('title', trans('app.contact_group'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.contact_group') }}</h3>
    @include('partial/flash-message')
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-6">
             <a href="javascript::void(0);" class="btn btn-success mb-1 btn-modal" title="{{ trans('app.create') }}" data-href="{{ route('contact.group.create') }}" data-container=".group-modal">
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
            <th>{{  trans('app.name') }}</th>
            <th>@sortablelink('type',trans('app.type'))</th>
            <th class="text-right">{{ trans('app.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($contactGroups as $group)
                <tr>
                    <td class="text-center">{{ $offset++ }}</td>
                    <td>{{ $group->name }}</td>
                    <td>{{ contacttypes($group->type) }}</td>
                    <td>
                      <a href="javascript::void(0);" class="btn btn-sm btn-primary mb-1 btn-modal" title="{{ trans('app.edit') }}" data-href="{{ route('contact.group.edit',$group) }}" data-container=".group-modal">
                        <i class="fa fa-edit"></i> 
                      </a>
                        @include('partial/button-delete', ['url' => route('contact.group.destroy',$group)])
                    </td>
                </tr>
          @endforeach
        </tbody>
      </table>
      {!! $contactGroups->appends(Request::except('page'))->render() !!}
    </div>
  </div>
</main>

<div class="modal fade group-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('js')
  <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
  <script src="{{ asset('js/mask.js') }}"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>

  <script type="text/javascript">
    $(document).ready( function() {
        $(".btn-delete").on('click', function() {
            confirmPopup($(this).data('url'), 'error', 'DELETE');
        });
        //On display of add contact modal
        $('.ime-modal').on('shown.bs.modal', function(e) {

        });
    });

  </script>
@endsection
