@extends('layouts/backend')

@section('title', trans('app.user'))

@section('content') 
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.user') }}</h3>
      @include('partial/flash-message')
      <div class="card">
        <div class="card-header">
          <form method="get" action="{{ route('user.index') }}">
            <div class="row">
              <div class="col-lg-4">
                @include('partial/anchor-create', [
                  'href' => route('user.create')
                ])
              </div>
              <div class="col-lg-8">
                <div class="row">
                  @include('partial.search-input-group')
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <br>

      @include('partial.item-count-label')
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>{{ trans('app.no_sign') }}</th>
              <td>@sortablelink('name', trans('app.staff'))</td>
              <td>@sortablelink('username', trans('app.username'))</td>
              <td>@sortablelink('role', trans('app.role'))</td>
              <td>@sortablelink('active', trans('app.status'))</td>
              <th>{{ trans('app.action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
              <tr>
                <td>{{ $offset++ }}</td>
                <td>
                  @if (!empty($user->staff))
                    <a href="{{ route('staff.show', $user->staff->id) }}">{{ $user->staff->name }}</a>
                  @else
                    @if($user->id === 2)
                      System Administrator
                    @else
                      System user
                    @endif
                    {{-- {{ trans('app.n/a') }} --}}
                  @endif
                </td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->roles[0]->display_name }}</td>
                <td class="text-center">
                  @if ($user->active)
                    <label class="badge badge-success">{{ trans('app.active') }}</label>
                  @else
                    <label class="badge badge-danger">{{ trans('app.inactive') }}</label>
                  @endif
                </td>
                <td>
                  @include('partial.anchor-edit', [
                    'href' => route('user.edit', $user->id),
                  ])
                  @if(2 != $user->id)
                    @if(!empty($user->staff))
                      @include('partial.button-jv-delete', [
                        'url' => route('staff.destroy', $user->staff->id)
                      ])
                    @else
                      @include('partial.button-jv-delete', [
                        'url' => route('user.destroy', $user->id)
                      ])
                    @endif
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {!! $users->appends(Request::except('page'))->render() !!}
      </div>
    </div>
  </main>
@endsection

@section('js')
  {{-- <script src="https://unpkg.com/axios/dist/axios.min.js"></script> --}}
  <script type="text/javascript">
    function deleteAction(argument) {
      console.log($(this).attr("data-url"));
    }

    $(document).ready(function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });

      // let root_url= window.location.href;
      // $(".btn-delete").click(function(evt) {
      //   evt.preventDefault();
      //   let url = $(this).data('url');
      //   Swal.fire({
      //     title: 'Are you sure?',
      //     text: 'You will not be able to recover this record!',
      //     type: 'warning',
      //     showCancelButton: true,
      //     confirmButtonText: 'Yes, delete it!',
      //     cancelButtonText: 'No, keep it'
      //   }).then((result) => {
      //     if (result.value) {
      //       axios.delete(url).then(res => {
      //         if(200 ===res.status){
      //           window.location.href=root_url;
      //         }
      //       }).catch(err => {
      //         console.log(err);
      //       });
      //     }
      //   });
      // });
    });
  </script>
@endsection