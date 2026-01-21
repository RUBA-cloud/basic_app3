@extends('adminlte::page')
@section('title', __('adminlte::adminlte.order_module'))

@section('adminlte_css')
@parent
<style>
  /* ... نفس CSS عندك بدون تغيير ... */
</style>
@endsection

@section('content')

@php
    use Illuminate\Support\Str;

    $isAr = app()->isLocale('ar');

    // ✅ shipped value (عدّليه إذا عندك مختلف)
    $SHIPPED_STATUS = 3;

    $items      = $order->items ?? collect();
    $offer      = $order->offer ?? null;

    $subtotal  = $items->sum(fn($it) => (float)$it->price * (int)$it->quantity);
    $itemCount = (int) $items->sum('quantity');

    $asPercentOrFixed = function ($val) {
        if ($val === null || $val === '') return null;
        $v = (float)$val;
        if ($v <= 1)   return ['type' => 'fraction',  'value' => $v];
        if ($v <= 100) return ['type' => 'percent',   'value' => $v];
        return ['type' => 'fixed', 'value' => $v];
    };

    $discountAmount = 0.0;
    $offerSummary = [];

    $isDiscount           = (bool) data_get($offer, 'is_discount', false);
    $isTotalDiscount      = (bool) data_get($offer, 'is_total_discount', false);
    $isTotalGift          = (bool) data_get($offer, 'is_total_gift', false);
    $isProductCountGift   = (bool) data_get($offer, 'is_product_count_gift', false);

    $discProductRaw  = data_get($offer, 'discount_value_product');
    $discDeliveryRaw = data_get($offer, 'discount_value_delivery');
    $totalDiscount   = (float) data_get($offer, 'total_discount', 0);
    $giftThreshold   = (int)   data_get($offer, 'products_count_to_get_gift_offer', 0);
    $giftAmount      = (float) data_get($offer, 'total_gift', 0);

    $discProduct  = $asPercentOrFixed($discProductRaw);
    $discDelivery = $asPercentOrFixed($discDeliveryRaw);

    if ($offer) {
        if ($isDiscount) {
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

            $linesCount = max(1, $items->count());
            $discountAmount = $items->sum(function ($it) use ($discProduct, $linesCount) {
                $lineTotal = (float)$it->price * (int)$it->quantity;
                if (!$discProduct) return 0;

                return match($discProduct['type']) {
                    'fraction' => $lineTotal * $discProduct['value'],
                    'percent'  => $lineTotal * ($discProduct['value'] / 100),
                    'fixed'    => min($lineTotal, $discProduct['value'] / $linesCount),
                    default    => 0,
                };
            });

        } elseif ($isTotalDiscount) {
            $offerSummary[] = __('adminlte::adminlte.is_total_discount') ?: 'Type: Total discount';
            $offerSummary[] = __('adminlte::adminlte.total_amount') . ': ' . number_format($totalDiscount, 2);
            $discountAmount = min($totalDiscount, $subtotal);

        } elseif ($isTotalGift || $isProductCountGift) {
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

    $statusLabelMap = [
        0 => __('adminlte::adminlte.pending')   ?: 'Pending',
        1 => __('adminlte::adminlte.accepted')  ?: 'Accepted',
        2 => __('adminlte::adminlte.rejected')  ?: 'Rejected',
        3 => __('adminlte::adminlte.shipped')   ?: 'Shipped',
    ];
    $statusLabel = $order->status_label ?? ($statusLabelMap[$order->status] ?? 'Unknown');

    $isShipped = (int)$order->status === (int)$SHIPPED_STATUS;

    // ✅ Transportation Way + Country/City (from relation trnasparation)
    $wayObj = $order->trnasparation; // already with(['country','city']) if you applied it
    $wayName = $isAr ? ($wayObj?->name_ar ?? $wayObj?->name_en) : ($wayObj?->name_en ?? $wayObj?->name_ar);

    $countryName = $isAr
        ? ($wayObj?->country?->name_ar ?? $wayObj?->country?->name_en)
        : ($wayObj?->country?->name_en ?? $wayObj?->country?->name_ar);

    $cityName = $isAr
        ? ($wayObj?->city?->name_ar ?? $wayObj?->city?->name_en)
        : ($wayObj?->city?->name_en ?? $wayObj?->city?->name_ar);

    $daysCount = $order->days_count ?? $wayObj?->days_count ?? null;

    // ✅ dynamic colspan for empty table row
    $cols = 6; // product,color,qty,price,total,actions
    if ($offer) $cols++;
    if ($isDiscount) $cols++;
@endphp

<div class="order-wrap">

  {{-- HERO --}}
  <div class="order-hero">
    <div class="order-hero-top">
      <div>
        <h2 class="order-title">
          {{ __('adminlte::adminlte.order') ?: 'Order' }} #{{ $order->id }}
        </h2>
        <div class="order-sub">
          <span class="pill"><i class="fas fa-badge-check"></i> {{ __('adminlte::adminlte.status') ?: 'Status' }}:
            <span class="ml-1 badge-soft text-uppercase">{{ $statusLabel }}</span>
          </span>
          <span class="pill"><i class="fas fa-user"></i> {{ __('adminlte::adminlte.user_name') ?: 'Customer' }}:
            <strong>{{ $order->user?->name ?? '-' }}</strong>
          </span>
          <span class="pill"><i class="fas fa-user-tie"></i> {{ __('adminlte::adminlte.employee') ?: 'Employee' }}:
            <strong>{{ $order->employee?->name ?? '-' }}</strong>
          </span>
          <span class="pill"><i class="fas fa-clock"></i> {{ optional($order->created_at)->format('Y-m-d H:i') }}</span>
        </div>
      </div>

      <div class="d-flex gap-2">
        <a class="btn btn-warning btn-round mr-2" href="{{ route('orders.edit',$order) }}">
          <i class="fas fa-edit mr-1"></i> {{ __('adminlte::adminlte.edit') }}
        </a>
        <a class="btn btn-secondary btn-round" href="{{ url()->previous() }}">
          <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.go_back') }}
        </a>
      </div>
    </div>

    @if($order->notes)
      <div class="mt-3 stat-card">
        <div class="small" style="color:var(--muted);font-weight:800;">
          {{ __('adminlte::adminlte.notes') ?: 'Notes' }}
        </div>
        <div style="font-weight:800;">{{ $order->notes }}</div>
      </div>
    @endif
  </div>

  {{-- ✅ Shipment section only when shipped --}}
  @if($isShipped)
    <div class="card glass mb-3">
      <div class="card-header">
        <span><i class="fas fa-truck mr-2"></i>{{ __('adminlte::adminlte.shipment') ?? 'Shipment' }}</span>
        <p class="hint">{{ __('adminlte::adminlte.required_when_shipped') ?? 'Shipment details when order is shipped.' }}</p>
      </div>

      <div class="card-body">
        <div class="stat-grid">

          <div class="stat-card">
            <strong>{{ __('adminlte::adminlte.transportation_way') ?? 'Transportation Way' }}</strong>

            <div class="stat-row">
              <div class="kpi">
                <div class="lbl">{{ __('adminlte::adminlte.transportation_way') ?? 'Way' }}</div>
                <div class="val">{{ $wayName ?: '-' }}</div>
              </div>

              <div class="kpi">
                <div class="lbl">{{ __('adminlte::adminlte.country') ?? 'Country' }}</div>
                <div class="val">{{ $countryName ?: '-' }}</div>
              </div>

              <div class="kpi">
                <div class="lbl">{{ __('adminlte::adminlte.city') ?? 'City' }}</div>
                <div class="val">{{ $cityName ?: '-' }}</div>
              </div>

              <div class="kpi">
                <div class="lbl">{{ __('adminlte::adminlte.days_count') ?? 'Days' }}</div>
                <div class="val">{{ $daysCount !== null ? (int)$daysCount : '-' }}</div>
              </div>
            </div>

            @if(!$wayObj)
              <div class="text-muted mt-2" style="font-weight:700;">
                {{ __('adminlte::adminlte.no_data_found') ?? 'No transportation way selected' }}
              </div>
            @endif
          </div>

        </div>
      </div>
    </div>
  @endif

  {{-- ✅ باقي الصفحة (Summary/Items/Delete Modal) زي ما هي عندك --}}
  {{-- SUMMARY + OFFER --}}
  <div class="card glass mb-3">
    <div class="card-header">
      <span><i class="fas fa-chart-pie mr-2"></i>{{ __('adminlte::adminlte.summary') ?? 'Summary' }}</span>
      <p class="hint">{{ __('adminlte::adminlte.quick_overview') ?? 'Quick overview of totals and offer.' }}</p>
    </div>

    <div class="card-body">
      <div class="stat-grid">
        <div class="stat-card">
          <div class="d-flex align-items-center justify-content-between">
            <strong>{{ __('adminlte::adminlte.totals') ?? 'Totals' }}</strong>
            <span class="badge badge-info badge-soft">{{ $itemCount }} {{ __('adminlte::adminlte.items') ?: 'items' }}</span>
          </div>

          <div class="stat-row">
            <div class="kpi">
              <div class="lbl">{{ __('adminlte::adminlte.sub_total') ?: 'Subtotal' }}</div>
              <div class="val">{{ number_format($subtotal, 2) }}</div>
            </div>
            <div class="kpi">
              <div class="lbl">{{ __('adminlte::adminlte.discount') ?: 'Discount' }}</div>
              <div class="val">-{{ number_format($discountAmount, 2) }}</div>
            </div>
            <div class="kpi">
              <div class="lbl">{{ __('adminlte::adminlte.total_amount') ?: 'Grand Total' }}</div>
              <div class="val">{{ number_format($grandTotal, 2) }}</div>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="d-flex align-items-center justify-content-between">
            <strong>{{ __('adminlte::adminlte.offer_details') ?: 'Offer Details' }}</strong>
            <span class="badge-soft">
              {{ $offer?->name_en ?? $offer?->name_ar ?? '-' }}
            </span>
          </div>

          <div class="mt-2">
            @if($offer)
              <ul class="mb-0" style="padding-left:18px;">
                @forelse($offerSummary as $line)
                  <li style="font-weight:700;color:rgba(0,0,0,.72)">{{ $line }}</li>
                @empty
                  <li class="text-muted">{{ __('adminlte::adminlte.no_data_found') ?: 'No data found' }}</li>
                @endforelse
              </ul>
            @else
              <div class="text-muted" style="font-weight:700;">{{ __('adminlte::adminlte.no_data_found') ?: 'No data found' }}</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ITEMS (بدون تغيير) --}}
  <div class="card glass">
    <div class="card-header">
      <span><i class="fas fa-box-open mr-2"></i>{{ __('adminlte::adminlte.order_items') ?: 'Order Items' }}</span>
      <span class="badge-soft">{{ $itemCount }} {{ __('adminlte::adminlte.items') ?: 'items' }}</span>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive table-wrap">
        <table class="table mb-0 align-middle">
          <thead>
            <tr>
              <th style="min-width:260px;">
                {{ __('adminlte::menu.product') ?: 'Product' }}
                <small class="text-muted d-block">EN / AR</small>
              </th>
              <th>{{ __('adminlte::adminlte.color') ?: 'Color' }}</th>
              <th>{{ __('adminlte::adminlte.quantity') ?: 'Qty' }}</th>
              @if($offer) <th>{{ __('adminlte::adminlte.offer_name') ?: 'Offer' }}</th> @endif
              <th>{{ __('adminlte::adminlte.price') ?: 'Unit Price' }}</th>
              <th>{{ __('adminlte::adminlte.total_amount') ?: 'Line Total' }}</th>
              @if($isDiscount)
                <th>{{ __('adminlte::adminlte.effective_total') ?: 'After Discount' }}</th>
              @endif
              <th style="width:140px;">{{ __('adminlte::adminlte.actions') ?: 'Actions' }}</th>
            </tr>
          </thead>

          <tbody>
          @forelse($items as $it)
            @php
              $product  = $it->product;
              $nameEn   = $product?->name_en ?? $product?->name ?? '';
              $nameAr   = $product?->name_ar ?? '';
              $sku      = $product?->sku ?? $it->product_id;
              $lineTotal = (float)$it->price * (int)$it->quantity;
              $afterDisc = $effectiveLineTotal($it);
              $displayName = $nameEn ?: ($nameAr ?: ('#'.$it->id));
            @endphp

            <tr>
              <td>
                <div class="d-flex flex-column">
                  <div class="name-badges">
                    @if($nameEn)
                      <div>
                        <span class="badge badge-light mr-1">{{ __('adminlte::adminlte.en') ?: 'EN' }}</span>
                        <strong>{{ Str::limit($nameEn, 70) }}</strong>
                      </div>
                    @endif

                    @if($nameAr)
                      <div class="mt-1">
                        <span class="badge badge-light mr-1">{{ __('adminlte::adminlte.ar') ?: 'AR' }}</span>
                        <strong>{{ Str::limit($nameAr, 70) }}</strong>
                      </div>
                    @endif
                  </div>

                  <span class="text-muted small mt-1" style="font-weight:700;">
                    {{ __('adminlte::adminlte.product_id') ?: 'Product ID' }}: {{ $sku }}
                  </span>
                </div>
              </td>

              <td>{{ $it->color ?: '-' }}</td>
              <td><span class="badge-soft">{{ $it->quantity }}</span></td>

              @if($offer)
                <td><span class="badge badge-info badge-soft">{{ $offer->name_en ?? $offer->name_ar }}</span></td>
              @endif

              <td>{{ number_format((float)$it->price, 2) }}</td>
              <td><strong>{{ number_format($lineTotal, 2) }}</strong></td>

              @if($isDiscount)
                <td>
                  <span class="d-block" style="font-weight:900;">{{ number_format($afterDisc, 2) }}</span>
                  @if($afterDisc < $lineTotal)
                    <small class="text-success d-block" style="font-weight:800;">
                      -{{ number_format($lineTotal - $afterDisc, 2) }}
                    </small>
                  @endif
                </td>
              @endif

              <td>
                <button type="button"
                        class="btn btn-sm btn-danger btn-round js-delete-item"
                        data-toggle="modal"
                        data-target="#deleteItemModal"
                        data-action="{{ route('orders.items.destroy', [$order, $it]) }}"
                        data-name="{{ $displayName }}">
                  <i class="fas fa-trash mr-1"></i> {{ __('adminlte::adminlte.delete') }}
                </button>
              </td>
            </tr>

          @empty
            <tr>
              <td colspan="{{ $cols }}" class="text-center text-muted py-4" style="font-weight:800;">
                {{ __('adminlte::adminlte.no_data_found') ?: 'No data found' }}
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="totals">
      <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6">
          <table class="table table-sm mb-0">
            <tr>
              <th style="width:40%;">{{ __('adminlte::adminlte.sub_total') ?: 'Subtotal' }}</th>
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

</div>

{{-- Delete modal + JS (نفس اللي عندك) --}}
{{-- ... --}}
@endsection
