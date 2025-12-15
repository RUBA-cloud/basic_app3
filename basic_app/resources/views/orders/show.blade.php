@extends('adminlte::page')
@section('title', __('adminlte::adminlte.order_module'))

@section('adminlte_css')
@parent
<style>
  :root{
    --card-radius:18px;
    --soft-border: rgba(0,0,0,.07);
    --soft-shadow: 0 18px 40px rgba(0,0,0,.10);
    --muted: rgba(0,0,0,.55);
    --danger-soft: rgba(220,53,69,.08);
    --danger-border: rgba(220,53,69,.20);
  }

  .order-wrap{max-width:1100px;margin:0 auto}
  .order-hero{
    border-radius:22px;
    padding:18px 18px;
    margin-bottom:14px;
    background:linear-gradient(135deg, rgba(0,0,0,.06), rgba(0,0,0,.02));
    border:1px solid var(--soft-border);
  }
  .order-hero-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
  .order-title{margin:0;font-size:1.35rem;font-weight:900;letter-spacing:.2px}
  .order-sub{color:var(--muted);font-size:.9rem;margin-top:6px;display:flex;flex-wrap:wrap;gap:10px}
  .pill{
    display:inline-flex;align-items:center;gap:8px;
    padding:6px 10px;border-radius:999px;
    background:rgba(0,0,0,.04);border:1px solid var(--soft-border);
    font-size:.85rem;font-weight:700;color:rgba(0,0,0,.75);
  }

  .glass{
    border-radius:var(--card-radius);
    overflow:hidden;
    border:1px solid var(--soft-border);
    box-shadow:var(--soft-shadow);
    background:rgba(255,255,255,.92);
    backdrop-filter: blur(10px);
  }
  .glass .card-header{
    background:transparent;
    border-bottom:1px solid var(--soft-border);
    padding:14px 16px;
    font-weight:900;
    display:flex;align-items:center;justify-content:space-between;gap:10px;
  }
  .hint{margin:0;font-size:.82rem;color:var(--muted);font-weight:600}

  .stat-grid{display:grid;grid-template-columns:1fr;gap:12px}
  @media (min-width:992px){.stat-grid{grid-template-columns:1.2fr .8fr}}
  .stat-card{
    border-radius:16px;border:1px solid var(--soft-border);
    background:rgba(0,0,0,.02);
    padding:14px;
  }
  .stat-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:8px}
  .kpi{
    flex:1 1 160px;
    border-radius:14px;
    border:1px solid var(--soft-border);
    background:rgba(255,255,255,.7);
    padding:10px 12px;
  }
  .kpi .lbl{font-size:.78rem;color:var(--muted);font-weight:700}
  .kpi .val{font-size:1.05rem;font-weight:900;margin-top:3px}

  .badge-soft{
    border-radius:999px;
    padding:.42rem .6rem;
    font-weight:900;
    letter-spacing:.35px;
    border:1px solid var(--soft-border);
    background:rgba(0,0,0,.04);
    color:rgba(0,0,0,.78);
  }

  .table-wrap{border-radius:16px;overflow:hidden;border:1px solid var(--soft-border)}
  .table thead th{
    background:rgba(0,0,0,.03) !important;
    border-bottom:1px solid var(--soft-border);
    font-weight:900;
    color:rgba(0,0,0,.78);
    white-space:nowrap;
  }
  .table td{border-top:1px solid rgba(0,0,0,.06); vertical-align:middle}
  .name-badges .badge{
    border-radius:999px;
    border:1px solid var(--soft-border);
    background:rgba(0,0,0,.03);
    font-weight:900;
  }

  .totals{
    border-top:1px dashed rgba(0,0,0,.12);
    padding:14px 16px;
    background:rgba(0,0,0,.02);
  }
  .totals table th{font-weight:900;color:rgba(0,0,0,.75)}
  .totals table td{font-weight:800}

  .btn-round{border-radius:14px;font-weight:900;padding:.6rem .95rem}
  .btn-danger.btn-round{box-shadow:0 10px 22px rgba(220,53,69,.18)}
  .btn-warning.btn-round{box-shadow:0 10px 22px rgba(255,193,7,.18)}
  .btn-secondary.btn-round{box-shadow:0 10px 22px rgba(108,117,125,.14)}

  /* ===== Custom Delete Modal ===== */
  .modal-content{border-radius:18px;border:1px solid var(--soft-border); overflow:hidden}
  .modal-header{border-bottom:1px solid var(--soft-border); background:linear-gradient(135deg,var(--danger-soft),rgba(0,0,0,0))}
  .modal-footer{border-top:1px solid var(--soft-border); background:rgba(0,0,0,.02)}
  .delete-hero{
    border:1px solid var(--danger-border);
    background:linear-gradient(135deg,var(--danger-soft),rgba(255,255,255,.65));
    border-radius:16px;
    padding:12px 12px;
  }
  .delete-hero .title{font-weight:900;font-size:1.05rem;margin:0;display:flex;align-items:center;gap:10px}
  .delete-hero .sub{margin:6px 0 0;color:var(--muted);font-weight:700;font-size:.9rem}
  .danger-chip{
    display:inline-flex;align-items:center;gap:8px;
    padding:6px 10px;border-radius:999px;
    background:rgba(220,53,69,.08);
    border:1px solid rgba(220,53,69,.22);
    color:rgba(150,20,35,.95);
    font-weight:900;
  }
  .soft-field{
    border-radius:14px!important;
    border:1px solid rgba(0,0,0,.10)!important;
    box-shadow:none!important;
    padding:.7rem .9rem;
  }
  .soft-field:focus{
    border-color:rgba(0,0,0,.22)!important;
    box-shadow:0 0 0 .18rem rgba(0,0,0,.06)!important
  }
</style>
@endsection

@section('content')

@php
    use Illuminate\Support\Str;

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
        3 => __('adminlte::adminlte.completed') ?: 'Completed',
    ];
    $statusLabel = $order->status_label ?? ($statusLabelMap[$order->status] ?? 'Unknown');
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
          <span class="pill"><i class="fas fa-badge-check"></i> {{ __('adminlte::adminlte.status') ?: 'Status' }}: <span class="ml-1 badge-soft text-uppercase">{{ $statusLabel }}</span></span>
          <span class="pill"><i class="fas fa-user"></i> {{ __('adminlte::adminlte.user_name') ?: 'Customer' }}: <strong>{{ $order->user?->name ?? '-' }}</strong></span>
          <span class="pill"><i class="fas fa-user-tie"></i> {{ __('adminlte::adminlte.employee') ?: 'Employee' }}: <strong>{{ $order->employee?->name ?? '-' }}</strong></span>
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

  {{-- ITEMS --}}
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
                {{-- ✅ Custom delete dialog trigger --}}
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
              <td colspan="8" class="text-center text-muted py-4" style="font-weight:800;">
                {{ __('adminlte::adminlte.no_data_found') ?: 'No data found' }}
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Totals footer --}}
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

{{-- ✅ ONE Custom Delete Modal --}}
<div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" id="deleteItemForm" action="#" class="modal-content">
      @csrf
      @method('DELETE')

      <div class="modal-header">
        <div class="d-flex align-items-center justify-content-between w-100">
          <div>
            <div class="danger-chip">
              <i class="fas fa-exclamation-triangle"></i>
              {{ __('adminlte::adminlte.delete') }}
            </div>
          </div>
          <button type="button" class="close" data-disiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>

      <div class="modal-body">
        <div class="delete-hero">
          <p class="title mb-0">
            <i class="fas fa-trash"></i>
            {{ __('adminlte::adminlte.confirm_to_delete_product') ?? 'Confirm Delete' }}
          </p>
          <p class="sub">
            {{ __('adminlte::adminlte.confirm_to_delete_product') ?? 'You are about to delete:' }}
            <strong id="deleteItemName"></strong>
          </p>
        </div>

        <div class="mt-3">
          <label style="font-weight:900;">
            {{ __('adminlte::adminlte.please_add_reason_cancel') ?? 'Reason' }}
          </label>
          <textarea name="note" id="deleteNote" class="form-control soft-field" rows="3" required
                    placeholder="{{ __('adminlte::adminlte.please_add_reason_cancel') }}"></textarea>
          <small class="text-muted d-block mt-2" style="font-weight:700;">
            {{ __('adminlte::adminlte.required') ?? 'Required' }}
          </small>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-round" data-dismiss="modal">
          {{ __('adminlte::adminlte.cancel') }}
        </button>
        <button type="submit" class="btn btn-danger btn-round" id="deleteSubmitBtn">
          <i class="fas fa-trash mr-1"></i> {{ __('adminlte::adminlte.delete') }}
        </button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('deleteItemForm');
  const nameEl = document.getElementById('deleteItemName');
  const noteEl = document.getElementById('deleteNote');
  const submitBtn = document.getElementById('deleteSubmitBtn');

  // When modal opens (Bootstrap 4)
  $('#deleteItemModal').on('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const action = btn.getAttribute('data-action');
    const name = btn.getAttribute('data-name') || '';

    form.setAttribute('action', action);
    nameEl.textContent = name;

    if (noteEl) noteEl.value = '';
    if (submitBtn) submitBtn.disabled = false;
  });

  // Focus textarea after modal shown
  $('#deleteItemModal').on('shown.bs.modal', function () {
    if (noteEl) noteEl.focus();
  });

  // Small UX: disable button on submit to prevent double click
  form.addEventListener('submit', function () {
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("adminlte::adminlte.please_wait") ?? "Please wait" }}';
    }
  });
});
</script>
@endpush
