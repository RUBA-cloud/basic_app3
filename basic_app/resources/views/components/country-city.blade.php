@props([
  'prefix' => 'from',              // from | to | employee | any
  'countries' => collect(),
  'cities' => null,                // Collection|array|null (optional)
  'selectedCountry' => '',
  'selectedCity' =>null,
  'title' => null,
  'badgeText' => null,
  'locked' => false,
  'required' => false,
  'isAr' => false,

  // OPTIONAL: override names/ids if needed
  'countryName' => null,
  'cityName'    => null,
  'countryId'   => null,
  'cityId'      => null,
])

@php
  // default names/ids based on prefix (your current behavior)
  $countryName = $countryName ?: ($prefix.'_country_id');
  $cityName    = $cityName    ?: ($prefix.'_city_id');

  $countryId   = $countryId   ?: $countryName;
  $cityId      = $cityId      ?: $cityName;

  // normalize cities to collection
  $citiesCol = collect($cities)->filter();
@endphp

<div class="mini-card">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div class="font-weight-bold">{{ $title }}</div>
    @if($badgeText)
      <span class="badge badge-soft badge-secondary">{{ $badgeText }}</span>
    @endif
  </div>

  {{-- Country --}}
  <div class="mb-2">
    <label class="subtle">{{ __('adminlte::adminlte.country') ?? 'Country' }}</label>
    <select
      name="{{ $countryName }}"
      id="{{ $countryId }}"
      class="form-control soft-field country-select"
      data-prefix="{{ $prefix }}"
      data-selected="{{ $selectedCountry }}"
      @disabled($locked)
      @if($required) required @endif
    >
      <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
      @foreach($countries as $c)
        <option value="{{ $c->id }}" @selected($selectedCountry === (string)$c->id)>
          {{ $isAr ? ($c->name_ar ?? $c->name_en) : ($c->name_en ?? $c->name_ar) }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- City --}}
  <div class="mb-2">
    <label class="subtle">{{ __('adminlte::adminlte.city') ?? 'City' }}</label>
    <select
      name="{{ $cityName }}"
      id="{{ $cityId }}"
      class="form-control soft-field city-select"
      data-prefix="{{ $prefix }}"
      data-selected="{{ (string)$selectedCity }}"
      @disabled($locked)
      @if($required) required @endif
    >
      <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>

      {{-- ✅ If cities are already provided (server-side), render them + select the right one --}}
      @if($citiesCol->count())
        @foreach($citiesCol as $ct)
          <option value="{{ $ct->id }}" @selected((string)$selectedCity === (string)$ct->id)>
            {{ $isAr ? ($ct->name_ar ?? $ct->name_en) : ($ct->name_en ?? $ct->name_ar) }}
          </option>
        @endforeach
      @endif
    </select>
  </div>
</div>
