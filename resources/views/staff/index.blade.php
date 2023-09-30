@extends('layouts/backend')
@section('title', trans('app.staff'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.staff') }}</h3>
            @include('partial/flash-message')
            <div class="card">
                <div class="card-header">
                    <form method="get" action="">
                        <div class="row">
                            <div class="col-lg-4">
                                @include('partial/anchor-create', [
                                    'href' => route('staff.create')
                                ])
                            </div>
                            <div class="col-lg-2 pl-1 pr-0">
                                <select name="branch" id="branch" class="form-control select2">
                                    <option value="">{{ trans('app.branch') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 pl-1 pr-0">
                                <select name="position" id="position" class="form-control select2">
                                    <option value="">{{ trans('app.position') }}</option>
                                    @foreach (positions() as $position)
                                        <option value="{{ $position->id }}" {{ request('position') == $position->id ? 'selected' : '' }}>
                                            {{ $position->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 pl-1">
                                @include('partial.search-input-group')
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
                            <td>@sortablelink('name', trans('app.name'))</td>
                            <th>{{ trans('app.profile_photo') }}</th>
                            <td>@sortablelink('gender', trans('app.gender'))</td>
                            <td>@sortablelink('id_card_number', trans('app.id_card_number'))</td>
                            <td>@sortablelink('first_phone', trans('app.first_phone'))</td>
                            <th>{{ trans('app.branch') }}</th>
                            <th>{{ trans('app.position') }}</th>
                            <th>{{ trans('app.username') }}</th>
                            <th>{{ trans('app.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staff as $singleStaff)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <td>{{ $singleStaff->name }}</td>
                                <td>@include('partial.staff-profile-photo', ['staff' => $singleStaff])</td>
                                <td>{{ genders($singleStaff->gender) }}</td>
                                <td>{{ $singleStaff->id_card_number }}</td>
                                <td>{{ $singleStaff->first_phone }}</td>
                                <td>{{ $singleStaff->branch->location ?? trans('app.n/a') }}</td>
                                <td>{{ positions($singleStaff->position) }}</td>
                                <td>{{ $singleStaff->user->username ?? '' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('staff.commission', $singleStaff->id) }}" class="btn btn-success btn-sm mb-1">
                                        {{ trans('app.commission') }}
                                    </a>
                                    <br>
                                    @include('partial.anchor-show', [
                                        'href' => route('staff.show', $singleStaff->id),
                                    ])
                                    @include('partial.anchor-edit', [
                                        'href' => route('staff.edit', $singleStaff->id),
                                    ])
                                    @include('partial.button-delete', [
                                        'url' => route('staff.destroy', $singleStaff->id),
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $staff->appends(Request::except('page'))->render() !!}
            </div>
        </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="text/javascript">
        function deleteAction(argument) {
            console.log($(this).attr("data-url"));
        }
        $(document).ready(function(){
            let root_url= window.location.href;
            $(".btn-delete").click(function(evt){
                evt.preventDefault();
                let url = $(this).data('url');
                Swal.fire({
                  title: 'Are you sure?',
                  text: 'You will not be able to recover this record!',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, delete it!',
                  cancelButtonText: 'No, keep it'
                }).then((result) => {
                  if (result.value) {
                    axios.delete(url)
                        .then(res => {
                            if(200 ===res.status){
                                window.location.href=root_url;
                            }
                        }).catch(err => {
                            console.log(err);
                        });
                    
                  }
                });
            });
        });
    </script>
@endsection
