{{-- resources/views/sizes/_form.blade.php --}}
@php
    /**
     * Inputs:
     *  - $action (route)
     *  - $method ('POST'|'PUT'|'PATCH')
     *  - $size   (Model|null)
     * Optional:
     *  - $channel (default 'sizes')
     *  - $events  (default ['size_updated'])
     */
    $sizeObj     = $size ?? null;
    $httpMethod  = strtoupper($method ?? 'POST');

    // Correct way to access Pusher config at runtime
    $pusher_key     = config('broadcasting.connections.pusher.key');
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');
@endphp

<form id="sizes-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $channel ?? 'sizes' }}"
      data-events='@json($events ?? ["size_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array($httpMethod, ['GET','POST']))
        @method($httpMethod)
    @endunless

    @if(!empty($sizeObj?->id))
        <input type="hidden" name="id" value="{{ $sizeObj->id }}">
    @endif

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Image --}}
    <x-upload-image
        :image="old('image', data_get($sizeObj,'image'))"
        label="{{ __('adminlte::adminlte.image') }}"
        name="image"
        id="image"
    />

    {{-- Size Name EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', data_get($sizeObj,'name_en',''))"
    />
    @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Size Name AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', data_get($sizeObj,'name_ar',''))"
    />
    @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Price --}}
    <x-form.textarea
        id="price"
        name="price"
        label="{{ __('adminlte::adminlte.price') }}"
        :value="old('price', data_get($sizeObj,'price',''))"
        dir="rtl"
    />
    @error('price') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Description (field key is "descripation") --}}
    <x-form.textarea
        id="descripation"
        name="descripation"
        label="{{ __('adminlte::adminlte.descripation') }}"
        :value="old('descripation', data_get($sizeObj,'descripation',''))"
    />
    @error('descripation') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Is Active --}}
    <div class="form-group" style="margin: 20px 0;">
        <input type="hidden" name="is_active" value="0">
        @php $isActive = old('is_active', (int) data_get($sizeObj,'is_active', 1)); @endphp
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

{{-- Use SECTION to avoid push/stack errors; ensure layout has @yield('js') --}}
@section('js')
<script>
(function () {
    const form = document.getElementById('sizes-form');
    if (!form) return;

    // ---- Read config from data-* → meta fallbacks ----
    const ds = form.dataset;
    const channelName = ds.channel || 'sizes';
    let events;
    try { events = JSON.parse(ds.events || '["size_updated"]'); } catch { events = ['size_updated']; }
    if (!Array.isArray(events) || events.length === 0) events = ['size_updated'];

    const dataKey     = ds.pusherKey || '';
    const dataCluster = ds.pusherCluster || '';
    const metaKey     = document.querySelector('meta[name="pusher-key"]')?.content || '';
    const metaCluster = document.querySelector('meta[name="pusher-cluster"]')?.content || '';
    const key         = dataKey || metaKey;
    const cluster     = dataCluster || metaCluster;

    if (!key || !cluster) {
        console.warn('[sizes-form] Missing Pusher key/cluster. Provide data-pusher-key/cluster or <meta> tags.');
        return;
    }

    // Wait for Pusher to load
    const ensurePusher = () => new Promise((resolve, reject) => {
        if (window.Pusher) return resolve();
        const t = setInterval(() => { if (window.Pusher) { clearInterval(t); resolve(); } }, 50);
        setTimeout(() => { clearInterval(t); if (!window.Pusher) reject(new Error('Pusher JS not loaded')); }, 5000);
    });

    // Update fields from payload
    function applyPayloadToForm(payload) {
        if (!payload || typeof payload !== 'object') return;

        Object.entries(payload).forEach(([name, value]) => {
            // file inputs cannot be set programmatically
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

        // quick visual feedback
        form.classList.add('border','border-success');
        setTimeout(() => form.classList.remove('border','border-success'), 800);
    }

    // Subscribe and bind
    ensurePusher()
      .then(() => {
          // eslint-disable-next-line no-undef
          const pusher = new Pusher(key, { cluster, forceTLS: true });
          const ch = pusher.subscribe(channelName);

          // Bind common variants: dotted, plain, and lowercase
          events.forEach(ev => {
              ch.bind(ev,               e => applyPayloadToForm(e?.payload || e));
              ch.bind('.' + ev,         e => applyPayloadToForm(e?.payload || e));
              ch.bind(ev.toLowerCase(), e => applyPayloadToForm(e?.payload || e));
          });

          console.info('[sizes-form] Pusher listening on', channelName, 'events:', events);
      })
      .catch((e) => console.error('[sizes-form] Failed to init Pusher:', e));
})();
</script>
@endsection
