@switch ($purchase->status)
  @case (PurchaseStatus::RECEIVED)
    <label class="badge badge-success">{{ purchaseStatuses($purchase->status) }}</label>
  @break
  @case (PurchaseStatus::ORDERED)
    <label class="badge badge-warning">{{ purchaseStatuses($purchase->status) }}</label>
  @break
@endswitch
