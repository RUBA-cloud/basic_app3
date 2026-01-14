@extends('adminlte::page')

@section('title', __('adminlte::adminlte.transpartation_type') . ' ' . __('adminlte::adminlte.details'))

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
@endphp

<style>
  /* logical spacing works for LTR & RTL */
  .mie-1 { margin-inline-end: .25rem; }
  .mie-2 { margin-inline-end: .5rem; }
  .mis-2 { margin-inline-start: .5rem; }
</style>

<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 fw-bold text-dark">
            @if ($isAr)
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.transpartation_type') }}
            @else
                {{ __('adminlte::adminlte.transpartation_type') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>
    </div>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">


            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Name EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div class="fs-5 fw-bold text-dark" id="transpartationType-name-en">{{ $transpartationType->name_en ?? '—' }}</div>
                    </div>

                    {{-- Name AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark" id="transpartationType-name-ar">{{ $transpartationType->name_ar ?? '—' }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($transpartationType->is_active)
                            <span id="transpartationType-status-badge" class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle mie-1"></i>
                                <span id="transpartationType-status-text">{{ __('adminlte::adminlte.active') }}</span>
                            </span>
                        @else
                            <span id="transpartationType-status-badge" class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle mie-1"></i>
                                <span id="transpartationType-status-text">{{ __('adminlte::adminlte.inactive') }}</span>
                            </span>
                        @endif
                    </div>


                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('transpartation_types.edit', $transpartationType->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit mie-2"></i>{{ __('adminlte::adminlte.edit') }}
                        </a>

                        <a href="{{ route('transpartation_types.index') }}" class="btn btn-outline-secondary mis-2 px-4 py-2">
                            <i class="fas fa-arrow-left mie-2"></i>{{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>

{{-- Listener anchor for window broadcasting --}}
<div id="transpartationType-show-listener"
     data-channel="transpartationTypes"
     data-events='["transpartationType_updated","SizeUpdated"]'
     data-transpartationType-id="{{ $transpartationType->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('transpartationType-show-listener');
    if (!anchor) {
      console.warn('[transpartationType-show] listener anchor not found');
      return;
    }

    const channelName = anchor.dataset.channel || 'transpartation-updated';

    let events;
    try {
      events = JSON.parse(anchor.dataset.events || '["transpartation.updated"]');
    } catch (_) {
      events = ['transpartation.updated'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['transpartation.updated'];
    }

    const currentId = anchor.dataset.transpartationTypeId || null;

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName);

      const handler = function (e) {
        // accept shapes: { payload: { transpartationType: {...} } }, { transpartationType: {...} }, or plain
        const raw = e?.payload || e?.transpartationType || e;
        const t   = raw?.transpartationType || raw || {};

        const incomingId = t.id ?? raw?.id;
        if (currentId && incomingId && String(incomingId) !== String(currentId)) {
          // different transpartationType → ignore
          return;
        }

        // Optional live DOM update (before reload)
        if (t.name_en !== undefined) {
          const el = document.getElementById('transpartationType-name-en');
          if (el) el.textContent = String(t.name_en ?? '—');
        }
        if (t.name_ar !== undefined) {
          const el = document.getElementById('transpartationType-name-ar');
          if (el) el.textContent = String(t.name_ar ?? '—');
        }
        if (t.price !== undefined) {
          const el = document.getElementById('transpartationType-price');
          if (el) el.textContent = `${Number(t.price || 0).toFixed(2)} JD`;
        }
        if (t.descripation !== undefined) {
          const el = document.getElementById('transpartationType-description');
          if (el) el.textContent = String(t.descripation ?? '—');
        }
        if (t.is_active !== undefined) {
          const badge = document.getElementById('transpartationType-status-badge');
          const text  = document.getElementById('transpartationType-status-text');
          const on    = !!Number(t.is_active);

          if (badge) {
            badge.classList.remove('bg-success', 'bg-danger');
            badge.classList.add(on ? 'bg-success' : 'bg-danger');
          }
          if (text) {
            text.textContent = on
              ? '{{ __("adminlte::adminlte.active") }}'
              : '{{ __("adminlte::adminlte.inactive") }}';
          }
        }

        if (window.toastr) {
          toastr.info(@json(__('adminlte::adminlte.saved_successfully')));
        }

        // Reset Blade/page fully from server
        window.location.reload();
      };

      // register for global bootstrapper
      window.__pageBroadcasts.push({
        channel: channelName,
        event:   event,
        handler: handler,
      });

      // subscribe immediately if AppBroadcast is ready
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
        console.info('[transpartationType-show] subscribed via AppBroadcast →', channelName, '/', event);
      } else {
        console.info('[transpartationType-show] registered in __pageBroadcasts; layout will subscribe later →', channelName, '/', event);
      }
    });
  });
})();
</script>
@endpush
