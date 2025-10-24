

@php
  // Consistent defaults
  $broadcast = $broadcast ?? [
      'channel' => 'additional',
      'events'  => ['additional_updated'],
    'pusher_key'     => env('PUSHER_APP_KEY'),
    'pusher_cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),

];
@endphp

<form id="additional-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $broadcast['channel'] ?? 'additional' }}"
      data-events='@json($broadcast['events'] ?? ["AdditionalUpdated"])'
      data-pusher-key="{{ $broadcast['pusher_key'] ?? '' }}"
      data-pusher-cluster="{{ $broadcast['pusher_cluster'] ?? '' }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET','POST']))
        @method($method)
    @endunless

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-upload-image
        :image="$additional->image ?? null"
        label="{{ __('adminlte::adminlte.image') }}"
        name="image"
        id="image"
    />

    <x-form.textarea id="name_en" name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', $additional->name_en ?? '')" rows="1" />

    <x-form.textarea id="name_ar" name="name_ar" dir="rtl"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        :value="old('name_ar', $additional->name_ar ?? '')" rows="1" />

    <x-form.textarea id="price" name="price"
        label="{{ __('adminlte::adminlte.price') }}"
        :value="old('price', $additional->price ?? '')" rows="1" />

    <x-form.textarea id="description" name="description"
        label="{{ __('adminlte::adminlte.descripation') }}"
        :value="old('description', $additional->description ?? '')" />

    <div class="form-group my-3">
        <input type="hidden" name="is_active" value="0">
        <label class="mb-0">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', (int) ($additional->is_active ?? 1)) ? 'checked' : '' }}>
            {{ __('adminlte::adminlte.is_active') }}
        </label>
    </div>

    <x-adminlte-button
        :label="isset($additional) ? __('adminlte::adminlte.update_information') : __('adminlte::adminlte.save_information')"
        type="submit" theme="success" class="w-100" icon="fas fa-save" />
</form>

@push('js')
@once
<script>
(function(){
  'use strict';

  // --- Load Pusher once ---
  function loadPusher(){
    return new Promise((resolve, reject)=>{
      if (window.Pusher) return resolve();
      const s = document.createElement('script');
      s.src = 'https://js.pusher.com/8.4/pusher.min.js';
      s.async = true;
      s.onload = resolve;
      s.onerror = reject;
      document.head.appendChild(s);
    });
  }

  // --- Helpers ---
  const esc = (s) => (window.CSS && CSS.escape) ? CSS.escape(s) : s;

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

  const previewImage = (url) => {
    if (!url) return;
    const img = document.querySelector('#image-preview,[data-role="image-preview"]');
    if (img) img.src = url;
  };

  const applyPayload = (payload) => {
    const a = (payload && (payload.additional ?? payload)) || {};
    setField('name_en', a.name_en);
    setField('name_ar', a.name_ar);
    setField('price', a.price);
    setField('description', a.description);
    setCheckbox('is_active', a.is_active);
    previewImage(a.image_url || a.image);
    if (window.toastr) { try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_){} }
  };

  // Optional: expose a hard reset
  window.resetAdditionalForm = function(){
    setField('name_en', '');
    setField('name_ar', '');
    setField('price', '');
    setField('description', '');
    setCheckbox('is_active', 1);
    if (window.bsCustomFileInput) { try { bsCustomFileInput.init(); } catch(_){} }
  };

  // --- Setup Pusher listener ---
  document.addEventListener('DOMContentLoaded', async () => {
    const form = document.getElementById('additional-form');
    if (!form) return;

    const channel = form.dataset.channel || 'additional';
    const events  = JSON.parse(form.dataset.events || '["AdditionalUpdated"]');

    // prefer data-*; fallback to <meta>
    let key     = form.dataset.pusherKey;
    let cluster = form.dataset.pusherCluster;

    if (!key) {
      const m = document.querySelector('meta[name="pusher-key"]');
      key = m ? m.content : '';
    }
    if (!cluster) {
      const m = document.querySelector('meta[name="pusher-cluster"]');
      cluster = m ? m.content : '';
    }

    if (!key || !cluster) {
      console.warn('[additional] Missing Pusher key/cluster. Add data-pusher-key/data-pusher-cluster or <meta> tags.');
      return;
    }

    try {
      await loadPusher();
      // eslint-disable-next-line no-undef
      const pusher = new Pusher(key, { cluster, forceTLS: true });
      const ch = pusher.subscribe(channel);

      // Bind common variants: exact, lowercase, dotted
      events.forEach(ev => {
        ch.bind(ev,                e => applyPayload(e));
        ch.bind(ev.toLowerCase(),  e => applyPayload(e));
        ch.bind('.' + ev,          e => applyPayload(e));
      });

      console.log(`[additional] Pusher listening on "${channel}" for`, events);
    } catch (e) {
      console.error('[additional] Failed to init Pusher:', e);
    }
  });
})();
</script>
@endonce
@endpush
