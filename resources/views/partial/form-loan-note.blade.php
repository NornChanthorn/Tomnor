{{-- Form updating note of a loan --}}
<form method="post" action="{{ route('loan.update_note', $loan) }}">
    @csrf
    <textarea name="note" class="form-control" rows="5">{{ $loan->note }}</textarea>
    <div>
        @include('partial.button-save', [
            'class' => 'pull-right'
        ])
    </div>
</form>
