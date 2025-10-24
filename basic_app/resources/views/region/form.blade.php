{{-- resources/views/regions/_form.blade.php --}}
@php
    /**
     * Pass:
     *  - $action (string route)
     *  - $method ('POST'|'PUT'|'PATCH')
     *  - $region (Model|null)
     * Optional Pusher config (same as order_status):
     *  - $pusher_key, $pusher_cluster
     *  - $channel (default 'regions')
     *  - $events  (default ['region_updated'])
     */
    $regionObj  = $region ?? null;
    $httpMethod = strtoupper($method ?? 'POST');
    $pusher_key     = config('broadcasting.connections.pusher.key');
$pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');


@endphp

<form id="regions-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $channel ?? 'regions' }}"
      data-events='@json($events ?? ["region_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array($httpMethod, ['GET','POST']))
        @method($httpMethod)
    @endunless

    @if(!empty($regionObj?->id))
        <input type="hidden" name="id" value="{{ $regionObj->id }}">
    @endif

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Country EN --}}
    <x-form.textarea
        id="country_en"
        name="country_en"
        label="{{ __('adminlte::adminlte.country') }} EN"
        :value="old('country_en', data_get($regionObj,'country_en',''))"
    />
    @error('country_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Country AR --}}
    <x-form.textarea
        id="country_ar"
        name="country_ar"
        label="{{ __('adminlte::adminlte.country') }} AR"
        dir="rtl"
        :value="old('country_ar', data_get($regionObj,'country_ar',''))"
    />
    @error('country_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- City EN --}}
    <x-form.textarea
        id="city_en"
        name="city_en"
        label="{{ __('adminlte::adminlte.city') }} EN"
        :value="old('city_en', data_get($regionObj,'city_en',''))"
    />
    @error('city_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- City AR --}}
    <x-form.textarea
        id="city_ar"
        name="city_ar"
        label="{{ __('adminlte::adminlte.city') }} AR"
        dir="rtl"
        :value="old('city_ar', data_get($regionObj,'city_ar',''))"
    />
    @error('city_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Expected Day Count (number) --}}
    <div class="form-group">
        <label for="excepted_day_count">{{ __('adminlte::adminlte.excepted_delivery_days') }}</label>
        <input
            id="excepted_day_count"
            type="number"
            min="0"
            class="form-control @error('excepted_day_count') is-invalid @enderror"
            name="excepted_day_count"
            value="{{ old('excepted_day_count', data_get($regionObj,'excepted_day_count','')) }}"
        >
        @error('excepted_day_count')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    {{-- Is Active --}}
    <div class="form-group" style="margin:20px 0;">
        <input type="hidden" name="is_active" value="0">
        @php $isActive = old('is_active', (int) data_get($regionObj,'is_active', 1)); @endphp
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

@once
    {{-- Echo + Pusher (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
@endonce

@push('js')
<script>
(function () {
    const form = document.getElementById('regions-form');
    if (!form) return;

    // ---- Pusher/Echo bootstrap (same as order_status) ----
    const ds = form.dataset;
    const pusherKey     = ds.pusherKey || (document.querySelector('meta[name="pusher-key"]')?.content || '');
    const pusherCluster = ds.pusherCluster || (document.querySelector('meta[name="pusher-cluster"]')?.content || '');
    const channelName   = ds.channel || 'regions';

    let events = [];
    try { events = JSON.parse(ds.events || '[]'); } catch (_) { events = []; }
    if (!Array.isArray(events) || events.length === 0) events = ['region_updated'];

    if (!pusherKey || !pusherCluster) {
        console.warn('[regions-form] Missing Pusher key/cluster. Provide data-pusher-key/cluster on the form or <meta> tags.');
        return;
    }

    if (!window.Echo) {
        try {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster,
                forceTLS: true,           // false if you use plain ws in local dev
                enabledTransports: ['ws','wss'],
            });
        } catch (e) {
            console.error('[regions-form] Echo init failed:', e);
            return;
        }
    }

    const channel = window.Echo.channel(channelName);
    if (!channel) {
        console.error('[regions-form] Cannot subscribe to channel:', channelName);
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
            // Expect payload keys matching input names:
            // { id, country_en, country_ar, city_en, city_ar, excepted_day_count, is_active }
            const payload = e?.payload || e;
            applyPayloadToForm(payload);

            form.classList.add('border','border-success');
            setTimeout(() => form.classList.remove('border','border-success'), 800);
        });
    });

    console.info('[regions-form] Listening on', channelName, 'events:', events);
})();
</script>
@endpush
