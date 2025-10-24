<div>
    <!-- Simplicity is the essence of happiness. - Cedric Bledsoe -->
</div>
{{-- resources/views/offers/_form.blade.php --}}
{{-- expects:
    $action (string|Url),
    $method ('POST'|'PUT'|'PATCH'),
    $categories (Collection),
    $offerTypes (Collection),
    optional $offer (model|null)
    Optional Pusher config via data-* or meta tags:
      - data-pusher-key / data-pusher-cluster on the form
      - OR <meta name="pusher-key"> and <meta name="pusher-cluster"> in your layout
--}}

@php
    $isAr = app()->getLocale() === 'ar';
    $offer = $offer ?? null;
    $oldCategoryIds = collect(old('category_ids', $offer?->categories?->pluck('id')->all() ?? []))
        ->map(fn($v)=>(int)$v);

    // AdminLTE Date config (same as your page)
    $config = ['format' => 'DD/MM/YYYY'];

$pusher_key     = config('broadcasting.connections.pusher.key');
$pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');

@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="offer-form"
      data-channel="offers"
      data-events='@json(["offer_updated"])'
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

            {{-- Name EN --}}
            <div class="form-group">
                <label for="name_en">{{ __('adminlte::adminlte.name_en') }}</label>
                <input id="name_en" type="text" name="name_en" class="form-control"
                       value="{{ old('name_en', $offer->name_en ?? '') }}" required>
            </div>

            {{-- Name AR --}}
            <div class="form-group">
                <label for="name_ar">{{ __('adminlte::adminlte.name_ar') }}</label>
                <input id="name_ar" type="text" name="name_ar" class="form-control"
                       value="{{ old('name_ar', $offer->name_ar ?? '') }}" required>
            </div>

            {{-- Description EN --}}
            <div class="form-group">
                <label for="description_en">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                <textarea id="description_en" name="description_en" class="form-control" required>{{ old('description_en', $offer->description_en ?? '') }}</textarea>
            </div>

            {{-- Description AR --}}
            <div class="form-group">
                <label for="description_ar">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                <textarea id="description_ar" name="description_ar" class="form-control" required>{{ old('description_ar', $offer->description_ar ?? '') }}</textarea>
            </div>

            {{-- Categories (multi-select) --}}
            <div class="form-group">
                <label for="category_ids">{{ __('adminlte::adminlte.category') }}</label>
                <select id="category_ids" name="category_ids[]" class="form-control select2" multiple required style="width:100%;">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $oldCategoryIds->contains($category->id) ? 'selected' : '' }}>
                            {{ $isAr ? ($category->name_ar ?? $category->name_en) : ($category->name_en ?? $category->name_ar) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Offer Type (single select) --}}
            <div class="form-group">
                <label for="type_id">{{ __('adminlte::adminlte.type') }}</label>
                <select id="type_id" name="type_id" class="form-control select2" required style="width:100%;">
                    <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.type') }}</option>
                    @foreach($offerTypes as $type)
                        <option value="{{ $type->id }}" {{ (string)old('type_id', $offer->type_id ?? '') === (string)$type->id ? 'selected' : '' }}>
                            {{ $isAr ? ($type->name_ar ?? $type->name_en) : ($type->name_en ?? $type->name_ar) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dates --}}
            <div class="form-group">
                <label for="start_date">{{ __('adminlte::adminlte.start_date') }}</label>
                <x-adminlte-input-date name="start_date" :config="$config" id="start_date"
                                       placeholder="{{ __('adminlte::adminlte.choose_date') }}"
                                       value="{{ old('start_date', $offer->start_date ?? '') }}">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-primary" icon="fas fa-lg fa-calendar-alt" title="Set to Today"/>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            <div class="form-group">
                <label for="end_date">{{ __('adminlte::adminlte.end_date') }}</label>
                <x-adminlte-input-date name="end_date" :config="$config" id="end_date"
                                       placeholder="{{ __('adminlte::adminlte.choose_date') }}"
                                       value="{{ old('end_date', $offer->end_date ?? '') }}">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-primary" icon="fas fa-lg fa-calendar-alt" title="Set to Today"/>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            {{-- Active --}}
            <div class="form-check mb-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                       {{ old('is_active', (int)($offer->is_active ?? 1)) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
            </div>

            {{-- Submit --}}
            <div class="form-group">
                <x-adminlte-button
                    :label="isset($offer) ? __('adminlte::adminlte.update_information') : __('adminlte::adminlte.save_information')"
                    type="submit"
                    theme="success"
                    class="w-100"
                    icon="fas fa-save"
                />
            </div>
        </div>
    </div>
</form>

@push('js')
@once
<script>
(function(){
  'use strict';

  // --- Load Pusher once (same pattern as branch form) ---
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

  // --- Helpers (same style as branch form) ---
  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input',  { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  // For AdminLTE date input (under the hood it's an <input>)
  const setDate = (name, value) => setField(name, value);

  const setCheckbox = (name, isOn) => {
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.checked = !!Number(isOn);
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setMultiSelect = (selectorOrName, values = []) => {
    const el = document.querySelector(selectorOrName.startsWith('#') ? selectorOrName : `[name="${esc(selectorOrName)}[]"]`);
    if (!el) return;
    const want = (values || []).map(v => String(v));
    Array.from(el.options).forEach(opt => { opt.selected = want.includes(String(opt.value)); });
    if (window.jQuery && jQuery(el).hasClass('select2')) {
      jQuery(el).trigger('change.select2');
    } else {
      el.dispatchEvent(new Event('change', { bubbles: true }));
    }
  };

  // --- Apply payload from server ---
  // Accepts {offer:{...}} or the object itself
  const applyPayload = (payload) => {
    const o = payload?.offer ?? payload ?? {};

    setField('name_en', o.name_en);
    setField('name_ar', o.name_ar);
    setField('description_en', o.description_en);
    setField('description_ar', o.description_ar);

    // category ids can be array of ids or array of objects with id
    const categoryIds = o.category_ids || (Array.isArray(o.categories) ? o.categories.map(c => c.id) : []);
    setMultiSelect('#category_ids', categoryIds);

    setField('type_id', o.type_id);
    if (window.jQuery && jQuery('#type_id').hasClass('select2')) {
      jQuery('#type_id').val(o.type_id ?? '').trigger('change.select2');
    }

    setDate('start_date', o.start_date);
    setDate('end_date',   o.end_date);

    setCheckbox('is_active', o.is_active);

    if (window.toastr) { try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_){} }
    console.log('[offers] form updated', o);
  };

  // Optional: expose a hard reset, similar to other forms
  window.resetOfferForm = function(){
    setField('name_en',''); setField('name_ar','');
    setField('description_en',''); setField('description_ar','');
    setMultiSelect('#category_ids', []);
    if (window.jQuery && jQuery('#type_id').hasClass('select2')) {
      jQuery('#type_id').val('').trigger('change.select2');
    } else {
      setField('type_id','');
    }
    setDate('start_date',''); setDate('end_date','');
    setCheckbox('is_active', 1);
  };

  // --- Setup Pusher listener (pure Pusher, identical pattern to branch) ---
  document.addEventListener('DOMContentLoaded', async () => {
    const form = document.getElementById('offer-form');
    if (!form) return;

    const channel = form.dataset.channel || 'offers';
    const events  = JSON.parse(form.dataset.events || '["OfferUpdated"]');

    // Prefer data-*; fallback to <meta> tags
    let key     = form.dataset.pusherKey || document.querySelector('meta[name="pusher-key"]')?.content || '';
    let cluster = form.dataset.pusherCluster || document.querySelector('meta[name="pusher-cluster"]')?.content || '';

    if (!key || !cluster) {
      console.warn('[offers] Missing Pusher key/cluster. Add data-pusher-key/data-pusher-cluster or <meta> tags.');
      return;
    }

    try {
      await loadPusher();
      // eslint-disable-next-line no-undef
      const pusher = new Pusher(key, { cluster, forceTLS: true });
      const ch = pusher.subscribe(channel);

      // Bind common variants (exact/lowercase/dotted)
      events.forEach(ev => {
        ch.bind(ev,               e => applyPayload(e));
        ch.bind(ev.toLowerCase(), e => applyPayload(e));
        ch.bind('.' + ev,         e => applyPayload(e));
      });

      console.log(`[offers] Pusher listening on "${channel}" for`, events);
    } catch (e) {
      console.error('[offers] Failed to init Pusher:', e);
    }
  });
})();
</script>
@endonce
@endpush


@section('css')
    {{-- Tempus Dominus (Bootstrap 4) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css">
@stop

@section('js')
    {{-- Moment + Tempus Dominus (Bootstrap 4) --}}
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js"></script>

    {{-- Select2 (if not already globally included) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function () {
            // Datepickers (IDs must match the x-adminlte-input-date ids)
            $('#start_date').datetimepicker({ format: 'DD/MM/YYYY' });
            $('#end_date').datetimepicker({ format: 'DD/MM/YYYY' });

            // Select2 with RTL if Arabic
            var isAr = @json($isAr);
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                dir: isAr ? 'rtl' : 'ltr'
            });
        });
    </script>
@stop
