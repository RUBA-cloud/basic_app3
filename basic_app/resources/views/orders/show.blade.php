@extends('adminlte::page')
@section('title', __('adminlte::adminlte.order_module'))

@section('content')

@php
    // Ensure relationships are available (controller can eager load them too)
    $items      = $order->items ?? collect();
    $offer      = $order->offer ?? null;

    $subtotal = $items->sum(fn($it) => (float)$it->price * (int)$it->quantity);
    $itemCount = (int) $items->sum('quantity');

    // Helper closures
    $asPercentOrFixed = function ($val) {
        if ($val === null || $val === '') return null;
        $v = (float)$val;
        if ($v <= 1)   return ['type' => 'fraction',  'value' => $v];        // 0.2 → 20%
        if ($v <= 100) return ['type' => 'percent',   'value' => $v];        // 20 → 20%
        return ['type' => 'fixed', 'value' => $v];                            // 150 → JOD 150
    };

    $sumLineTotals    = fn() => $items->sum(fn($it) => (float)$it->price * (int)$it->quantity);
    $itemsCount       = fn() => (int) $items->sum('quantity');

    // Offer calculation
    $discountAmount = 0.0;
    $offerSummary = [];

    // Determine the active type (the form makes them mutually exclusive)
    $isDiscount           = (bool) data_get($offer, 'is_discount', false);
    $isTotalDiscount      = (bool) data_get($offer, 'is_total_discount', false);
    $isTotalGift          = (bool) data_get($offer, 'is_total_gift', false);
    $isProductCountGift   = (bool) data_get($offer, 'is_product_count_gift', false);

    // Common fields
    $discProductRaw  = data_get($offer, 'discount_value_product');
    $discDeliveryRaw = data_get($offer, 'discount_value_delivery');
    $totalDiscount   = (float) data_get($offer, 'total_discount', 0);
    $giftThreshold   = (int)   data_get($offer, 'products_count_to_get_gift_offer', 0);
    $giftAmount      = (float) data_get($offer, 'total_gift', 0);

    $discProduct  = $asPercentOrFixed($discProductRaw);
    $discDelivery = $asPercentOrFixed($discDeliveryRaw);

    // Compute per-offer type
    if ($offer) {
        if ($isDiscount) {
            // Per-product discount (and optional delivery discount, shown only)
            $offerSummary[] = __('adminlte::adminlte.is_discount') ?: 'Type: Per-product discount';

            if ($discProduct) {
                $label = match($discProduct['type']) {
                    'fraction' => (string) round($discProduct['value'] * 100, 2) . '%',
                    'percent'  => (string) round($discProduct['value'], 2) . '%',
                    default    => number_format($discProduct['value'], 2)
                };
                $offerSummary[] = __('adminlte::adminlte.discount_value_product') . ': ' . $label;
            }

            if ($discDelivery) {
                $label = match($discDelivery['type']) {
                    'fraction' => (string) round($discDelivery['value'] * 100, 2) . '%',
                    'percent'  => (string) round($discDelivery['value'], 2) . '%',
                    default    => number_format($discDelivery['value'], 2)
                };
                $offerSummary[] = __('adminlte::adminlte.discount_value_delivery') . ': ' . $label;
            }

            // Calculate total discount across lines
            $linesCount = max(1, $items->count());
            $discountAmount = $items->sum(function ($it) use ($discProduct, $linesCount) {
                $lineTotal = (float)$it->price * (int)$it->quantity;
                if (!$discProduct) return 0;

                return match($discProduct['type']) {
                    'fraction' => $lineTotal * $discProduct['value'],                // e.g. 0.2
                    'percent'  => $lineTotal * ($discProduct['value'] / 100),       // e.g. 20%
                    'fixed'    => min($lineTotal, $discProduct['value'] / $linesCount), // spread fixed
                    default    => 0,
                };
            });

        } elseif ($isTotalDiscount) {
            // Flat discount on the total
            $offerSummary[] = __('adminlte::adminlte.is_total_discount') ?: 'Type: Total discount';
            $offerSummary[] = __('adminlte::adminlte.total_amount') . ': ' . number_format($totalDiscount, 2);
            $discountAmount = min($totalDiscount, $subtotal);

        } elseif ($isTotalGift || $isProductCountGift) {
            // Gift amount off when qty threshold is met
            $offerSummary[] = ($isTotalGift
                ? (__('adminlte::adminlte.is_total_gift') ?: 'Type: Total gift')
                : (__('adminlte::adminlte.is_product_count_gift') ?: 'Type: Gift on product count'));
            if ($giftThreshold > 0) {
                $offerSummary[] = (__('adminlte::adminlte.products_count_to_get_gift_offer') ?: 'Min items')
                                  . ': ' . $giftThreshold;
            }
            $offerSummary[] = (__('adminlte::adminlte.total_gift') ?: 'Gift amount')
                              . ': ' . number_format($giftAmount, 2);

            if ($itemCount >= $giftThreshold) {
                $discountAmount = min($giftAmount, $subtotal);
            }
        }
    }

    $grandTotal = max(0, $subtotal - $discountAmount);

    // Helper for per-line effective total (only changes for is_discount)
    $effectiveLineTotal = function($it) use ($isDiscount, $discProduct, $items) {
        $lineTotal = (float)$it->price * (int)$it->quantity;
        if (!$isDiscount || !$discProduct) return $lineTotal;

        $linesCount = max(1, $items->count());
        $deduction = match($discProduct['type']) {
            'fraction' => $lineTotal * $discProduct['value'],
            'percent'  => $lineTotal * ($discProduct['value'] / 100),
            'fixed'    => min($lineTotal, $discProduct['value'] / $linesCount),
            default    => 0,
        };
        return max(0, $lineTotal - $deduction);
    };

    // Fallback status label if you don't have $order->status_label accessor
    $statusLabelMap = [
        0 => __('adminlte::adminlte.pending')   ?: 'Pending',
        1 => __('adminlte::adminlte.accepted')  ?: 'Accepted',
        2 => __('adminlte::adminlte.rejected')  ?: 'Rejected',
        3 => __('adminlte::adminlte.completed') ?: 'Completed',
    ];
    $statusLabel = $order->status_label ?? ($statusLabelMap[$order->status] ?? 'Unknown');
@endphp

{{-- Header Card --}}
<div class="card mb-3">
  <div class="card-body">
    <h4 class="mb-1">#{{ $order->id }}</h4>
    <div class="text-muted">
        {{ __('adminlte::adminlte.status') ?: 'Status' }}:
        <span class="badge bg-secondary text-uppercase">{{ $statusLabel }}</span>
    </div>
    <div>{{ __('adminlte::adminlte.user_name') ?: 'Customer' }}: {{ $order->user?->name }}</div>
    <div>{{ __('adminlte::adminlte.employee') ?: 'Employee' }}: {{ $order->employee?->name ?? '-' }}</div>
    <div>{{ __('adminlte::adminlte.offer_name') ?: 'Offer' }}: {{ $offer?->name_en ?? $offer?->name_ar ?? '-' }}</div>
    <div class="mt-2">{{ $order->notes }}</div>
  </div>
</div>

{{-- Offer Summary Card (shown if offer exists) --}}
@if($offer)
<div class="card mb-3">
  <div class="card-header"><strong>{{ __('adminlte::adminlte.offer_details') ?: 'Offer Details' }}</strong></div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <ul class="mb-0">
            @forelse($offerSummary as $line)
                <li>{{ $line }}</li>
            @empty
                <li>{{ __('adminlte::adminlte.no_data_found') ?: 'No specific parameters for this offer.' }}</li>
            @endforelse
        </ul>
      </div>
      <div class="col-md-6">
        <table class="table table-sm mb-0">
            <tr>
                <th style="width: 40%;">{{ __('adminlte::adminlte.subtotal') ?: 'Subtotal' }}</th>
                <td>{{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <th>{{ __('adminlte::adminlte.discount') ?: 'Discount' }}</th>
                <td>-{{ number_format($discountAmount, 2) }}</td>
            </tr>
            <tr>
                <th>{{ __('adminlte::adminlte.total_amount') ?: 'Grand Total' }}</th>
                <td><strong>{{ number_format($grandTotal, 2) }}</strong></td>
            </tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Items Table --}}
<div class="card">
  <div class="card-header"><strong>{{ __('adminlte::adminlte.Modules') }}</strong></div>
  <div class="card-body p-0 table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>{{ __('adminlte::menu.product') ?: 'Product' }}</th>
          <th>{{ __('adminlte::adminlte.color') ?: 'Color' }}</th>
          <th>{{ __('adminlte::adminlte.quantity') ?: 'Qty' }}</th>
          @if($offer) <th>{{ __('adminlte.adminlte.offer_name') ?: 'Offer' }}</th> @endif
          <th>{{ __('adminlte::adminlte.price') ?: 'Unit Price' }}</th>
          <th>{{ __('adminlte::adminlte.total_amount') ?: 'Line Total' }}</th>
          @if($isDiscount)
            <th>{{ __('adminlte::adminlte.effective_total') ?: 'After Discount' }}</th>
          @endif
          <th style="width: 120px;">{{ __('adminlte::adminlte.actions') ?: 'Actions' }}</th>
        </tr>
      </thead>
      <tbody>
      @forelse($items as $it)
        @php
          $lineTotal = (float)$it->price * (int)$it->quantity;
          $afterDisc = $effectiveLineTotal($it);
        @endphp
        <tr>
          <td>{{ $it->product?->name ?? $it->product_id }}</td>
          <td>{{ $it->color }}</td>
          <td>{{ $it->quantity }}</td>
          @if($offer)
            <td>{{ $offer->name_en ?? $offer->name_ar }}</td>
          @endif
          <td>{{ number_format((float)$it->price, 2) }}</td>
          <td>{{ number_format($lineTotal, 2) }}</td>
          @if($isDiscount)
            <td>
              {{ number_format($afterDisc, 2) }}
              @if($afterDisc < $lineTotal)
                  <small class="text-success d-block">
                      -{{ number_format($lineTotal - $afterDisc, 2) }}
                  </small>
              @endif
            </td>
          @endif
          <td>
            {{-- Delete button -> opens modal requesting a note --}}
            <button type="button" class="btn btn-sm btn-danger"
                    data-toggle="modal" data-target="#deleteItemModal-{{ $it->id }}">
              <i class="fas fa-trash"></i> {{ __('adminlte::adminlte.delete') }}
            </button>
          </td>
        </tr>

        {{-- Modal per item to require a note --}}
        <div class="modal fade" id="deleteItemModal-{{ $it->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <form method="POST" action="{{ route('orders.items.destroy', [$order, $it]) }}" class="modal-content">
              @csrf
              @method('DELETE')
              <div class="modal-header">
                <h5 class="modal-title">{{ __('adminlte::adminlte.delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p>{{ __('adminlte::adminlte.please_add_reason_cancel') }}</p>
                <textarea name="note" class="form-control" rows="3" required
                          placeholder="{{ __('adminlte::adminlte.please_add_reason_cancel') }}"></textarea>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('adminlte::adminlte.cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('adminlte::adminlte.delete') }}</button>
              </div>
            </form>
          </div>
        </div>
      @empty
        <tr>
            <td colspan="8" class="text-center text-muted">{{ __('adminlte::adminlte.no_data_found') ?: 'No data found' }}</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- Totals footer (shown even if no offer) --}}
  <div class="card-footer">
    <div class="row g-3">
      <div class="col-md-6"></div>
      <div class="col-md-6">
        <table class="table table-sm mb-0">
            <tr>
                <th style="width: 40%;">{{ __('adminlte::adminlte.sub_total') ?: 'Subtotal' }}</th>
                <td>{{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <th>{{ __('adminlte::adminlte.discount') ?: 'Discount' }}</th>
                <td>-{{ number_format($discountAmount, 2) }}</td>
            </tr>
            <tr>
                <th>{{ __('adminlte::adminlte.total_amount') ?: 'Grand Total' }}</th>
                <td><strong>{{ number_format($grandTotal, 2) }}</strong></td>
            </tr>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Footer buttons --}}
<div class="mt-3 d-flex">
  <a class="btn btn-warning mr-2" href="{{ route('orders.edit',$order) }}">{{ __('adminlte::adminlte.edit') }}</a>
  <a class="btn btn-secondary" href="{{ url()->previous() }}">{{ __('adminlte::adminlte.go_back') }}</a>
</div>

@endsection
