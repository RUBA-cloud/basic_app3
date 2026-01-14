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

  // ✅ selected values (old -> model)
  $selectedCountryId = (string) old('country_id', data_get($obj, 'country_id', ''));
  $selectedCityId    = (string) old('city_id', data_get($obj, 'city_id', ''));
@endphp

<form id="transportation-way-form"
      method="{{ $formMethod }}"
      action="{{ $action }}"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast["events"])'>

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
      </select>
      @error('city_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
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
      @php $isActive = (int) old('is_active', (int) data_get($obj,'is_active', 1)); @endphp

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

@push('js')
<script>
(function () {
  'use strict';

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

  document.addEventListener('DOMContentLoaded', function () {
    const countrySel = document.getElementById('country_id');
    if (countrySel) {
      countrySel.addEventListener('change', function () {
        const citySel = document.getElementById('city_id');
        if (citySel) citySel.dataset.selected = '';
        filterCities();
      });
    }
    filterCities();
  });
})();
</script>
@endpush
