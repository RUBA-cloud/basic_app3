{{-- resources/views/order_status/show.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.order_status'))

@php
  // Map status id -> label (عدل الأرقام حسب نظامك)
  $statusMap = [
    1 => __('adminlte::adminlte.pending')   ?? 'Pending',
    2 => __('adminlte::adminlte.accepted')    ?? 'Accept',
    3 => __('adminlte::adminlte.rejected')    ?? 'Reject',
    4 => __('adminlte::adminlte.complete')  ?? 'Complete',
    5 => __('adminlte::adminlte.delivered') ?? 'Delivered',
  ];

  $statusVal = (int)($orderStatus->status ?? 0);
  $statusText = $statusMap[$statusVal] ?? '-';
@endphp

@section('content')
<div class="row justify-content-center">
  <div class="col-md-10">

    <x-adminlte-card title="{{ __('adminlte::adminlte.order_status') }}"
                     theme="info"
                     icon="fas fa-info-circle"
                     collapsible>

      <div class="row mb-3">
        <div class="col-md-6">
          <strong>{{ __('adminlte::adminlte.name_en')}}:</strong>
          <div id="order-status-name-en" class="form-control-plaintext">
            {{ $orderStatus->name_en ?? '-' }}
          </div>
        </div>
        <div class="col-md-6">
          <strong>{{ __('adminlte::adminlte.name_ar')}}:</strong>
          <div id="order-status-name-ar" class="form-control-plaintext">
            {{ $orderStatus->name_ar ?? '-' }}
          </div>
        </div>
      </div>

      {{-- Status --}}
      <div class="mb-3">
        <strong>{{ __('adminlte::adminlte.status') }}:</strong><br>
        <span id="order-status-status" class="badge bg-info">
          {{ $statusText }}
        </span>
      </div>

      {{-- Is Active --}}
      <div class="mb-3">
        <strong>{{ __('adminlte::adminlte.is_active') }}:</strong><br>
        <span id="order-status-is-active"
              class="badge {{ $orderStatus->is_active ? 'bg-success' : 'bg-secondary' }}">
          {{ $orderStatus->is_active
              ? __('adminlte::adminlte.active')
              : __('adminlte::adminlte.inactive') }}
        </span>
      </div>

      <div class="d-flex justify-content-end mt-4">
        <a href="{{ route('order_status.edit', $orderStatus->id) }}" class="btn btn-primary">
          <i class="fas fa-edit mr-1"></i> {{ __('adminlte::adminlte.edit') }}
        </a>
      </div>

    </x-adminlte-card>

  </div>
</div>

{{-- Listener anchor for broadcasting --}}
<div id="order-status-listener"
     data-channel="order_status"
     data-events='["order_status_updated","OrderStatusUpdated"]'
     data-order-status-id="{{ $orderStatus->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
  'use strict';

  const STATUS_MAP = {
    1: @json(__('adminlte::adminlte.pending')   ?? 'Pending'),
    2: @json(__('adminlte::adminlte.accept')    ?? 'Accept'),
    3: @json(__('adminlte::adminlte.reject')    ?? 'Reject'),
    4: @json(__('adminlte::adminlte.complete')  ?? 'Complete'),
    5: @json(__('adminlte::adminlte.delivered') ?? 'Delivered'),
  };

  function norm(v) {
    if (v === undefined || v === null) return '';
    return String(v);
  }

  function updateDomFromPayload(payload) {
    if (!payload) return;

    const t = payload.orderStatus ?? payload.order_status ?? payload ?? {};

    const anchor    = document.getElementById('order-status-listener');
    const currentId = anchor ? anchor.dataset.orderStatusId : null;
    if (currentId && t.id && String(t.id) !== String(currentId)) return;

    const nameEnEl = document.getElementById('order-status-name-en');
    if (nameEnEl) nameEnEl.textContent = norm(t.name_en) || '-';

    const nameArEl = document.getElementById('order-status-name-ar');
    if (nameArEl) nameArEl.textContent = norm(t.name_ar) || '-';

    // status badge
    if (t.status !== undefined) {
      const stEl = document.getElementById('order-status-status');
      if (stEl) {
        const s = Number(t.status);
        stEl.textContent = STATUS_MAP[s] || '-';
      }
    }

    // is_active badge
    if (t.is_active !== undefined) {
      const el = document.getElementById('order-status-is-active');
      if (el) {
        const on = !!Number(t.is_active);
        el.classList.remove('bg-success', 'bg-secondary');
        el.classList.add(on ? 'bg-success' : 'bg-secondary');
        el.textContent = on
          ? @json(__('adminlte::adminlte.active'))
          : @json(__('adminlte::adminlte.inactive'));
      }
    }

    if (window.toastr) {
      toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
    }
  }

  window.updateOrderStatusShow = updateDomFromPayload;

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('order-status-listener');
    if (!anchor) return;

    let events;
    try { events = JSON.parse(anchor.dataset.events || '["order_status_updated"]'); }
    catch (_) { events = ['order_status_updated']; }

    const handler = function (e) {
      updateDomFromPayload(e && (e.orderStatus ?? e.order_status ?? e));
    };

    window.__pageBroadcasts = window.__pageBroadcasts || [];
    events.forEach(function (ev) {
      window.__pageBroadcasts.push({ channel: 'order_status', event: ev, handler });

      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe('order_status', ev, handler);
      }
    });
  });
})();
</script>
@endpush
