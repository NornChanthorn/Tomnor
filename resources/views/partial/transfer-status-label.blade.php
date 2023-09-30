@switch ($transfer->status)
    @case (StockTransferStatus::COMPLETED)
    <label class="badge badge-success">{{ stockTransferStatuses($transfer->status) }}</label>
    @break
    @case (StockTransferStatus::SENT)
    <label class="badge badge-info">{{ stockTransferStatuses($transfer->status) }}</label>
    @break
@endswitch
