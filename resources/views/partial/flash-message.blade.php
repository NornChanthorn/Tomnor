@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p><strong class="text-danger">{!! $error !!}</strong></p>
        @endforeach
    </div>
@endif

@if (session()->has(Message::SUCCESS_KEY))
    <div class="alert alert-success">
        <p>{!! session(Message::SUCCESS_KEY) !!}</p>
    </div>
@endif

@if (session()->has(Message::ERROR_KEY))
    <div class="alert alert-danger">
        <p><strong class="text-danger">{!! session(Message::ERROR_KEY) !!}</strong></p>
    </div>
@endif

