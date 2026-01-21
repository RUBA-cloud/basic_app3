


@php
$emp = $employee ?? null;
    $pusher_key     = config('broadcasting.connections.pusher.key');
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');

    $isAr = app()->isLocale('ar');

    // stable selected values (old -> model)
    $selectedCountryId = (string) old('country_id', $emp->country_id ?? '');
    $selectedCityId    = (string) old('city_id',    $emp->city_id ?? '');
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="employee-form"
      data-channel="employees"
      data-events='@json(["EmployeeEventUpdate"])'
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

    <div class="row g-3">

        {{-- Name --}}
        <div class="col-md-6">
            <label class="form-label">{{ __('adminlte::adminlte.full_name') }}</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ old('name', $emp->name ?? '') }}"
                   required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Email --}}
        <div class="col-md-6">
            <label class="form-label">{{ __('adminlte::adminlte.email') }}</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email', $emp->email ?? '') }}"
                   required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Password --}}
        <div class="col-md-6">
            <label class="form-label">{{ __('adminlte::adminlte.password') }}</label>
            <input type="password"
                   name="password"
                   class="form-control"
                   {{ $emp ? '' : 'required' }}
                   placeholder="{{ $emp ? __('adminlte::adminlte.password') : '' }}">
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Avatar --}}
        <div class="col-md-6">
            <x-upload-image
                :image="$emp->avatar ?? ''"
                label="{{ __('adminlte::adminlte.choose_image') }}"
                name="avatar"
                id="logo"
            />
        </div>

        {{-- ✅ Country + City (ONE TIME فقط) --}}
        <div class="col-12">
            {{-- ✅ Country + City (ONE TIME فقط) --}}
<div class="col-12">

            <x-country-city
                :countries="$countries"
                :cities="$cities"
                prefix="employee"
                countryName="country_id"
                cityName="city_id"
                countryId="country_id"
                cityId="city_id"
                selectedCountry="{{$emp->country->id}}"
                selectedCity="{{  $emp->city->id}}"
                :required="true"
                title="{{ __('adminlte::adminlte.location') ?? 'Location' }}"
                badgeText="{{ __('adminlte::adminlte.employee') ?? 'Employee' }}"
            />
            @error('country_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            @error('city_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
        </div>

        {{-- Permissions --}}
        <div class="col-12">
            <label class="form-label d-block">{{ __('adminlte::adminlte.permissions') }}</label>
            <div class="row">
                @foreach($permissions as $perm)
                    @php
                        $checked = in_array($perm->id, old('permissions', $emp?->permissions->pluck('id')->all() ?? []));
                    @endphp

                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="form-check mb-2">
                            <input type="checkbox"
                                   name="permissions[]"
                                   id="perm_{{ $perm->id }}"
                                   value="{{ $perm->id }}"
                                   class="form-check-input"
                                   {{ $checked ? 'checked' : '' }}>
                            <label for="perm_{{ $perm->id }}" class="form-check-label">
                                {{ $perm->name_en ?? ($perm->name_en ?: $perm->name_ar) }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('permissions') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

    </div>

    <div class="mt-3">
        <x-adminlte-button
            :label="isset($emp) ? __('adminlte::adminlte.update_information') : __('adminlte::adminlte.save_information')"
            type="submit"
            theme="success"
            class="w-100"
            icon="fas fa-save"
        />
    </div>
</form>

@push('js')
@once
<script>
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  const esc = (s) => (window.CSS && CSS.escape) ? CSS.escape(s) : s;

  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input',  { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setPermissions = (ids = []) => {
    const want = (ids || []).map(v => String(v));
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
      cb.checked = want.includes(String(cb.value));
      cb.dispatchEvent(new Event('change', { bubbles: true }));
    });
  };

  const previewAvatar = (url) => {
    const img = document.querySelector('#logo-preview, [data-role="logo-preview"]');
    if (img && url) img.src = url;
  };

  // ✅ Country/City (generic for component)
  const tSelect = @json(__('adminlte::adminlte.select') ?? 'Select');
  const isAr = document.documentElement.getAttribute('lang') === 'ar';

  const getCitiesUrl = (countryId) => {
    return @json(route('countries.cities', ['country' => '___ID___']))
      .replace('___ID___', encodeURIComponent(countryId));
  };

  const resetCity = (citySel) => {
    if (!citySel) return;
    citySel.innerHTML = `<option value="">${tSelect}</option>`;
  };

  const loadCities = async (countrySel, citySel) => {
    resetCity(citySel);

    const countryId = countrySel?.value;
    if (!countryId || !citySel) return;

    const selectedCity = String(citySel.dataset.selected || '');
    const url = getCitiesUrl(countryId);

    try {
      const res = await fetch(url, {
        headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
        cache: 'no-store'
      });
      if (!res.ok) return;

      const json = await res.json();
      const list = Array.isArray(json?.data) ? json.data : [];

      list.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = isAr ? (c.name_ar || c.name_en || '') : (c.name_en || c.name_ar || '');
        if (selectedCity && String(c.id) === selectedCity) opt.selected = true;
        citySel.appendChild(opt);
      });
    } catch (e) {
      console.warn('loadCities', e);
    }
  };

  // bind every pair (country-select + city-select) by same prefix
  document.querySelectorAll('.country-select').forEach(countrySel => {
    const prefix = countrySel.dataset.prefix || 'single';
    const citySel = document.querySelector(`.city-select[data-prefix="${prefix}"]`);

    countrySel.addEventListener('change', async () => {
      if (citySel) citySel.dataset.selected = '';
      await loadCities(countrySel, citySel);
    });

    // init on load (edit + old())
    if (countrySel.value) {
      loadCities(countrySel, citySel);
    } else {
      resetCity(citySel);
    }
  });

  // ✅ Broadcast apply
  const applyPayload = async (payload) => {
    const e = payload?.employee ?? payload ?? {};

    setField('name',  e.name);
    setField('email', e.email);

    // never fill password from broadcast
    if (e.clear_password) {
      const pwd = document.querySelector('[name="password"]');
      if (pwd) pwd.value = '';
    }

    // country/city (support shapes)
    const incomingCountry = e.country_id ?? e.country?.id ?? null;
    const incomingCity    = e.city_id    ?? e.city?.id    ?? null;

    // employee prefix
    const countrySel = document.querySelector('.country-select[data-prefix="employee"]');
    const citySel    = document.querySelector('.city-select[data-prefix="employee"]');

    if (incomingCountry && countrySel) {
      setField('country_id', incomingCountry);

      if (citySel) citySel.dataset.selected = incomingCity ? String(incomingCity) : '';
      await loadCities(countrySel, citySel);

      if (incomingCity) setField('city_id', incomingCity);
    }

    // permissions
    const permIds = e.permission_ids || (Array.isArray(e.permissions) ? e.permissions.map(p => p.id) : []);
    setPermissions(permIds);

    previewAvatar(e.avatar_url || e.avatar);

    if (window.bsCustomFileInput && document.querySelector('input[type="file"][name="avatar"]')) {
      try { bsCustomFileInput.init(); } catch (_) {}
    }

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch (_) {}
    }

    console.log('[employees] form updated from broadcast payload', e);
  };

  // Broadcast wiring
  const form = document.getElementById('employee-form');
  if (!form) return;

  const channel   = form.dataset.channel || 'employees';
  const eventsArr = JSON.parse(form.dataset.events || '["employee_updated"]');
  const eventName = eventsArr[0] || 'employee_updated';

  window.__pageBroadcasts = window.__pageBroadcasts || [];
  window.__pageBroadcasts.push({ channel, event: eventName, handler: applyPayload });

  if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
    window.AppBroadcast.subscribe(channel, eventName, applyPayload);
    console.info('[employees] subscribed via AppBroadcast →', channel, '/', eventName);
  } else {
    console.info('[employees] registered in __pageBroadcasts; layout will subscribe later:', channel, '/', eventName);
  }
});
</script>
@endonce
@endpush
