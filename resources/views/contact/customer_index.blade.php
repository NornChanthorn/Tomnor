@extends('layouts/backend')

@section('title', trans('app.customer'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.customer') }}</h3>
    @include('partial/flash-message')
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-6">
            <a href="javascript::void(0);" class="btn btn-success mb-1 btn-modal" title="{{ trans('app.create') }}" data-href="{{ route('contact.create', ['type'=>$type]) }}" data-container=".contact-modal">
              <i class="fa fa-plus-circle pr-1"></i> {{ trans('app.create') }}
            </a>
          </div>
          <div class="col-md-6 text-right">
            <form method="get" action="{{ route('contact.index', ['type'=>$type]) }}">
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
            <th>@sortablelink('contact_id', trans('app.contact-id'))</th>
            <th>@sortablelink('supplier_business_name', trans('app.company'))</th>
            <th>@sortablelink('name', trans('app.name'))</th>
            <th>{{ trans('app.first_phone') }}</th>
            <th>{{ trans('app.province') }}</th>
            <th class="text-right">{{ trans('app.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($contacts as $customer)
          <tr>
            <td class="text-center">{{ $offset++ }}</td>
            <td>{{ $customer->contact_id }}</td>
            <td>{{ $customer->supplier_business_name }}</td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->mobile }}</td>
            <td>{{ @$customer->province->khmer_name ?? '' }}</td>
            <td class="text-right">
              @include('partial/anchor-show', ['href' => route('contact.show', $customer->id)])
              @if ($customer->is_default == 0)
                @if(isAdmin() || Auth::user()->can('supplier.edit'))
                <a href="javascript::void(0);" class="btn btn-sm btn-primary mb-1 btn-modal" title="{{ trans('app.edit') }}" data-href="{{ route('contact.edit', ['id'=>$customer->id, 'type'=>$customer->type]) }}" data-container=".contact-modal">
                  <i class="fa fa-edit"></i>
                </a>
                @endif
                @if (isAdmin() || Auth::user()->can('supplier.delete'))
                  @include('partial/button-delete', ['url' => route('contact.destroy', $customer->id)])
                @endif
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      {!! $contacts->appends(Request::except('page'))->render() !!}
    </div>
  </div>
</main>

<div class="modal fade contact-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('js')
  <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
  <script src="{{ asset('js/mask.js') }}"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>

  <script type="text/javascript">
    var contactExist = "{{ trans('message.customer_already_exists') }}";

    $(document).ready( function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });

      //On display of add contact modal
      $('.contact-modal').on('shown.bs.modal', function(e) {
        let type = $("select#type").val();
        if(type=="{{\App\Constants\ContactType::SUPPLIER}}" || type=="{{\App\Constants\ContactType::BOTH}}") {
          $(".hidden-block").addClass('d-block').removeClass('d-none');
          $("#company").attr('required', true);
        } 
        else {
          $(".hidden-block").addClass('d-none').removeClass('d-block');
          $("#company").attr('required', false);
        }

        $("#type").on('change', function(e) {
          let type = $(this).val();
          if(type=="{{\App\Constants\ContactType::SUPPLIER}}" || type=="{{\App\Constants\ContactType::BOTH}}") {
            $(".hidden-block").addClass('d-block').removeClass('d-none');
            $("#company").attr('required', true);
          } 
          else {
            $(".hidden-block").addClass('d-none').removeClass('d-block');
            $("#company").attr('required', false);
          }
        });

        $('form#form-contact').submit(function(e) {
          e.preventDefault();
        }).validate({
          rules: {
            contact_id: {
              remote: {
                url: "{{ route('contact.check-contact') }}",
                type: 'POST',
                data: {
                  type: function() {
                    return $("#type").val();
                  },
                  contact_id: function() {
                    return $('#contact_id').val();
                  },
                  hidden_id: function() {
                    if($('#hidden_id').val()) {
                      return $('#hidden_id').val();
                    } 
                    else {
                      return '';
                    }
                  },
                },
              },
            },
          },
          messages: {
            contact_id: {
              remote: contactExist,
            },
          },
          submitHandler: function(form) {
            e.preventDefault();
            var data = $(form).serialize();
            $(form).find('button[type="submit"]').attr('disabled', true);
            $.ajax({
              method: 'POST',
              url: $(form).attr('action'),
              dataType: 'json',
              data: data,
              success: function(result) {
                if (result.success == true) {
                  window.location.reload();
                } 
                else {
                  // toastr.error(result.msg);
                }
              },
            });
          },
        });
      });
    });
  </script>
@endsection