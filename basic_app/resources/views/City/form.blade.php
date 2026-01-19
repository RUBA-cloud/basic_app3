@php
    $cityObject = $city ?? null;

    // ✅ method handling
    $spoofMethod = strtoupper($method ?? (empty($cityObject?->id) ? 'POST' : 'PUT'));
    $formMethod  = $spoofMethod === 'GET' ? 'GET' : 'POST';

    // ✅ broadcast setup
    $broadcast = $broadcast ?? [
        'channel' => 'city-channel',
        'events'  => ['city_updated'], // MUST match broadcastAs()
    ];

    $isAr = app()->getLocale() === 'ar';

    // ✅ refresh url (partial form)
    $refreshUrl = $refreshUrl ?? (!empty($cityObject?->id) ? route('cities.update', $cityObject->id) : null);

    $selectedCountryId = (string) old('country_id', data_get($cityObject, 'country_id', ''));
    $isActive = (int) old('is_active', (int) data_get($cityObject,'is_active', 1));
@endphp

<form id="city-form"
      method="{{ $formMethod }}"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast["events"])'
      data-refresh-url="{{ $refreshUrl ?? '' }}"
      data-current-id="{{ data_get($cityObject,'id','') }}">

    @csrf

    @if($formMethod === 'POST' && $spoofMethod !== 'POST')
        @method($spoofMethod)
    @endif

    @if(!empty($cityObject?->id))
        <input type="hidden" name="id" value="{{ $cityObject->id }}">
    @endif

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- ✅ Country Select --}}
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

    {{-- City Name EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', data_get($cityObject,'name_en',''))"
        rows="1"
    />
    @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- City Name AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', data_get($cityObject,'name_ar',''))"
        rows="1"
    />
    @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Is Active --}}
    <div class="form-group mt-3">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input class="form-check-input"
                   type="checkbox"
                   id="is_active"
                   name="is_active"
                   value="1"
                   {{ $isActive ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                {{ __('adminlte::adminlte.is_active') }}
            </label>
        </div>
    </div>
    @error('is_active') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    <x-adminlte-button
        label="{{ $spoofMethod === 'POST'
            ? __('adminlte::adminlte.save_information')
            : __('adminlte::adminlte.update_information') }}"
        type="submit"
        theme="success"
        class="w-100 mt-3"
        icon="fas fa-save"
    />
</form>

{{-- ✅ Listener Anchor (مثل category) --}}
<div id="city-form-listener"
     data-channel="{{ $broadcast['channel'] }}"
     data-events='@json($broadcast["events"])'
     data-refresh-url="{{ $refreshUrl ?? '' }}"
     data-current-id="{{ data_get($cityObject,'id','') }}">
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

  const form   = document.getElementById('city-form');
  const anchor = document.getElementById('city-form-listener');

  if (!form || !anchor) {
    console.warn('[city-form] form or listener anchor not found');
    return;
  }

  const channelName = String(anchor.dataset.channel || form.dataset.channel || 'city-channel');

  let events = [];
  try { events = JSON.parse(anchor.dataset.events || form.dataset.events || '["city_updated"]'); }
  catch (_) { events = ['city_updated']; }
  if (!Array.isArray(events) || !events.length) events = ['city_updated'];

  const refreshUrl = String(anchor.dataset.refreshUrl || form.dataset.refreshUrl || '');
  const currentId  = String(anchor.dataset.currentId || form.dataset.currentId || '');

  async function rebuildCityForm() {
    if (!refreshUrl) return;

    // ✅ snapshot (اختياري)
    const snapshot = {
      country_id: form.querySelector('#country_id')?.value ?? '',
      name_en: form.querySelector('#name_en')?.value ?? '',
      name_ar: form.querySelector('#name_ar')?.value ?? '',
      is_active: form.querySelector('#is_active')?.checked ?? true,
    };

    const res = await fetch(refreshUrl, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html',
      },
      cache: 'no-store',
    });

    if (!res.ok) return;

    // ✅ partial should contain ONLY the <form id="city-form">...</form>
    const html = await res.text();
    form.outerHTML = html;

    // ✅ get the new form after replacement
    const newForm = document.getElementById('city-form');
    if (!newForm) return;

    // ✅ restore snapshot (اختياري)
    const c = newForm.querySelector('#country_id');
    if (c) c.value = snapshot.country_id;

    const en = newForm.querySelector('#name_en');
    if (en) en.value = snapshot.name_en;

    const ar = newForm.querySelector('#name_ar');
    if (ar) ar.value = snapshot.name_ar;

    const chk = newForm.querySelector('#is_active');
    if (chk) chk.checked = !!snapshot.is_active;
  }

  function applyCityPayload(payload) {
    const c = payload?.city ?? payload ?? {};
    const payloadId = String(c.id ?? payload?.id ?? '');

    // ✅ only rebuild for same city when editing
    if (currentId && payloadId && currentId !== payloadId) return;

    console.log('[city-form] broadcast payload:', c);

    rebuildCityForm();

    if (window.toastr) {
      try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch (_) {}
    }
  }

  // ✅ Register like company_branch (NO window.Echo)
  window.__pageBroadcasts = window.__pageBroadcasts || [];

  events.forEach(function (ev) {
    const entry = {
      channel: channelName,
      event: ev,
      handler: applyCityPayload,
    };

    // If AppBroadcast manager exists, subscribe now
    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
      window.AppBroadcast.subscribe(channelName, ev, applyCityPayload);
      console.info('[city-form] subscribed via AppBroadcast →', channelName, '/', ev);
    } else {
      // Otherwise push for later boot (layout will subscribe)
      window.__pageBroadcasts.push(entry);
      console.info('[city-form] registered in __pageBroadcasts →', channelName, '/', ev);
    }
  });

});
</script>
@endpush
