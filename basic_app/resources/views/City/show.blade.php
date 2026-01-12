@extends('adminlte::page')

@section('title', __('adminlte::adminlte.city_details'))

@section('content')
<div class="container py-4">

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="col-lg-8 col-md-7">
            <div class="row gy-3">

                {{-- Name EN --}}
                <div class="col-12">
                    <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                    <div id="city-name-en" class="fs-5 fw-bold text-dark">
                        {{ $city->name_en }}
                    </div>
                </div>

                {{-- Name AR --}}
                <div class="col-12">
                    <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                    <div id="city-name-ar" class="fs-5 fw-bold text-dark">
                        {{ $city->name_ar }}
                    </div>
                </div>
  <div class="col-12">
                    <small class="text-muted">{{ __('adminlte::adminlte.country') }}</small>
                    <div id="city-name-ar" class="fs-5 fw-bold text-dark">
                        {{ $city->country->name_en }}
                    </div>
                </div>
                {{-- Status --}}
                <div class="col-12">
                    <span id="city-status"
                          class="badge {{ $city->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                        @if($city->is_active)
                            <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                        @else
                            <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                        @endif
                    </span>
                </div>

                {{-- Actions --}}
                <div class="col-12 pt-3">
                    <a href="{{ route('cities.edit', $city->id) }}"
                       class="btn btn-primary px-4 py-2">
                        <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                    </a>
                    <a href="{{ route('cities.index') }}"
                       class="btn btn-outline-secondary ms-2 px-4 py-2">
                        <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                    </a>
                </div>

            </div>
        </div>
    </x-adminlte-card>
</div>

{{-- 🔔 Listener anchor (used by JS to know which record & channel) --}}
<div id="city-listener"
     data-channel="city"
     data-events='["city_updated"]'
     data-delivery-id="{{ $city->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
    'use strict';

    function norm(v) {
        if (v === undefined || v === null) return '';
        return String(v);
    }

    // Update the visible text / status from broadcast payload
    function updateDomFromPayload(payload) {
        if (!payload) return;

        const d = payload.delivery ?? payload ?? {};

        const anchor = document.getElementById('city-listener');
        const currentId = anchor ? anchor.dataset.deliveryId : null;
        if (currentId && d.id && String(d.id) !== String(currentId)) {
            // event for another delivery record – ignore
            return;
        }

        // Name EN
        const nameEnEl = document.getElementById('city-name-en');
        if (nameEnEl) {
            nameEnEl.textContent = norm(d.name_en);
        }

        // Name AR
        const nameArEl = document.getElementById('city-name-ar');
        if (nameArEl) {
            nameArEl.textContent = norm(d.name_ar);
        }

        // Status badge
        const statusEl = document.getElementById('city-status');
        if (statusEl && d.is_active !== undefined && d.is_active !== null) {
            const isOn = Number(d.is_active) === 1;
            statusEl.classList.remove('bg-success', 'bg-danger');
            statusEl.classList.add(isOn ? 'bg-success' : 'bg-danger');
            statusEl.innerHTML = isOn
                ? '<i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}'
                : '<i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}';
        }

        if (window.toastr) {
            toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }

        console.log('[city show] updated from broadcast payload', d);
    }

    // Optional global hook
    window.updateCompanyDeliveryShow = updateDomFromPayload;

    document.addEventListener('DOMContentLoaded', function () {
        const anchor = document.getElementById('city-listener');
        if (!anchor) {
            console.warn('[city show] listener anchor not found');
            return;
        }

        // Register with global broadcasting (same style as additional/category/branch)
        window.__pageBroadcasts = window.__pageBroadcasts || [];

        const handler = function (e) {
            updateDomFromPayload(e && (e.delivery ?? e));
        };

        window.__pageBroadcasts.push({
            channel: 'city',           // broadcastOn()
            event:   'city_updated',   // broadcastAs()
            handler: handler
        });

        if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
            window.AppBroadcast.subscribe('city', 'city_updated', handler);
            console.info('[city show] subscribed via AppBroadcast → city / city_updated');
        } else {
            console.info('[city show] registered in __pageBroadcasts; layout will subscribe later.');
        }
    });
})();
</script>
@endpush
