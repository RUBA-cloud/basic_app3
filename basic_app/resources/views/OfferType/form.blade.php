<div>
    <!-- It always seems impossible until it is done. - Nelson Mandela -->
</div>
{{-- resources/views/offers_type/_form.blade.php --}}
{{-- expects:
    $action (string|Url),
    $method ('POST'|'PUT'|'PATCH'),
    optional $offersType (model|null)
    Optional Pusher config via data-* or meta tags:
      - data-pusher-key / data-pusher-cluster on the form
      - OR <meta name="pusher-key"> and <meta name="pusher-cluster"> in your layout
--}}

@php($ot = $offersType ?? null)
@php
$pusher_key     = config('broadcasting.connections.pusher.key');
$pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');

@endphp
<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="offers-type-form"
      data-channel="offers_type"
      data-events='@json(["offer_type_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET','POST']))
        @method($method)
    @endunless

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="padding: 24px; width: 100%;">
        <div class="card-body">
            <div class="mb-3">
                <label for="name_en" class="form-label">{{ __('adminlte::adminlte.name_en') }}</label>
                <input type="text" name="name_en" id="name_en" class="form-control"
                       value="{{ old('name_en', $ot->name_en ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="name_ar" class="form-label">{{ __('adminlte::adminlte.name_ar') }}</label>
                <input type="text" name="name_ar" id="name_ar" class="form-control"
                       value="{{ old('name_ar', $ot->name_ar ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="description_en" class="form-label">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                <textarea name="description_en" id="description_en" class="form-control">{{ old('description_en', $ot->description_en ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="description_ar" class="form-label">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                <textarea name="description_ar" id="description_ar" class="form-control">{{ old('description_ar', $ot->description_ar ?? '') }}</textarea>
            </div>

            {{-- Flags --}}
            <div class="form-check mb-3">
                <input type="checkbox" name="is_discount" id="is_discount" class="form-check-input" value="1"
                       {{ old('is_discount', (int)($ot->is_discount ?? 0)) ? 'checked' : '' }}>
                <label for="is_discount" class="form-check-label">{{ __('adminlte::adminlte.is_discount') }}</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_total_gift" id="is_total_gift" class="form-check-input" value="1"
                       {{ old('is_total_gift', (int)($ot->is_total_gift ?? 0)) ? 'checked' : '' }}>
                <label for="is_total_gift" class="form-check-label">{{ __('adminlte::adminlte.is_total_gift') }}</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_total_discount" id="is_total_discount" class="form-check-input" value="1"
                       {{ old('is_total_discount', (int)($ot->is_total_discount ?? 0)) ? 'checked' : '' }}>
                <label for="is_total_discount" class="form-check-label">{{ __('adminlte::adminlte.is_total_discount') }}</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_product_count_gift" id="is_product_count_gift" class="form-check-input" value="1"
                       {{ old('is_product_count_gift', (int)($ot->is_product_count_gift ?? 0)) ? 'checked' : '' }}>
                <label for="is_product_count_gift" class="form-check-label">{{ __('adminlte::adminlte.is_product_count_gift') }}</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                       {{ old('is_active', (int)($ot->is_active ?? 1)) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
            </div>

            {{-- Discount fields --}}
            <div id="discount_fields" style="display: none;">
                <div class="mb-3">
                    <label for="discount_value_product" class="form-label">{{ __('adminlte::adminlte.discount_value_product') }}</label>
                    <input type="text" name="discount_value_product" id="discount_value_product" class="form-control"
                           value="{{ old('discount_value_product', $ot->discount_value_product ?? '') }}">
                </div>
                <div class="mb-3">
                    <label for="discount_value_delivery" class="form-label">{{ __('adminlte::adminlte.discount_value_delivery') }}</label>
                    <input type="text" name="discount_value_delivery" id="discount_value_delivery" class="form-control"
                           value="{{ old('discount_value_delivery', $ot->discount_value_delivery ?? '') }}">
                </div>
            </div>

            {{-- Total Discount field --}}
            <div id="total_discount_field" style="display: none;">
                <div class="mb-3">
                    <label for="total_discount" class="form-label">{{ __('adminlte::adminlte.total_amount') }}</label>
                    <input type="text" name="total_discount" id="total_discount" class="form-control"
                           value="{{ old('total_discount', $ot->total_discount ?? '') }}">
                </div>
            </div>

            {{-- Gift fields --}}
            <div id="gift_fields" style="display: none;">
                <div class="mb-3">
                    <label for="products_count_to_get_gift_offer" class="form-label">{{ __('adminlte::adminlte.products_count_to_get_gift_offer') }}</label>
                    <input type="number" name="products_count_to_get_gift_offer" id="products_count_to_get_gift_offer" class="form-control"
                           value="{{ old('products_count_to_get_gift_offer', $ot->products_count_to_get_gift_offer ?? '') }}">
                </div>
                <div class="mb-3" id="total_fields">
                    <label for="total_gift" class="form-label">{{ __('adminlte::adminlte.total_gift') }}</label>
                    <input type="number" name="total_gift" id="total_gift" class="form-control"
                           value="{{ old('total_gift', $ot->total_gift ?? '') }}">
                </div>
            </div>

            <x-adminlte-button
                :label="isset($ot) ? __('adminlte::adminlte.update_information') : __('adminlte::adminlte.save_information')"
                type="submit"
                theme="success"
                class="w-100"
                icon="fas fa-save"
            />
        </div>
    </div>
</form>

@push('js')
@once
<script>
(function(){
  'use strict';

  // --------- Pusher loader (branch pattern) ---------
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

  const esc = (s) => (window.CSS && CSS.escape) ? CSS.escape(s) : s;
  const $ = (sel) => document.querySelector(sel);

  // ---------- field helpers ----------
  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input', { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setCheck = (name, on) => {
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.checked = !!Number(on);
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  // ---------- UI toggle logic (your original) ----------
  function toggleFields() {
    const isDiscount = $('#is_discount');
    const isProductGift = $('#is_product_count_gift');
    const isTotalGift = $('#is_total_gift');
    const isTotalDiscount = $('#is_total_discount');

    $('#discount_fields').style.display = isDiscount.checked ? 'block' : 'none';
    $('#gift_fields').style.display = (isProductGift.checked || isTotalGift.checked) ? 'block' : 'none';
    $('#total_discount_field').style.display = isTotalDiscount.checked ? 'block' : 'none';
  }

  function toggleCheckboxes() {
    const isDiscount = $('#is_discount');
    const isProductGift = $('#is_product_count_gift');
    const isTotalGift = $('#is_total_gift');
    const isTotalDiscount = $('#is_total_discount');

    const all = [isDiscount, isTotalGift, isProductGift, isTotalDiscount];
    all.forEach(cb => cb.disabled = false);

    if (isDiscount.checked)       disableOther(isDiscount);
    else if (isTotalGift.checked) disableOther(isTotalGift);
    else if (isProductGift.checked) disableOther(isProductGift);
    else if (isTotalDiscount.checked) disableOther(isTotalDiscount);
  }

  function disableOther(selected) {
    const all = [$('#is_discount'), $('#is_total_gift'), $('#is_product_count_gift'), $('#is_total_discount')];
    all.forEach(cb => { if (cb !== selected) cb.disabled = true; });
  }

  // ---------- apply payload from Pusher ----------
  // Accepts {offers_type:{...}} or the object itself
  const applyPayload = (payload) => {
    const o = payload?.offers_type ?? payload ?? {};

    setField('name_en', o.name_en);
    setField('name_ar', o.name_ar);
    setField('description_en', o.description_en);
    setField('description_ar', o.description_ar);

    setCheck('is_discount', o.is_discount);
    setCheck('is_total_gift', o.is_total_gift);
    setCheck('is_total_discount', o.is_total_discount);
    setCheck('is_product_count_gift', o.is_product_count_gift);
    setCheck('is_active', o.is_active);

    setField('discount_value_product', o.discount_value_product);
    setField('discount_value_delivery', o.discount_value_delivery);
    setField('total_discount', o.total_discount);
    setField('products_count_to_get_gift_offer', o.products_count_to_get_gift_offer);
    setField('total_gift', o.total_gift);

    toggleFields();
    toggleCheckboxes();

    if (window.toastr) { try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_){} }
    console.log('[offers_type] form updated', o);
  };

  // ---------- init on DOM ready ----------
  document.addEventListener('DOMContentLoaded', async () => {
    // initial UI
    toggleFields();
    toggleCheckboxes();
    [ '#is_discount', '#is_product_count_gift', '#is_total_gift', '#is_total_discount' ]
      .forEach(sel => $(sel).addEventListener('change', () => { toggleFields(); toggleCheckboxes(); }));

    const form = document.getElementById('offers-type-form');
    if (!form) return;

    const channel = form.dataset.channel || 'offers_type';
    const events  = JSON.parse(form.dataset.events || '["OffersTypeUpdated"]');

    let key     = form.dataset.pusherKey || document.querySelector('meta[name="pusher-key"]')?.content || '';
    let cluster = form.dataset.pusherCluster || document.querySelector('meta[name="pusher-cluster"]')?.content || '';

    if (!key || !cluster) {
      console.warn('[offers_type] Missing Pusher key/cluster. Add data-pusher-key/data-pusher-cluster or <meta> tags.');
      return;
    }

    try {
      await loadPusher();
      // eslint-disable-next-line no-undef
      const pusher = new Pusher(key, { cluster, forceTLS: true });
      const ch = pusher.subscribe(channel);

      // Bind common variants
      events.forEach(ev => {
        ch.bind(ev,               e => applyPayload(e));
        ch.bind(ev.toLowerCase(), e => applyPayload(e));
        ch.bind('.' + ev,         e => applyPayload(e));
      });

      console.log(`[offers_type] Pusher listening on "${channel}" for`, events);
    } catch (e) {
      console.error('[offers_type] Failed to init Pusher:', e);
    }
  });
})();
</script>
@endonce
@endpush
