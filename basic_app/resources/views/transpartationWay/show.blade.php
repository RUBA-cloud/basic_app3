<div>
    <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->
</div>@extends('adminlte::page')

@section('title', __('adminlte::adminlte.transportation_way') . ' ' . __('adminlte::adminlte.details'))

@section('content')
@php
  $isAr = app()->getLocale() === 'ar';

  $obj = $transportationWay; // show page always has model

  $countryName = $obj->country
      ? ($isAr ? ($obj->country->name_ar ?? $obj->country->name_en) : ($obj->country->name_en ?? $obj->country->name_ar))
      : '—';

  $cityName = $obj->city
      ? ($isAr ? ($obj->city->name_ar ?? $obj->city->name_en) : ($obj->city->name_en ?? $obj->city->name_ar))
      : '—';
@endphp

<style>
  .mie-1 { margin-inline-end: .25rem; }
  .mie-2 { margin-inline-end: .5rem; }
  .mis-2 { margin-inline-start: .5rem; }

  .show-card .kv { padding: .75rem 0; border-bottom: 1px solid rgba(0,0,0,.06); }
  .show-card .kv:last-child { border-bottom: 0; }
  .show-card .k { font-size: .85rem; color: #6c757d; }
  .show-card .v { font-size: 1.05rem; font-weight: 700; color: #212529; }
</style>

<div class="container py-4">

  {{-- Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
    <div class="d-flex align-items-center gap-3">
      <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
           style="width:52px;height:52px;border:1px solid rgba(0,0,0,.08);">
        <i class="fas fa-route text-primary"></i>
      </div>
      <div>
        <div class="text-muted small mb-1">
          {{ __('adminlte::adminlte.transportation_way') }}
        </div>
        <div class="h5 mb-0 fw-bold">
          {{ $isAr ? ($obj->name_ar ?? $obj->name_en ?? '—') : ($obj->name_en ?? $obj->name_ar ?? '—') }}
        </div>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <span id="tw-status-badge"
            class="badge {{ $obj->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
        <i class="fas {{ $obj->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mie-1"></i>
        <span id="tw-status-text">
          {{ $obj->is_active ? __('adminlte::adminlte.active') : __('adminlte::adminlte.inactive') }}
        </span>
      </span>
    </div>
  </div>

  {{-- Card --}}
  <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm show-card">
    <div class="row g-4">

      {{-- Details --}}
      <div class="col-lg-8 col-md-7">
        <div class="kv">
          <div class="k">{{ __('adminlte::adminlte.country') }}</div>
          <div class="v" id="tw-country">{{ $countryName }}</div>
        </div>

        <div class="kv">
          <div class="k">{{ __('adminlte::adminlte.city') }}</div>
          <div class="v" id="tw-city">{{ $cityName }}</div>
        </div>

        <div class="kv">
          <div class="k">{{ __('adminlte::adminlte.name_en') }}</div>
          <div class="v" id="tw-name-en">{{ $obj->name_en ?? '—' }}</div>
        </div>

        <div class="kv">
          <div class="k">{{ __('adminlte::adminlte.name_ar') }}</div>
          <div class="v" id="tw-name-ar">{{ $obj->name_ar ?? '—' }}</div>
        </div>

        <div class="kv">
          <div class="k">{{ __('adminlte::adminlte.days_count') }}</div>
          <div class="v" id="tw-days-count">{{ $obj->days_count ?? '—' }}</div>
        </div>

        {{-- Actions --}}
        <div class="pt-3 d-flex flex-wrap gap-2">
          <a href="{{ route('transpartation_ways.edit', $obj->id) }}" class="btn btn-primary px-4 py-2">
            <i class="fas fa-edit mie-2"></i>{{ __('adminlte::adminlte.edit') }}
          </a>

          <form action="{{ route('transpartation_ways.destroy', $obj->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="btn btn-outline-danger px-4 py-2"
                    onclick="return confirm(@json(__('adminlte::adminlte.are_you_sure')));">
              <i class="fas fa-trash mie-2"></i>{{ __('adminlte::adminlte.delete') }}
            </button>
          </form>

          <a href="{{ route('transpartation_ways.index') }}" class="btn btn-outline-secondary px-4 py-2">
            <i class="fas fa-arrow-left mie-2"></i>{{ __('adminlte::adminlte.go_back') }}
          </a>
        </div>
      </div>

      {{-- Side info --}}
      <div class="col-lg-4 col-md-5">
        <div class="p-3 bg-light rounded" style="border:1px solid rgba(0,0,0,.06);">
          <div class="text-muted small mb-2">{{ __('adminlte::adminlte.information') ?? 'Information' }}</div>

          <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:rgba(0,0,0,.06)!important;">
            <span class="text-muted">{{ __('adminlte::adminlte.created_at') }}</span>
            <span class="fw-semibold">{{ optional($obj->created_at)->format('Y-m-d H:i') ?? '—' }}</span>
          </div>

          <div class="d-flex justify-content-between py-2">
            <span class="text-muted">{{ __('adminlte::adminlte.updated_at') }}</span>
            <span class="fw-semibold">{{ optional($obj->updated_at)->format('Y-m-d H:i') ?? '—' }}</span>
          </div>
        </div>
      </div>

    </div>
  </x-adminlte-card>
</div>

{{-- Listener anchor for window broadcasting --}}
<div id="transportation-way-show-listener"
     data-channel="transportation-way"
     data-events='["transportation_way_updated"]'
     data-transportation-way-id="{{ $obj->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
  'use strict';

  const STATUS_ACTIVE_TEXT = @json(__('adminlte::adminlte.active'));
  const STATUS_INACTIVE_TEXT = @json(__('adminlte::adminlte.inactive'));
  const TOAST_SAVED = @json(__('adminlte::adminlte.saved_successfully'));

  document.addEventListener('DOMContentLoaded', function () {
    const anchor = document.getElementById('transportation-way-show-listener');
    if (!anchor) return;

    const channelName = anchor.dataset.channel || 'transpartation_way_channel';

    let events = [];
    try { events = JSON.parse(anchor.dataset.events || '[]'); } catch (e) { events = []; }
    if (!Array.isArray(events) || !events.length) events = ['transportation_way_updated'];

    // data-transportation-way-id => dataset.transportationWayId
    const currentId = String(anchor.dataset.transportationWayId || '');

    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach((evtName) => {
      const event = String(evtName || '').trim();
      if (!event) return;

      const handler = function (e) {
        const raw = (e && (e.payload ?? e)) || {};
        const t   = raw.transportationWay ?? raw.transportation_way ?? raw;

        const incomingId = (t && (t.id ?? raw.id)) ? String(t.id ?? raw.id) : '';
        if (currentId && incomingId && incomingId !== currentId) return;

        // Update DOM (optional)
        if (t.name_en !== undefined) {
          const el = document.getElementById('tw-name-en');
          if (el) el.textContent = String(t.name_en ?? '—');
        }
        if (t.name_ar !== undefined) {
          const el = document.getElementById('tw-name-ar');
          if (el) el.textContent = String(t.name_ar ?? '—');
        }
        if (t.days_count !== undefined) {
          const el = document.getElementById('tw-days-count');
          if (el) el.textContent = String(t.days_count ?? '—');
        }
        if (t.country_name !== undefined) {
          const el = document.getElementById('tw-country');
          if (el) el.textContent = String(t.country_name ?? '—');
        }
        if (t.city_name !== undefined) {
          const el = document.getElementById('tw-city');
          if (el) el.textContent = String(t.city_name ?? '—');
        }
        if (t.is_active !== undefined) {
          const badge = document.getElementById('tw-status-badge');
          const text  = document.getElementById('tw-status-text');
          const on = (t.is_active === true || t.is_active === 1 || t.is_active === '1');

          if (badge) {
            badge.classList.remove('bg-success', 'bg-danger');
            badge.classList.add(on ? 'bg-success' : 'bg-danger');

            const icon = badge.querySelector('i');
            if (icon) {
              icon.classList.remove('fa-check-circle', 'fa-times-circle');
              icon.classList.add(on ? 'fa-check-circle' : 'fa-times-circle');
            }
          }
          if (text) text.textContent = on ? STATUS_ACTIVE_TEXT : STATUS_INACTIVE_TEXT;
        }

        if (window.toastr) toastr.info(TOAST_SAVED);

        // Reload to keep Blade formatting consistent
        window.location.reload();
      };

      window.__pageBroadcasts.push({ channel: channelName, event, handler });

      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, event, handler);
      }
    });
  });
})();
</script>
@endpush
