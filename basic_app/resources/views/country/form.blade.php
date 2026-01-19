@php
    // ✅ model may be null in create
    $obj = $country ?? null;

    // ✅ default method: POST create, PUT edit
    $spoofMethod = strtoupper($method ?? (empty($obj?->id) ? 'POST' : 'PUT'));

    // ✅ HTML form method must be GET or POST only
    $formMethod  = $spoofMethod === 'GET' ? 'GET' : 'POST';

    // ✅ refresh url that returns THIS form partial (HTML)
    $refreshUrl = $refreshUrl ?? (!empty($obj?->id) ? route('countries.form', $obj->id) : null);

    // ✅ broadcast defaults (MUST match your event->broadcastAs())
    $broadcast = $broadcast ?? [
        'channel' => 'country-channel',
        'events'  => ['country_updated'],
    ];

    // ✅ is active stable
    $isActive = (int) old('is_active', (int) data_get($obj, 'is_active', 1));
@endphp

<div id="country-form-wrap">
    <form id="country-form"
          method="{{ $formMethod }}"
          action="{{ $action }}"
          enctype="multipart/form-data"
          data-channel="{{ $broadcast['channel'] }}"
          data-events='@json($broadcast["events"])'
          data-refresh-url="{{ $refreshUrl ?? '' }}"
          data-current-id="{{ data_get($obj,'id','') }}">

        @csrf

        {{-- ✅ Spoof PUT/PATCH/DELETE --}}
        @if($formMethod === 'POST' && $spoofMethod !== 'POST')
            @method($spoofMethod)
        @endif

        @if(!empty($obj?->id))
            <input type="hidden" name="id" value="{{ $obj->id }}">
        @endif

        {{-- ✅ Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ✅ Name EN --}}
        <x-form.textarea
            id="name_en"
            name="name_en"
            label="{{ __('adminlte::adminlte.name_en') }}"
            :value="old('name_en', data_get($obj,'name_en',''))"
            rows="1"
        />
        @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

        {{-- ✅ Name AR --}}
        <x-form.textarea
            id="name_ar"
            name="name_ar"
            label="{{ __('adminlte::adminlte.name_ar') }}"
            dir="rtl"
            :value="old('name_ar', data_get($obj,'name_ar',''))"
            rows="1"
        />
        @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

        {{-- ✅ Is Active --}}
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

{{-- ✅ Listener Anchor (optional but useful like category) --}}
<div id="country-form-listener"
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

  async function rebuildCountryForm(snapshot) {
    const wrap = document.getElementById('country-form-wrap');
    const form = document.getElementById('country-form');
    if (!wrap || !form) return;

    const url = form.dataset.refreshUrl || '';
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

    // ✅ restore snapshot (optional)
    const newForm = document.getElementById('country-form');
    if (!newForm || !snapshot) return;

    if (newForm.querySelector('#name_en')) newForm.querySelector('#name_en').value = snapshot.name_en ?? '';
    if (newForm.querySelector('#name_ar')) newForm.querySelector('#name_ar').value = snapshot.name_ar ?? '';

    const chk = newForm.querySelector('input[name="is_active"][type="checkbox"]');
    if (chk) chk.checked = !!snapshot.is_active;
  }

  function takeSnapshot() {
    const form = document.getElementById('country-form');
    if (!form) return null;
    return {
      name_en: form.querySelector('#name_en')?.value ?? '',
      name_ar: form.querySelector('#name_ar')?.value ?? '',
      is_active: form.querySelector('input[name="is_active"][type="checkbox"]')?.checked ?? true,
    };
  }

  function initCountryBroadcast() {
    const form   = document.getElementById('country-form');
    const anchor = document.getElementById('country-form-listener');

    if (!form && !anchor) return;

    const channelName = String((anchor?.dataset.channel || form?.dataset.channel || '')).trim();
    const events = parseJsonSafe((anchor?.dataset.events || form?.dataset.events || '[]'), []);
    const currentId = String(anchor?.dataset.currentId || form?.dataset.currentId || '');

    if (!channelName || !Array.isArray(events) || !events.length) {
      console.warn('[country-form] missing channel/events');
      return;
    }

    function handler(payload) {
      // Accept { country: {...} } or direct {...}
      const c = payload?.country ?? payload ?? {};
      const payloadId = String(c.id ?? payload?.id ?? '');

      // ✅ only rebuild when same record (if editing)
      if (currentId && payloadId && currentId !== payloadId) return;

      const snap = takeSnapshot();
      rebuildCountryForm(snap);

      if (window.toastr) {
        try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch (_) {}
      }
    }

    // ✅ Register like your company_branch: AppBroadcast OR __pageBroadcasts
    window.__pageBroadcasts = window.__pageBroadcasts || [];

    events.forEach(function (ev) {
      if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(channelName, ev, handler);
        console.info('[country-form] subscribed via AppBroadcast →', channelName, '/', ev);
      } else {
        window.__pageBroadcasts.push({ channel: channelName, event: ev, handler: handler });
        console.info('[country-form] registered in __pageBroadcasts →', channelName, '/', ev);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', initCountryBroadcast);
})();
</script>
@endpush
