{{-- resources/views/order_status/_form.blade.php --}}
@extends('adminlte::page')

@section('title')
    {{ strtoupper($method ?? 'POST') === 'POST'
        ? __('adminlte::adminlte.create').' '.__('adminlte::adminlte.order_status')
        : __('adminlte::adminlte.edit').' '.__('adminlte::adminlte.order_status') }}
@endsection
@php
$pusher_key     = config('broadcasting.connections.pusher.key');
$pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');


@endphp
@section('content')
<div style="min-height: 100vh; display:flex;">
    <div class="card" style="padding:24px; width:100%;">
        <h2 style="font-size:2rem; font-weight:700; color:#22223B; margin-bottom:24px;">
            {{ strtoupper($method ?? 'POST') === 'POST'
                ? __('adminlte::adminlte.create').' '.__('adminlte::adminlte.order_status')
                : __('adminlte::adminlte.edit').' '.__('adminlte::adminlte.order_status') }}
        </h2>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            // Expected:
            // $action (string), $method ('POST'|'PUT'|'PATCH'), optional $status (model|null)
            // Optional Pusher config (same as your permission/branch forms):
            // $pusher_key, $pusher_cluster, $channel (default 'order_status'), $events (default ['order_status_updated'])
            $statusObj = $status ?? null;
            $httpMethod = strtoupper($method ?? 'POST');
        @endphp

        <form method="POST"
              action="{{ $action }}"
              id="order-status-form"
              enctype="multipart/form-data"
              data-channel="{{ $channel ?? 'order_status' }}"
              data-events='@json($events ?? ["order_status_updated"])'
              data-pusher-key="{{ $pusher_key ?? '' }}"
              data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
            @csrf
            @unless (in_array($httpMethod, ['GET','POST']))
                @method($httpMethod)
            @endunless

            {{-- Hidden ID if editing (optional) --}}
            @if(!empty($statusObj?->id))
                <input type="hidden" name="id" value="{{ $statusObj->id }}">
            @endif

            {{-- Name EN --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="{{ __('adminlte::adminlte.name_en') }}"
                :value="old('name_en', data_get($statusObj, 'name_en', ''))"
            />
            @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

            {{-- Name AR --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="{{ __('adminlte::adminlte.name_ar') }}"
                dir="rtl"
                :value="old('name_ar', data_get($statusObj, 'name_ar', ''))"
            />
            @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

            {{-- Is Active --}}
            <div class="form-group" style="margin:20px 0;">
                <input type="hidden" name="is_active" value="0">
                @php $isActive = old('is_active', (int) data_get($statusObj, 'is_active', 1)); @endphp
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ (int)$isActive ? 'checked' : '' }}>
                    {{ __('adminlte::adminlte.is_active') }}
                </label>
            </div>
            @error('is_active') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

            <x-adminlte-button
                label="{{ $httpMethod === 'POST'
                    ? __('adminlte::adminlte.save_information')
                    : __('adminlte::adminlte.update_information') }}"
                type="submit"
                theme="success"
                class="w-100"
                icon="fas fa-save"
            />
        </form>
    </div>
</div>
@endsection

@once
    {{-- Echo + Pusher (CDN, same pattern as permission form) --}}
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
@endonce

@push('js')
<script>
(function () {
    const form = document.getElementById('order-status-form');
    if (!form) return;

    const ds = form.dataset;
    const pusherKey     = ds.pusherKey || (document.querySelector('meta[name="pusher-key"]')?.content || '');
    const pusherCluster = ds.pusherCluster || (document.querySelector('meta[name="pusher-cluster"]')?.content || '');
    const channelName   = ds.channel || 'order_status';

    let events = [];
    try { events = JSON.parse(ds.events || '[]'); } catch (_) { events = []; }
    if (!Array.isArray(events) || events.length === 0) events = ['order_status_updated'];

    if (!pusherKey || !pusherCluster) {
        console.warn('[order-status-form] Missing Pusher key/cluster. Provide data-pusher-key/cluster on the form or <meta> fallbacks.');
        return;
    }

    if (!window.Echo) {
        try {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster,
                forceTLS: true,             // set to false if you use plain ws in dev
                enabledTransports: ['ws','wss'],
            });
        } catch (e) {
            console.error('[order-status-form] Echo init failed:', e);
            return;
        }
    }

    const channel = window.Echo.channel(channelName);
    if (!channel) {
        console.error('[order-status-form] Cannot subscribe to channel:', channelName);
        return;
    }

    function applyPayloadToForm(payload) {
        if (!payload || typeof payload !== 'object') return;

        Object.entries(payload).forEach(([name, value]) => {
            const inputs = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
            if (!inputs.length) return;

            inputs.forEach((el) => {
                const type = (el.getAttribute('type') || el.tagName).toLowerCase();
                if (type === 'radio') {
                    el.checked = (String(el.value) === String(value));
                } else if (type === 'checkbox') {
                    el.checked = Boolean(value) && String(value) !== '0';
                } else {
                    el.value = (value ?? '');
                }
            });
        });
    }

    events.forEach((evt) => {
        channel.listen('.' + evt, (e) => {
            // Expect broadcast payload keys that match input names:
            // { id: 5, name_en: 'Shipped', name_ar: 'تم الشحن', is_active: 1 }
            const payload = e?.payload || e;
            applyPayloadToForm(payload);

            // Small visual cue
            form.classList.add('border','border-success');
            setTimeout(() => form.classList.remove('border','border-success'), 800);
        });
    });

    console.info('[order-status-form] Listening on', channelName, 'events:', events);
})();
</script>
@endpush
