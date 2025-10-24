{{-- resources/views/categories/_form.blade.php --}}
{{-- expects: $action (string), $method ('POST'|'PUT'|'PATCH'), $branches (Collection), optional $category (model|null) --}}

@php
    // Prefer config() here (env() is for config files)
    $broadcast = $broadcast ?? [
        'channel'        => 'categories',
        'events'         => ['category_updated'],
        'pusher_key'     => config('broadcasting.connections.pusher.key'),
        'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster', 'mt1'),
    ];
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="category-form"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast['events'])'
      data-pusher-key="{{ $broadcast['pusher_key'] }}"
      data-pusher-cluster="{{ $broadcast['pusher_cluster'] }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endunless

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Category Image --}}
    <x-upload-image
        :image="$category->image ?? null"
        label="{{ __('adminlte::adminlte.image') }}"
        name="image"
        id="image"
    />

    {{-- Category Name English --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', $category->name_en ?? '')"
        rows="1"
    />

    {{-- Category Name Arabic --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', $category->name_ar ?? '')"
        rows="1"
    />

    {{-- Category Branch Selection (Multiple) --}}
    @php
        $oldSelected = collect(
            old('branch_ids', isset($category) ? ($category->branches->pluck('id')->all() ?? []) : [])
        )->map(fn($v)=>(int)$v);
    @endphp
    <div class="form-group" style="margin-bottom: 20px;">
        <label for="branch_ids" style="display:block;margin-bottom:8px;font-weight:600;">
            {{ __('adminlte::adminlte.branches') }}
        </label>
        <select name="branch_ids[]" id="branch_ids" class="form-control select" multiple required style="width:100%;">
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}"
                    {{ $oldSelected->contains((int)$branch->id) ? 'selected' : '' }}>
                    {{ app()->getLocale() === 'ar' ? $branch->name_ar : $branch->name_en }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Is Active --}}
    <div class="form-group" style="margin: 20px 0;">
        <input type="hidden" name="is_active" value="0">
        <input
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            {{ old('is_active', isset($category) ? (int)$category->is_active : 0) ? 'checked' : '' }}
        />
        <label for="is_active">{{ __('adminlte::adminlte.is_active') }}</label>
    </div>

    {{-- Submit --}}
    <x-adminlte-button
        :label="isset($category) ? __('adminlte::adminlte.update_information') : __('adminlte::adminlte.save_information')"
        type="submit"
        theme="success"
        class="full-width-btn"
        icon="fas fa-save"
    />
</form>

@once
    {{-- Pusher loader (optional; you can also include globally) --}}
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

@section('js')
<script>
(function(){
  'use strict';
  const esc = (s) => (window.CSS && CSS.escape) ? CSS.escape(s) : s;

  // Small helpers
  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input',  { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setCheckbox = (name, isOn) => {
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.checked = !!Number(isOn);
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setMultiSelect = (name, values = []) => {
    const el = document.getElementById('branch_ids') || document.querySelector(`[name="${esc(name)}[]"]`);
    if (!el) return;
    const want = (values || []).map(v => String(v));
    Array.from(el.options).forEach(opt => { opt.selected = want.includes(String(opt.value)); });
    if (window.jQuery && jQuery(el).hasClass('select')) {
      jQuery(el).trigger('change.select2');
    } else {
      el.dispatchEvent(new Event('change', { bubbles: true }));
    }
  };

  const previewImage = (url) => {
    const img = document.querySelector('#image-preview, [data-role="image-preview"]');
    if (img && url) img.src = url;
  };

  // Apply incoming payload
  const applyPayload = (payload) => {
    const c = payload?.category ?? payload ?? {};
    setField('name_en',  c.name_en);
    setField('name_ar',  c.name_ar);
    setCheckbox('is_active', c.is_active);

    const ids = c.branch_ids || (Array.isArray(c.branches) ? c.branches.map(b => b.id) : []);
    setMultiSelect('branch_ids', ids);

    previewImage(c.image_url || c.image);

    if (window.toastr) { try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_){} }
    console.log('[categories] form updated', c);
  };

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('category-form');
    if (!form) return;

    const channel = form.dataset.channel || 'categories';
    let events;
    try { events = JSON.parse(form.dataset.events || '["category_updated"]'); }
    catch { events = ['category_updated']; }
    if (!Array.isArray(events) || events.length === 0) events = ['category_updated'];

    // ORDER OF FALLBACKS: data-* → <meta> → server-rendered config from PHP
    const dataKey     = form.dataset.pusherKey || '';
    const dataCluster = form.dataset.pusherCluster || '';
    const metaKey     = document.querySelector('meta[name="pusher-key"]')?.content || '';
    const metaCluster = document.querySelector('meta[name="pusher-cluster"]')?.content || '';
    const cfgKey      = @json($broadcast['pusher_key'] ?? '');
    const cfgCluster  = @json($broadcast['pusher_cluster'] ?? 'mt1');

    const key     = dataKey     || metaKey     || cfgKey;
    const cluster = dataCluster || metaCluster || cfgCluster;

    if (!key || !cluster) {
      console.warn('[categories] Missing Pusher key/cluster. Provide data-pusher-key/data-pusher-cluster or <meta> tags or config/broadcasting.php');
      return;
    }

    // Wait for Pusher to load if not yet present
    const ensurePusher = () => new Promise((resolve, reject) => {
      if (window.Pusher) return resolve();
      const check = setInterval(() => {
        if (window.Pusher) { clearInterval(check); resolve(); }
      }, 50);
      setTimeout(() => { clearInterval(check); if (!window.Pusher) reject(new Error('Pusher JS not loaded')); }, 5000);
    });

    ensurePusher()
      .then(() => {
        // eslint-disable-next-line no-undef
        const pusher = new Pusher(key, { cluster, forceTLS: true });
        const ch = pusher.subscribe(channel);

        events.forEach(ev => {
          ch.bind(ev,               e => applyPayload(e));
          ch.bind(ev.toLowerCase(), e => applyPayload(e));
          ch.bind('.' + ev,         e => applyPayload(e));
        });

        console.info(`[categories] Pusher listening on "${channel}" for`, events);
      })
      .catch((e) => console.error('[categories] Failed to init Pusher:', e));
  });
})();
</script>
@endsection
