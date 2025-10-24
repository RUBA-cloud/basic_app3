{{-- resources/views/type/_form.blade.php --}}
@php
    /**
     * Inputs:
     *  - $action (route)
     *  - $method ('POST'|'PUT'|'PATCH')
     *  - $type   (Model|null)
     * Optional:
     *  - $channel (default 'types')
     *  - $events  (default ['type_updated'])
     */
    $typeObj     = $type ?? null;
    $httpMethod  = strtoupper($method ?? 'POST');

    // Read from config (not env) at runtime
    $pusher_key     = config('broadcasting.connections.pusher.key');
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');
@endphp

<form id="type-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $channel ?? 'types' }}"
      data-events='@json($events ?? ["type_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless(in_array($httpMethod, ['GET','POST']))
        @method($httpMethod)
    @endunless

    @if(!empty($typeObj?->id))
        <input type="hidden" name="id" value="{{ $typeObj->id }}">
    @endif

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Type Name EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', data_get($typeObj,'name_en',''))"
    />
    @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Type Name AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', data_get($typeObj,'name_ar',''))"
    />
    @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Is Active --}}
    <div class="form-group" style="margin: 20px 0;">
        <input type="hidden" name="is_active" value="0">
        @php $isActive = old('is_active', (int) data_get($typeObj,'is_active', 1)); @endphp
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
    {{-- Pusher only (no Echo) --}}
    <script>
      (function loadPusherOnce(){
        if (window._pusherLoaderAdded) return;
        window._pusherLoaderAdded = true;
        const s = document.createElement('script');
        s.src = 'https://js.pusher.com/8.4/pusher.min.js';
        s.async = true;
        document.head.appendChild(s);
      })();
    </script>
@endonce

{{-- Use SECTION to avoid push/stack issues; ensure layout has @yield('js') --}}
@section('js')
<script>
(function () {
    const form = document.getElementById('type-form');
    if (!form) return;

    // Read from data-* then fall back to meta tags
    const ds = form.dataset;
    const channelName = ds.channel || 'types';

    let events;
    try { events = JSON.parse(ds.events || '["type_updated"]'); }
    catch { events = ['type_updated']; }
    if (!Array.isArray(events) || events.length === 0) events = ['type_updated'];

    const dataKey     = ds.pusherKey || '';
    const dataCluster = ds.pusherCluster || '';
    const metaKey     = document.querySelector('meta[name="pusher-key"]')?.content || '';
    const metaCluster = document.querySelector('meta[name="pusher-cluster"]')?.content || '';
    const key         = dataKey || metaKey;
    const cluster     = dataCluster || metaCluster;

    if (!key || !cluster) {
        console.warn('[type-form] Missing Pusher key/cluster. Provide data-pusher-key/cluster or <meta> tags.');
        return;
    }

    // Wait for Pusher to load
    const ensurePusher = () => new Promise((resolve, reject) => {
        if (window.Pusher) return resolve();
        const t = setInterval(() => { if (window.Pusher) { clearInterval(t); resolve(); } }, 50);
        setTimeout(() => { clearInterval(t); if (!window.Pusher) reject(new Error('Pusher JS not loaded')); }, 5000);
    });

    // Fill form from payload
    function applyPayloadToForm(payload) {
        if (!payload || typeof payload !== 'object') return;
        Object.entries(payload).forEach(([name, value]) => {
            const nodes = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
            if (!nodes.length) return;

            nodes.forEach((el) => {
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

        form.classList.add('border','border-success');
        setTimeout(() => form.classList.remove('border','border-success'), 800);
    }

    // Subscribe + bind (Pusher only)
    ensurePusher()
      .then(() => {
          // eslint-disable-next-line no-undef
          const pusher = new Pusher(key, { cluster, forceTLS: true });
          const ch = pusher.subscribe(channelName);

          // Support dotted, plain, lowercase names
          events.forEach(ev => {
              ch.bind(ev,               e => applyPayloadToForm(e?.payload || e));
              ch.bind('.' + ev,         e => applyPayloadToForm(e?.payload || e));
              ch.bind(ev.toLowerCase(), e => applyPayloadToForm(e?.payload || e));
          });

          console.info('[type-form] Pusher listening on', channelName, 'events:', events);
      })
      .catch((e) => console.error('[type-form] Failed to init Pusher:', e));
})();
</script>
@endsection
