{{-- resources/views/orders/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::menu.orders'))

@push('css')
<style>
/* ============================================================
   ORDERS PAGE — Custom Design
   Uses CSS variables from master.blade.php brand tokens
============================================================ */

/* ── Page header strip ── */
.orders-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.orders-title {
    display: flex;
    align-items: center;
    gap: .75rem;
}

.orders-title-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    background: var(--brand-main);
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
    flex-shrink: 0;
}

.orders-title-text {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--brand-text);
    letter-spacing: -.02em;
    margin: 0;
}

.orders-title-sub {
    font-size: .75rem;
    color: var(--brand-text);
    opacity: .45;
    margin: 0;
    letter-spacing: .01em;
}

/* ── Status filter pill bar ── */
.status-filter-wrap {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-wrap: wrap;
}

.status-pill-form {
    display: contents; /* allows pills to flow naturally */
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: .78rem;
    font-weight: 600;
    border: 1.5px solid rgba(0,0,0,.10);
    background: var(--brand-card);
    color: var(--brand-text);
    cursor: pointer;
    transition: all .18s ease;
    text-decoration: none;
    white-space: nowrap;
    letter-spacing: .01em;
}
.status-pill:hover {
    border-color: var(--brand-main);
    color: var(--brand-main);
    background: var(--brand-card);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}
.status-pill.active {
    background: var(--brand-main);
    border-color: var(--brand-main);
    color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,.18);
}
.status-pill .status-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: currentColor;
    opacity: .7;
    flex-shrink: 0;
}
.status-pill.active .status-dot { opacity: 1; background: rgba(255,255,255,.8); }

/* ── Metric cards row ── */
.orders-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: .75rem;
    margin-bottom: 1.25rem;
}

.metric-card {
    background: var(--brand-card);
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 14px;
    padding: 1rem 1.1rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    transition: box-shadow .2s, transform .2s;
    animation: metric-in .35s ease both;
}
.metric-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.08);
    transform: translateY(-2px);
}
@keyframes metric-in {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.metric-card:nth-child(1) { animation-delay: .04s; }
.metric-card:nth-child(2) { animation-delay: .08s; }
.metric-card:nth-child(3) { animation-delay: .12s; }
.metric-card:nth-child(4) { animation-delay: .16s; }

.metric-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem;
    flex-shrink: 0;
}
.metric-icon.blue   { background: rgba(59,130,246,.12);  color: #3b82f6; }
.metric-icon.green  { background: rgba(16,185,129,.12);  color: #10b981; }
.metric-icon.amber  { background: rgba(245,158,11,.12);  color: #f59e0b; }
.metric-icon.rose   { background: rgba(244,63,94,.12);   color: #f43f5e; }

.metric-body {}
.metric-value {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--brand-text);
    line-height: 1;
    letter-spacing: -.02em;
}
.metric-label {
    font-size: .72rem;
    color: var(--brand-text);
    opacity: .45;
    margin-top: 2px;
    font-weight: 500;
    letter-spacing: .03em;
    text-transform: uppercase;
}

/* ── Main card ── */
.orders-card {
    background: var(--brand-card);
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,.07);
    overflow: hidden;
    animation: card-in .3s ease both;
}
@keyframes card-in {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.orders-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(0,0,0,.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
}

.orders-card-title {
    font-size: .82rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--brand-text);
    opacity: .5;
    margin: 0;
}

/* ── Action buttons area ── */
.orders-actions {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-wrap: wrap;
}

/* ── Table overrides ── */
.orders-card .table-responsive { border-radius: 0; }

.orders-card table.table {
    margin-bottom: 0;
}

.orders-card .table thead th {
    font-size: 10.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: .08em !important;
    opacity: .45 !important;
    border-bottom: 2px solid rgba(0,0,0,.06) !important;
    padding: 10px 16px !important;
    white-space: nowrap;
    background: var(--brand-card) !important;
}

.orders-card .table tbody td {
    padding: 12px 16px !important;
    border-bottom: 1px solid rgba(0,0,0,.04) !important;
    vertical-align: middle !important;
    font-size: 13.5px !important;
}

.orders-card .table tbody tr {
    transition: background .15s ease;
}
.orders-card .table tbody tr:hover {
    background: rgba(0,0,0,.025) !important;
}
.orders-card .table tbody tr:last-child td {
    border-bottom: none !important;
}

/* Order ID badge */
.order-id-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: .72rem;
    font-weight: 700;
    font-family: 'IBM Plex Mono', monospace;
    background: rgba(0,0,0,.05);
    color: var(--brand-text);
    letter-spacing: .02em;
}

/* Status badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: .72rem;
    font-weight: 600;
    letter-spacing: .02em;
}
.status-badge::before {
    content: '';
    width: 5px; height: 5px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}
.status-badge.pending   { background: rgba(245,158,11,.12); color: #d97706; }
.status-badge.active    { background: rgba(16,185,129,.12); color: #059669; }
.status-badge.completed { background: rgba(59,130,246,.12); color: #2563eb; }
.status-badge.cancelled { background: rgba(244,63,94,.12);  color: #e11d48; }
.status-badge.default   { background: rgba(0,0,0,.06);      color: var(--brand-text); opacity: .7; }

/* Action buttons in table */
.tbl-action-btn {
    width: 30px; height: 30px;
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,.08);
    background: transparent;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .78rem;
    transition: all .15s ease;
    color: var(--brand-text);
    opacity: .6;
    cursor: pointer;
    text-decoration: none;
}
.tbl-action-btn:hover {
    opacity: 1;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,.1);
}
.tbl-action-btn.view  { border-color: rgba(59,130,246,.3);  }
.tbl-action-btn.view:hover  { background: rgba(59,130,246,.1);  color: #3b82f6; border-color: #3b82f6; }
.tbl-action-btn.edit  { border-color: rgba(16,185,129,.3);  }
.tbl-action-btn.edit:hover  { background: rgba(16,185,129,.1);  color: #10b981; border-color: #10b981; }
.tbl-action-btn.del   { border-color: rgba(244,63,94,.3);   }
.tbl-action-btn.del:hover   { background: rgba(244,63,94,.1);   color: #f43f5e; border-color: #f43f5e; }

/* ── Pagination overrides ── */
.orders-card .pagination {
    margin: 0;
}
.orders-card .pagination .page-link {
    border-radius: 8px !important;
    margin: 0 2px;
    font-size: 13px;
    font-weight: 500;
    border: 1px solid rgba(0,0,0,.08);
    color: var(--brand-text);
    background: var(--brand-card);
    transition: all .15s;
}
.orders-card .pagination .page-link:hover {
    background: var(--brand-main);
    border-color: var(--brand-main);
    color: #fff;
}
.orders-card .pagination .page-item.active .page-link {
    background: var(--brand-main);
    border-color: var(--brand-main);
    color: #fff;
    box-shadow: 0 3px 8px rgba(0,0,0,.15);
}
.orders-card .pagination-wrap {
    padding: .875rem 1.25rem;
    border-top: 1px solid rgba(0,0,0,.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .5rem;
}
.orders-card .pagination-info {
    font-size: .78rem;
    color: var(--brand-text);
    opacity: .45;
}

/* ── RTL flips ── */
body.rtl .orders-title-icon { margin-left: .75rem; margin-right: 0; }
body.rtl .orders-header      { flex-direction: row-reverse; }
body.rtl .orders-actions     { flex-direction: row-reverse; }
body.rtl .orders-card-header { flex-direction: row-reverse; }
body.rtl .status-filter-wrap { flex-direction: row-reverse; }
body.rtl .metric-card        { flex-direction: row-reverse; }

/* ── Empty state ── */
.orders-empty {
    padding: 3.5rem 1rem;
    text-align: center;
}
.orders-empty-icon {
    width: 64px; height: 64px;
    border-radius: 18px;
    background: rgba(0,0,0,.05);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem;
    margin: 0 auto 1rem;
    color: var(--brand-main);
    opacity: .6;
}
.orders-empty-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--brand-text);
    margin-bottom: .35rem;
}
.orders-empty-sub {
    font-size: .82rem;
    color: var(--brand-text);
    opacity: .4;
}

/* ── Responsive ── */
@media (max-width: 576px) {
    .orders-metrics { grid-template-columns: 1fr 1fr; }
    .orders-title-text { font-size: 1rem; }
    .status-pill { font-size: .72rem; padding: 4px 10px; }
}
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════
     PAGE HEADER
═══════════════════════════════════════════════════ --}}
<div class="orders-header">

    {{-- Title --}}
    <div class="orders-title">
        <div class="orders-title-icon">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div>
            <p class="orders-title-text">{{ __('adminlte::menu.orders') }}</p>
            <p class="orders-title-sub">
                {{ __('adminlte::adminlte.manage') }} · {{ now()->format('d M Y') }}
            </p>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="orders-actions">
        <x-action_buttons
            label="{{ __('adminlte::adminlte.orders') }}"
            addRoute="orders.create"
            historyRoute="orders.history"
            :showAdd="false"
            :goBack="false"
        />
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     METRIC SUMMARY CARDS
═══════════════════════════════════════════════════ --}}
@php
    /* These counts come from the controller.
       Fall back to 0 if not passed so the view never crashes. */
    $totalOrders     = $totalOrders     ?? 0;
    $pendingOrders   = $pendingOrders   ?? 0;
    $completedOrders = $completedOrders ?? 0;
    $cancelledOrders = $cancelledOrders ?? 0;
@endphp

<div class="orders-metrics">
    <div class="metric-card">
        <div class="metric-icon blue"><i class="fas fa-list-alt"></i></div>
        <div class="metric-body">
            <div class="metric-value">{{ number_format($totalOrders) }}</div>
            <div class="metric-label">{{ __('adminlte::adminlte.total') ?: 'Total' }}</div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon amber"><i class="fas fa-clock"></i></div>
        <div class="metric-body">
            <div class="metric-value">{{ number_format($pendingOrders) }}</div>
            <div class="metric-label">{{ __('adminlte::adminlte.pending') ?: 'Pending' }}</div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="metric-body">
            <div class="metric-value">{{ number_format($completedOrders) }}</div>
            <div class="metric-label">{{ __('adminlte::adminlte.completed') ?: 'Completed' }}</div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon rose"><i class="fas fa-times-circle"></i></div>
        <div class="metric-body">
            <div class="metric-value">{{ number_format($cancelledOrders) }}</div>
            <div class="metric-label">{{ __('adminlte::adminlte.cancelled') ?: 'Cancelled' }}</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     STATUS FILTER — pill bar
═══════════════════════════════════════════════════ --}}
<div class="status-filter-wrap mb-3">

    {{-- "All" pill --}}
    <a href="{{ url()->current() }}"
       class="status-pill {{ request('status') === null || request('status') === '' ? 'active' : '' }}">
        <span class="status-dot"></span>
        {{ __('adminlte::adminlte.all') ?: 'All' }}
    </a>

    @isset($orderStatus)
        @foreach($orderStatus as $st)
            <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except('status','page'), ['status' => $st->id])) }}"
               class="status-pill {{ (string)request('status') === (string)$st->id ? 'active' : '' }}">
                <span class="status-dot"></span>
                {{ app()->isLocale('ar') ? ($st->name_ar ?? $st->name_en) : ($st->name_en ?? $st->name_ar) }}
            </a>
        @endforeach
    @endisset

</div>

{{-- ═══════════════════════════════════════════════════
     MAIN ORDERS CARD
═══════════════════════════════════════════════════ --}}
<div class="orders-card">

    <div class="orders-card-header">
        <p class="orders-card-title">
            <i class="fas fa-table mr-1"></i>
            {{ __('adminlte::adminlte.orders_list') ?: 'Orders List' }}
        </p>

        {{-- Optional: search or extra controls could go here --}}
    </div>

    {{-- Livewire data table — unchanged logic, just sits inside the styled card --}}
    @php
        $fields = [
            [
                'key'   => app()->isLocale('ar') ? 'statusRel.name_ar' : 'statusRel.name_en',
                'label' => __('adminlte::adminlte.status') ?: 'Status',
            ],
            ['key' => 'user.name',     'label' => __('adminlte::adminlte.user_name') ?: 'User Name'],
            ['key' => 'offer.name_en', 'label' => (__('adminlte::adminlte.offer_name') ?: 'Offer Name').' (EN)'],
            ['key' => 'offer.name_ar', 'label' => (__('adminlte::adminlte.offer_name') ?: 'Offer Name').' (AR)'],
            ['key' => 'user.id',       'label' => __('adminlte::adminlte.user_id') ?: 'User ID'],
        ];
    @endphp

    <livewire:adminlte.data-table
        :fields="$fields"
        :model="\App\Models\Order::class"
        details-route="orders.show"
        edit-route="orders.edit"
        delete-route="orders.destroy"
        reactive-route="orders.reactivate"
        initial-route="{{ request()->fullUrl() }}"
        :search-in="['id']"
        :per-page="12"
        :filters="['status' => request('status')]"
    />

</div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    /*
     * ── CSRF fix for Livewire + Alpine requests ──────────────────
     * Livewire uses POST under the hood. The CSRF token must be
     * present in the meta tag so Livewire can read it.
     * master.blade.php already outputs:
     *   <meta name="csrf-token" content="{{ csrf_token() }}"/>
     *
     * If you're still seeing 419 on Livewire routes, add this to
     * bootstrap/app.php inside withMiddleware():
     *   $middleware->validateCsrfTokens(except: [
     *       'broadcasting/auth',
     *       'livewire/*',      // only if using Livewire v2
     *   ]);
     *
     * Livewire v3 handles CSRF automatically via its own mechanism.
     */

    /* ── Animate metric cards on load ── */
    document.querySelectorAll('.metric-card').forEach(function(card, i) {
        card.style.animationDelay = (i * 0.06) + 's';
    });

    /* ── Status pill active highlight from URL ── */
    var params  = new URLSearchParams(window.location.search);
    var current = params.get('status') || '';

    document.querySelectorAll('.status-pill').forEach(function(pill) {
        var href   = new URL(pill.href, window.location.origin);
        var pStatus = href.searchParams.get('status') || '';
        if (pStatus === current) {
            pill.classList.add('active');
        } else if (pill.href === window.location.href.split('?')[0] && current === '') {
            pill.classList.add('active');
        }
    });
});
</script>
@endpush