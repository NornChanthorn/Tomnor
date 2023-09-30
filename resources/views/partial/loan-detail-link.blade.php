
@if ($loan->type=='cash')
    <a href="{{ route('loan-cash.show', $loan) }}">
        {{ $loan->account_number }} / {{ str_pad($loan->client_id, 6, '0',STR_PAD_LEFT) }}

    </a>
@else
    <a href="{{ route('loan.show', $loan) }}">
        {{ $loan->account_number }} / {{ str_pad($loan->client_id, 6, '0',STR_PAD_LEFT) }}

    </a> 
@endif
