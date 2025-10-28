{{-- resources/views/company_branches/_form.blade.php --}}
{{-- expects: $action (string), $method (POST|PUT|PATCH), optional $branch (model|null) --}}

@php
    // Correct source for runtime access
    $pusher_key     = config('broadcasting.connections.pusher.key');
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      style="margin:10px"
      id="company-branch-form"
      data-channel="company_branch"
      data-events='@json(["company_branch_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET','POST']))
        @method($method)
    @endunless

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    <x-upload-image :image="$branch->image ?? null"
        label="{{ __('adminlte::adminlte.choose_file') }}"
        name="image" id="logo" />

    <x-form.textarea id="name_en" name="name_en"
        label="{{ __('adminlte::adminlte.branch_name_en') }}"
        :value="old('name_en', $branch->name_en ?? '')" rows="1" />

    <x-form.textarea id="name_ar" name="name_ar" dir="rtl"
        label="{{ __('adminlte::adminlte.branch_name_ar') }}"
        :value="old('name_ar', $branch->name_ar ?? '')" rows="1" />

    <x-form.textarea id="phone" name="phone"
        label="{{ __('adminlte::adminlte.branch_phone') }}"
        :value="old('phone', $branch->phone ?? '')" rows="1" />

    <x-form.textarea id="email" name="email"
        label="{{ __('adminlte::adminlte.company_email') }}"
        :value="old('email', $branch->email ?? '')" rows="1" />

    <x-form.textarea id="address_en" name="address_en"
        label="{{ __('adminlte::adminlte.company_address_en') }}"
        :value="old('address_en', $branch->address_en ?? '')" rows="1" />

    <x-form.textarea id="address_ar" name="address_ar"
        label="{{ __('adminlte::adminlte.company_address_ar') }}"
        :value="old('address_ar', $branch->address_ar ?? '')" rows="1" />

    <x-form.textarea id="fax" name="fax"
        label="{{ __('adminlte::adminlte.fax') }}"
        :value="old('fax', $branch->fax ?? '')" rows="1" />

    <x-form.textarea id="location" name="location"
        label="{{ __('adminlte::adminlte.location') }}"
        :value="old('location', $branch->location ?? '')" rows="1" />

    <x-working-days-hours
        :working_days="old('working_days', $branch->working_days ?? [])"
        :working_hours="old('working_hours', $branch->working_hours ?? [])"
        label="{{ __('adminlte::adminlte.working_days_hours') }}" />

    <div class="form-group" style="margin:20px 0;">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', isset($branch) ? (int)$branch->is_active : 0) ? 'checked' : '' }} />
        {{ __('adminlte::adminlte.active') }}
    </div>

    <x-adminlte-button
        :label="isset($branch) ? __('adminlte::adminlte.save_information') : __('adminlte::adminlte.save_information')"
        type="submit" theme="success" class="w-100" icon="fas fa-save" />

    </form>

@once
    {{-- load Pusher once (or include globally) --}}
    <script>
      (function loadPusherOnce(){
        if (window._pusherLoaderAdded) return;
        window._pusherLoaderAdded = true;
        const s = document.createElement('script');
        s.src = 'https://js.pusher.com/8.4/pusher.min.js';
        s.async = true;
        document.head.appendChild(s);
      })();
    </script>
@endonce

@section('js')
<script>
(function(){
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

  const setCheckbox = (name, isOn) => {
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.checked = !!Number(isOn);
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const previewLogo = (url) => {
    if (!url) return;
    const img = document.querySelector('#logo-preview, [data-role="logo-preview"]');
    if (img) img.src = url;
  };

  const updateWorkingDaysHours = (days, hours) => {
    const host = document.querySelector('x-working-days-hours, [data-working-days-hours]');
    if (host) {
      host.dispatchEvent(new CustomEvent('wdh:update', {
        bubbles: true,
        detail: { working_days: days || [], working_hours: hours || [] }
      }));
    }
  };

  const applyPayload = (payload) => {
    const b = payload?.branch ?? payload ?? {};
    setField('name_en',     b.name_en);
    setField('name_ar',     b.name_ar);
    setField('email',       b.email);
    setField('phone',       b.phone);
    setField('address_en',  b.address_en);
    setField('address_ar',  b.address_ar);
    setField('location',    b.location);
    setField('fax',         b.fax);
    setCheckbox('is_active', b.is_active);
    updateWorkingDaysHours(b.working_days, b.working_hours);
    previewLogo(b.image_url || b.logo_url);

    if (window.toastr) { try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_) {} }
    console.log('[company_branch] form updated', b);
  };

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('company-branch-form');
    if (!form) return;

    const channel = form.dataset.channel || 'company_branch';

    let events;
    try { events = JSON.parse(form.dataset.events || '["company_branch_updated"]'); }
    catch { events = ['company_branch_updated']; }
    if (!Array.isArray(events) || events.length === 0) events = ['company_branch_updated'];

    // Fallback order: data-* → <meta> → server config (from PHP above)
    const dataKey     = form.dataset.pusherKey || '';
    const dataCluster = form.dataset.pusherCluster || '';
    const metaKey     = document.querySelector('meta[name="pusher-key"]')?.content || '';
    const metaCluster = document.querySelector('meta[name="pusher-cluster"]')?.content || '';
    const cfgKey      = @json($pusher_key ?? '');
    const cfgCluster  = @json($pusher_cluster ?? 'mt1');

    const key     = dataKey     || metaKey     || cfgKey;
    const cluster = dataCluster || metaCluster || cfgCluster;

    if (!key || !cluster) {
      console.warn('[company_branch] Missing Pusher key/cluster. Provide data-pusher-key/data-pusher-cluster, or <meta>, or set broadcasting config.');
      return;
    }

    const ensurePusher = () => new Promise((resolve, reject) => {
      if (window.Pusher) return resolve();
      const check = setInterval(() => { if (window.Pusher) { clearInterval(check); resolve(); } }, 50);
      setTimeout(() => { clearInterval(check); if (!window.Pusher) reject(new Error('Pusher JS not loaded')); }, 5000);
    });

    ensurePusher()
      .then(() => {
        // eslint-disable-next-line no-undef
        const pusher = new Pusher(key, { cluster, forceTLS: true });
        const ch = pusher.subscribe(channel);

        events.forEach(ev => {
          ch.bind(ev,               e => applyPayload(e));
          ch.bind(ev.toLowerCase(), e => applyPayload(e));
          ch.bind('.' + ev,         e => applyPayload(e));
        });

        console.info(`[company_branch] Pusher listening on "${channel}" for`, events);
      })
      .catch((e) => console.error('[company_branch] Failed to init Pusher:', e));
  });
})();
</script>
@endsection
