{{-- resources/views/payment/show.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.payment'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-money-check-alt me-2 text-primary"></i>
            @if (app()->getLocale() === 'ar')
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.payment') }}
            @else
                {{ __('adminlte::adminlte.payment') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>

        <div>
            <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-primary px-4 py-2">
                <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
            </a>
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
            </a>
        </div>
    </div>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">

            {{-- Details --}}
            <div class="col-12">
                <div class="row gy-3">

                    {{-- Name EN --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div id="payment-name-en" class="fs-5 fw-bold text-dark">
                            {{ $payment->name_en }}
                        </div>
                    </div>

                    {{-- Name AR --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div id="payment-name-ar" class="fs-5 fw-bold text-dark">
                            {{ $payment->name_ar }}
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        <small class="text-muted d-block mb-1">{{ __('adminlte::adminlte.is_active') }}</small>
                        <span id="payment-status"
                              class="badge {{ $payment->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                            @if($payment->is_active)
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            @else
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            @endif
                        </span>
                    </div>

                </div>
            </div>

        </div>
    </x-adminlte-card>
</div>

{{-- Listener anchor for broadcasting (for reference / id binding) --}}
<div id="payment-listener"
     data-channel="payments"
     data-events='["payment_updated","PaymentUpdated"]'
     data-payment-id="{{ $payment->id }}">
</div>
@endsection

@push('js')
@once
<script>
(function () {
  'use strict';

  function norm(v) {
    if (v === undefined || v === null) return '';
    return String(v);
  }

  // Update DOM from broadcast payload
  function applyPaymentPayload(payload) {
    if (!payload) return;

    const p = payload.payment ?? payload ?? {};

    const anchor    = document.getElementById('payment-listener');
    const currentId = anchor ? anchor.dataset.paymentId : null;
    if (currentId && p.id && String(p.id) !== String(currentId)) {
      // event for another payment → ignore
      return;
    }

    // name_en
    const nameEnEl = document.getElementById('payment-name-en');
    if (nameEnEl && p.name_en !== undefined) {
      nameEnEl.textContent = norm(p.name_en) || '-';
    }

    // name_ar
    const nameArEl = document.getElementById('payment-name-ar');
    if (nameArEl && p.name_ar !== undefined) {
      nameArEl.textContent = norm(p.name_ar) || '-';
    }

    // status
    const statusEl = document.getElementById('payment-status');
    if (statusEl && p.is_active !== undefined && p.is_active !== null) {
      const on = !!Number(p.is_active);
      statusEl.classList.remove('bg-success','bg-danger');
      statusEl.classList.add(on ? 'bg-success' : 'bg-danger');
      statusEl.innerHTML = on
        ? '<i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}'
        : '<i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}';
    }

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_) {}
    }

    console.log('[payments show] updated from broadcast', p);
  }

  // Optional global helper
  window.updatePaymentShow = applyPaymentPayload;

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('payment-listener');
    if (!anchor) {
      console.warn('[payments show] listener anchor not found');
      return;
    }

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["payment_updated"]');
    } catch (_) {
      events = ['payment_updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['payment_updated'];
    }

    const handler = function (e) {
      // Try common shapes: {payment: {...}} or flat payload
      applyPaymentPayload(e && (e.payment ?? e.payload ?? e));
    };

    // Register so your layout-level broadcaster can attach later
    window.__pageBroadcasts.push({
      channel: 'payments',          // must match broadcastOn()
      event:   'payment_updated',   // must match broadcastAs()
      handler: handler
    });

    // If AppBroadcast is already booted (like in your layout), subscribe now
    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe('payments', 'payment_updated', handler);
      console.info('[payments show] subscribed via AppBroadcast → payments / payment_updated');
    } else {
      console.info('[payments show] registered in __pageBroadcasts; layout will subscribe later.');
    }
  });
})();
</script>
@endonce
@endpush
