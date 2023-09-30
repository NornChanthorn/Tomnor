@php $statusLabel = saleStatuses($sale->status) @endphp
@switch ($sale->status)
    @case (SaleStatus::DRAFT)
        <label class="badge badge-warning">{{ $statusLabel }}</label>
        @break
    @case (SaleStatus::PENDING)
        <label class="badge badge-warning">{{ $statusLabel }}</label>
        @break
    @case (SaleStatus::ACTIVE)
        <label class="badge badge-info">{{ $statusLabel }}</label>
        @break
    @case (SaleStatus::DONE)
        <label class="badge badge-success">{{ $statusLabel }}</label>
        @break
@endswitch