<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
            <h3 class="page-heading">{{ trans('app.contact_group') . ' - '.$title }}</h3>

            <form method="post" action="{{ route('contact.group.save',$group) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="">{{ trans('app.name') }}</label>
                    <input type="text" class="form-control" name="name" value="{{ $group->name }}" required>
                </div>
                <div class="form-group">
                    <label for="">{{ trans('app.type') }}</label>
                    <select name="type" class="form-control" id="">
                        @foreach (contacttypes() as $key => $item)
                            <option value="{{ $key }}" {{ selectedOption($key, old('type', $group->type)) }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-success" type="submit">{{ trans('app.save') }}</button>
            </form>
        </div>

    </div>
</div>
