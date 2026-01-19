@php
  // ✅ model may be null in create
  $obj = $transpartationWay ?? null;

  // ✅ default method: POST for create, PUT for edit
  $spoofMethod = strtoupper($method ?? (empty($obj?->id) ? 'POST' : 'PUT'));
  $formMethod  = $spoofMethod === 'GET' ? 'GET' : 'POST';

  $broadcast = $broadcast ?? [
      'channel' => 'transpartation_way_channel',
      'events'  => ['transportation_way_updated'],
  ];

  $isAr = app()->getLocale() === 'ar';

  // ✅ refresh url (partial form) — لازم route يرجع نفس الفورم partial
  $refreshUrl = $refreshUrl ?? (!empty($obj?->id) ? route('transpartation_ways.update', $obj->id) : null);

  // ✅ selected values (old -> model)
  $selectedCountryId = (string) old('country_id', data_get($obj, 'country_id', ''));
  $selectedCityId    = (string) old('city_id', data_get($obj, 'city_id', ''));
  $selectedTypeId    = (string) old('type_id', data_get($obj, 'type_id', ''));

  $isActive = (int) old('is_active', (int) data_get($obj,'is_active', 1));
@endphp

<div id="transportation-way-form-wrap">
<form id="transportation-way-form"
      method="{{ $formMethod }}"
      action="{{ $action }}"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast["events"])'
      data-refresh-url="{{ $refreshUrl ?? '' }}"
      data-current-id="{{ data_get($obj,'id','') }}">

  @csrf

  @if($formMethod === 'POST' && $spoofMethod !== 'POST')
      @method($spoofMethod)
  @endif

  @if(!empty($obj?->id))
      <input type="hidden" name="id" value="{{ $obj->id }}">
  @endif

  {{-- Errors --}}
  @if ($errors->any())
      <div class="alert alert-danger mb-3">
          <ul class="mb-0">
              @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
      </div>
  @endif

  {{-- ✅ Country --}}
  <div class="form-group mb-3">
      <label for="country_id">{{ __('adminlte::adminlte.country') }}</label>
      <select id="country_id"
              name="country_id"
              class="form-control @error('country_id') is-invalid @enderror">
          <option value="">{{ __('adminlte::adminlte.choose_country') }}</option>

          @foreach(($countries ?? []) as $country)
              <option value="{{ $country->id }}"
                  {{ (string)$country->id === $selectedCountryId ? 'selected' : '' }}>
                  {{ $isAr ? ($country->name_ar ?? $country->name_en) : ($country->name_en ?? $country->name_ar) }}
              </option>
          @endforeach
      </select>
      @error('country_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
  </div>

  {{-- ✅ City (filtered by country) --}}
  <div class="form-group mb-3">
      <label for="city_id">{{ __('adminlte::adminlte.city') }}</label>
      <select id="city_id"
              name="city_id"
              class="form-control @error('city_id') is-invalid @enderror"
              data-selected="{{ $selectedCityId }}">
          <option value="">{{ __('adminlte::adminlte.choose_city') }}</option>

          @foreach(($cities ?? []) as $c)
              <option value="{{ $c->id }}"
                      data-country-id="{{ $c->country_id }}"
                      {{ (string)$c->id === $selectedCityId ? 'selected' : '' }}>
                  {{ $isAr ? ($c->name_ar ?? $c->name_en) : ($c->name_en ?? $c->name_ar) }}
              </option>
          @endforeach
      </select>fv
      @error('city_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
  </div>

<div class="form-group mb-3">
  <label for="type_id">{{ __('adminlte::adminlte.transpartationType') }}</label>

  <select id="type_id"
          name="type_id"
          class="form-control @error('type_id') is-invalid @enderror"
          required>
    <option value="">{{ __('adminlte::adminlte.select') }}</option>

    @foreach(($transpartationsType ?? []) as $t)
      <option value="{{ $t->id }}"
        {{ (string)$t->id === (string)$selectedTypeId ? 'selected' : '' }}>
        {{ $isAr ? ($t->name_ar ?? $t->name_en) : ($t->name_en ?? $t->name_ar) }}
      </option>
    @endforeach
  </select>

  @error('type_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
</div>

  {{-- Name EN --}}
  <x-form.textarea
      id="name_en"
      name="name_en"
      label="{{ __('adminlte::adminlte.name_en') }}"
      :value="old('name_en', data_get($obj,'name_en',''))"
  />
  @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

  {{-- Name AR --}}
  <x-form.textarea
      id="name_ar"
      name="name_ar"
      label="{{ __('adminlte::adminlte.name_ar') }}"
      dir="rtl"
      :value="old('name_ar', data_get($obj,'name_ar',''))"
  />
  @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

  {{-- Days Count --}}
  <div class="form-group mb-3">
      <label for="days_count">{{ __('adminlte::adminlte.days_count') }}</label>
      <input type="number"
             min="0"
             step="1"
             id="days_count"
             name="days_count"
             value="{{ old('days_count', data_get($obj,'days_count','')) }}"
             class="form-control @error('days_count') is-invalid @enderror">
      @error('days_count') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
  </div>

  {{-- Is Active --}}
  <div class="form-group" style="margin: 20px 0;">
      <input type="hidden" name="is_active" value="0">
      <label class="mb-0">
          <input type="checkbox" name="is_active" value="1" {{ $isActive ? 'checked' : '' }}>
          {{ __('adminlte::adminlte.is_active') }}
      </label>
  </div>
  @error('is_active') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

  <x-adminlte-button
      label="{{ $spoofMethod === 'POST'
          ? __('adminlte::adminlte.save_information')
          : __('adminlte::adminlte.update_information') }}"
      type="submit"
      theme="success"
      class="w-100"
      icon="fas fa-save"
  />
</form>
</div>

{{-- ✅ Listener Anchor (مثل country) --}}
<div id="transportation-way-form-listener"
     data-channel="{{ $broadcast['channel'] }}"
     data-events='@json($broadcast["events"])'
     data-refresh-url="{{ $refreshUrl ?? '' }}"
     data-current-id="{{ data_get($obj,'id','') }}">
</div>

@push('js')
<script>
(function () {
  'use strict';

  function parseJsonSafe(v, fallback) {
    try { return JSON.parse(v); } catch (_) { return fallback; }
  }

  function filterCities() {
    const countrySel = document.getElementById('country_id');
    const citySel    = document.getElementById('city_id');
    if (!countrySel || !citySel) return;

    const selectedCountryId = String(countrySel.value || '');
    const selectedCityId = String(citySel.dataset.selected || citySel.value || '');

    let hasVisible = false;

    Array.from(citySel.options).forEach(opt => {
      if (!opt.value) return;

      const cId = String(opt.getAttribute('data-country-id') || '');
      const show = !selectedCountryId || (cId === selectedCountryId);

      opt.hidden = !show;
      opt.disabled = !show;

      if (show) hasVisible = true;
    });

    const currentOpt = citySel.options[citySel.selectedIndex];
    const currentCountry = currentOpt ? String(currentOpt.getAttribute('data-country-id') || '') : '';

    if (selectedCountryId && currentOpt && currentOpt.value && currentCountry !== selectedCountryId) {
      citySel.value = '';
    }

    if (selectedCityId) {
      const target = Array.from(citySel.options).find(o => o.value === selectedCityId && !o.hidden);
      if (target) citySel.value = selectedCityId;
    }

    if (selectedCountryId && !hasVisible) {
      citySel.value = '';
    }
  }

  function takeSnapshot(form) {
    return {
      country_id: form.querySelector('#country_id')?.value ?? '',
      city_id: form.querySelector('#city_id')?.value ?? '',
      name_en: form.querySelector('#name_en')?.value ?? '',
      name_ar: form.querySelector('#name_ar')?.value ?? '',
      days_count: form.querySelector('#days_count')?.value ?? '',
      is_active: form.querySelector('input[name="is_active"][type="checkbox"]')?.checked ?? true,
    };
  }

  async function refreshForm(snapshot) {
    const wrap = document.getElementById('transportation-way-form-wrap');
    const form = document.getElementById('transportation-way-form');
    if (!wrap || !form) return;

    const url = String(form.dataset.refreshUrl || '');
    if (!url) return;

    const res = await fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html',
      },
      cache: 'no-store',
    });

    if (!res.ok) return;

    wrap.innerHTML = await res.text();

    // ✅ restore snapshot
    const newForm = document.getElementById('transportation-way-form');
    if (!newForm || !snapshot) return;

    const c = newForm.querySelector('#country_id');
    if (c) c.value = snapshot.country_id;

    // city select has dataset.selected
    const citySel = newForm.querySelector('#city_id');
    if (citySel) {
      citySel.dataset.selected = snapshot.city_id || '';
    }

    if (newForm.querySelector('#name_en')) newForm.querySelector('#name_en').value = snapshot.name_en;
    if (newForm.querySelector('#name_ar')) newForm.querySelector('#name_ar').value = snapshot.name_ar;
    if (newForm.querySelector('#days_count')) newForm.querySelector('#days_count').value = snapshot.days_count;

    const chk = newForm.querySelector('input[name="is_active"][type="checkbox"]');
    if (chk) chk.checked = !!snapshot.is_active;

    // ✅ re-run city filter after rebuild
    filterCities();

    // ✅ re-bind country change
    const countrySel = document.getElementById('country_id');
    if (countrySel) {
      countrySel.addEventListener('change', function () {
        const citySel2 = document.getElementById('city_id');
        if (citySel2) citySel2.dataset.selected = '';
        filterCities();
      });
    }
  }

  function initBroadcast() {
    const form   = document.getElementById('transportation-way-form');
    const anchor = document.getElementById('transportation-way-form-listener');

    if (!form && !anchor) return;

    const channelName = String(anchor?.dataset.channel || form?.dataset.channel || '').trim();
    const events = parseJsonSafe((anchor?.dataset.events || form?.dataset.events || '[]'), []);
    const currentId = String(anchor?.dataset.currentId || form?.dataset.currentId || '');

    if (!channelName || !Array.isArray(events) || !events.length) {
      console.warn('[transportation-way-form] missing channel/events');
      return;
    }

    function handler(payload) {
      // Accept { transportationWay: {...} } OR direct {...}
      const t =
        payload?.transportationWay ??
        payload?.transpartationWay ??
        payload?.transportation_way ??
        payload?.transpartation_way ??
        payload ??
        {};

      const payloadId = String(t.id ?? payload?.id ?? '');

      // ✅ only refresh when same record if editing
      if (currentId && payloadId && currentId !== payloadId) return;

      const snap = takeSnapshot(form);
      refreshForm(snap);

      if (window.toastr) {
        try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch (_) {}
      }

      console.log('[transportation-way-form] broadcast payload received:', t);
    }

    // ✅ Register like country (NO Echo)
    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach(function (ev) {
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, ev, handler);
        console.info('[transportation-way-form] subscribed via AppBroadcast →', channelName, '/', ev);
      } else {
        window.__pageBroadcasts.push({ channel: channelName, event: ev, handler: handler });
        console.info('[transportation-way-form] registered in __pageBroadcasts →', channelName, '/', ev);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    // keep your original behavior
    const countrySel = document.getElementById('country_id');
    if (countrySel) {
      countrySel.addEventListener('change', function () {
        const citySel = document.getElementById('city_id');
        if (citySel) citySel.dataset.selected = '';
        filterCities();
      });
    }
    filterCities();

    // start pusher
    initBroadcast();
  });
})();
</script>
@endpush
