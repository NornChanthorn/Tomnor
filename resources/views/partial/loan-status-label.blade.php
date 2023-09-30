@php $statusLabel = loanStatuses($loan->status) @endphp
@switch ($loan->status)
    @case (LoanStatus::PENDING)
        <label class="badge badge-warning">{{ $statusLabel }}</label>
        @break
    @case (LoanStatus::ACTIVE)
        <label class="badge badge-info">{{ $statusLabel }}</label>
        @break
    @case (LoanStatus::PAID)
        <label class="badge badge-success">{{ $statusLabel }}</label>
        @break
    @case (LoanStatus::REJECTED)
        <label class="badge badge-danger">{{ $statusLabel }}</label>
        @break
@endswitch
