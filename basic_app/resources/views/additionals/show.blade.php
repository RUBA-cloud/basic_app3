@extends('adminlte::page')

@section('title', __('adminlte::adminlte.additional'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 fw-bold text-dark">
            @if (app()->getLocale() === 'ar')
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.additional') }}
            @else
                {{ __('adminlte::adminlte.additional') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>
    </div>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">

            {{-- Image --}}
            <div class="col-lg-4 col-md-5">
                <div class="border rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center p-2 h-100">
                    <img
                        id="additional-image"
                        src="{{ $additional->image ? asset($additional->image) : 'https://placehold.co/500x300?text=Additional+Image' }}"
                        alt="Additional Image"
                        class="img-fluid rounded-3"
                        style="max-height: 280px; object-fit: cover;"
                    >
                </div>
            </div>

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- name_en --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div id="additional-name-en" class="fs-5 fw-bold text-dark">
                            {{ $additional->name_en }}
                        </div>
                    </div>

                    {{-- name_ar --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div id="additional-name-ar" class="fs-5 fw-bold text-dark">
                            {{ $additional->name_ar }}
                        </div>
                    </div>

                    {{-- status --}}
                    <div class="col-12">
                        <span id="additional-status" class="badge {{ $additional->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                            @if($additional->is_active)
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            @else
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            @endif
                        </span>
                    </div>

                    {{-- price --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.price') }}</small>
                        <div id="additional-price" class="fs-5 fw-bold text-dark">
                            {{ $additional->price }} JD
                        </div>
                    </div>

                    {{-- description --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.descripation') }}</small>
                        <div id="additional-description" class="text-dark">
                            {{ $additional->description }}
                        </div>
                    </div>

                    {{-- actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('additional.edit', $additional->id) }}"
                           class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('additional.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>

{{-- 🔔 Listener anchor (this replaces the form-based detection) --}}
<div id="additional-listener"
     data-channel="additional"
     data-events='["additional_updated","AdditionalUpdated"]'
     data-pusher-key="{{ env('PUSHER_APP_KEY') }}"
     data-pusher-cluster="{{ env('PUSHER_APP_CLUSTER', 'mt1') }}"
     data-additional-id="{{ $additional->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
    'use strict';

    // Helpers to update the DOM
    function updateDomFromPayload(a) {
        if (!a) return;

        // Only update if the payload belongs to this additional
        var currentIdEl = document.getElementById('additional-listener');
        var currentId = currentIdEl ? currentIdEl.dataset.additionalId : null;
        if (currentId && a.id && String(a.id) !== String(currentId)) {
            // it's an update for another additional; ignore
            return;
        }

        // name_en
        var nameEnEl = document.getElementById('additional-name-en');
        if (nameEnEl && a.name_en !== undefined) {
            nameEnEl.textContent = a.name_en;
        }

        // name_ar
        var nameArEl = document.getElementById('additional-name-ar');
        if (nameArEl && a.name_ar !== undefined) {
            nameArEl.textContent = a.name_ar;
        }

        // price
        var priceEl = document.getElementById('additional-price');
        if (priceEl && a.price !== undefined) {
            priceEl.textContent = a.price + ' JD';
        }

        // description
        var descEl = document.getElementById('additional-description');
        if (descEl && a.description !== undefined) {
            descEl.textContent = a.description;
        }

        // status
        var statusEl = document.getElementById('additional-status');
        if (statusEl && a.is_active !== undefined) {
            var isOn = Number(a.is_active) === 1;
            statusEl.classList.remove('bg-success','bg-danger');
            statusEl.classList.add(isOn ? 'bg-success' : 'bg-danger');
            statusEl.innerHTML = isOn
                ? '<i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}'
                : '<i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}';
        }

        // image
        var imgEl = document.getElementById('additional-image');
        if (imgEl && (a.image_url || a.image)) {
            imgEl.src = a.image_url || a.image;
        }

        // toast (optional)
        if (window.toastr) {
            toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }
    }

    function loadPusher() {
        return new Promise(function (resolve, reject) {
            if (window.Pusher) {
                return resolve();
            }
            var s = document.createElement('script');
            s.src = 'https://js.pusher.com/8.4/pusher.min.js';
            s.async = true;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var anchor = document.getElementById('additional-listener');
        if (!anchor) {
            console.warn('[additional] listener anchor not found');
            return;
        }

        var channelName = anchor.dataset.channel || 'additional';
        var events = [];
        try {
            events = JSON.parse(anchor.dataset.events || '["additional_updated"]');
        } catch (_) {
            events = ['additional_updated'];
        }
        var key = anchor.dataset.pusherKey;
        var cluster = anchor.dataset.pusherCluster || 'mt1';

        if (!key) {
            console.warn('[additional] Missing Pusher key');
            return;
        }

        loadPusher().then(function () {
            // eslint-disable-next-line no-undef
            var pusher = new Pusher(key, {
                cluster: cluster,
                forceTLS: true
            });

            var ch = pusher.subscribe(channelName);

            events.forEach(function (ev) {
                // normal
                ch.bind(ev, function (e) {
                    var payload = e.additional || e;
                    updateDomFromPayload(payload);
                });
                // lowercase
                ch.bind(ev.toLowerCase(), function (e) {
                    var payload = e.additional || e;
                    updateDomFromPayload(payload);
                });
                // dotted
                ch.bind('.' + ev, function (e) {
                    var payload = e.additional || e;
                    updateDomFromPayload(payload);
                });
            });

            console.log('[additional] listening on "' + channelName + '" for', events);
        }).catch(function (err) {
            console.error('[additional] failed to load Pusher', err);
        });
    });
})();
</script>
@endpush
