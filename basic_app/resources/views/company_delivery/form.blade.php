{{-- resources/views/company_delivery/_form.blade.php --}}
{{-- expects: $action (string|Url), $method ('POST'|'PUT'|'PATCH'), optional $delivery (model|null)
    Optional Pusher config via data-* or meta tags:
    - data-pusher-key / data-pusher-cluster on the form
    - OR <meta name="pusher-key"> and <meta name="pusher-cluster"> in your layout
--}}
@php

$pusher_key     = config('broadcasting.connections.pusher.key');
$pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');

@endphp
<form id="company-delivery-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="company_delivery"
      data-events='@json(["company_delivery_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endunless

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

    {{-- Country EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', $delivery->name_en ?? '')"
        rows="1"
    />

    {{-- Country AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }} AR"
        dir="rtl"
        :value="old('name_ar', $delivery->name_ar ?? '')"
        rows="1"
    />

    {{-- Is Active (hidden 0 + checkbox 1) --}}
    <div class="form-group my-3">
        <input type="hidden" name="is_active" value="0">
        <label class="mb-0">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                {{ old('is_active', (int) data_get($delivery, 'is_active', 1)) ? 'checked' : '' }}
            >
            {{ __('adminlte::adminlte.is_active') }}
        </label>
    </div>

    <x-adminlte-button
        label="{{ __('adminlte::adminlte.save_information') }}"
        type="submit"
        theme="success"
        class="w-100"
        icon="fas fa-save"
    />
</form>

@push('js')
@once
<script>
(function(){
  'use strict';

  // --- Load Pusher once (same as Additional) ---
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

  // --- Reset helper (clears textareas/inputs; re-checks "is_active") ---
  function resetCompanyDeliveryForm() {
    const form = document.getElementById('company-delivery-form');
    if (!form) return;

    const nameEn = document.getElementById('name_en');
    const nameAr = document.getElementById('name_ar');

    if (nameEn) {
      nameEn.value = '';
      nameEn.dispatchEvent(new Event('input',  { bubbles: true }));
      nameEn.dispatchEvent(new Event('change', { bubbles: true }));
    }
    if (nameAr) {
      nameAr.value = '';
      nameAr.dispatchEvent(new Event('input',  { bubbles: true }));
      nameAr.dispatchEvent(new Event('change', { bubbles: true }));
    }

    const active = form.querySelector('input[name="is_active"][type="checkbox"]');
    if (active) {
      active.checked = true;
      active.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }

  // Optional: expose reset globally (like Additional)
  window.resetCompanyDeliveryForm = resetCompanyDeliveryForm;

  // --- Setup Pusher listener (identical pattern to Additional) ---
  document.addEventListener('DOMContentLoaded', async () => {
    const form = document.getElementById('company-delivery-form');
    if (!form) return;

    const channel = form.dataset.channel || 'company_delivery';
    const events  = JSON.parse(form.dataset.events || '["CompanyDeliveryUpdated"]');

    // Prefer data-*; fallback to <meta>
    let key     = form.dataset.pusherKey || document.querySelector('meta[name="pusher-key"]')?.content || '';
    let cluster = form.dataset.pusherCluster || document.querySelector('meta[name="pusher-cluster"]')?.content || '';

    if (!key || !cluster) {
      console.warn('[company_delivery] Missing Pusher key/cluster. Add data-pusher-key/data-pusher-cluster or <meta> tags.');
      return;
    }

    try {
      await loadPusher();
      // eslint-disable-next-line no-undef
      const pusher = new Pusher(key, { cluster, forceTLS: true });
      const ch = pusher.subscribe(channel);

      // Bind common variants (exact/lowercase/dotted), then reset form
      events.forEach(ev => {
        ch.bind(ev,                () => resetCompanyDeliveryForm());
        ch.bind(ev.toLowerCase(),  () => resetCompanyDeliveryForm());
        ch.bind('.' + ev,          () => resetCompanyDeliveryForm());
      });

      console.log(`[company_delivery] Pusher listening on "${channel}" for`, events);
    } catch (e) {
      console.error('[company_delivery] Failed to init Pusher:', e);
    }

    // Optional DOM fallback (manual trigger from anywhere)
    window.addEventListener('company-delivery:update', resetCompanyDeliveryForm);
  });
})();
</script>
@endonce
@endpush
