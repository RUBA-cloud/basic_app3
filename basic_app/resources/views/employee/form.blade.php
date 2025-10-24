{{-- resources/views/employees/_form.blade.php --}}
{{-- expects: $action (string), $method ('POST'|'PUT'|'PATCH'), $permissions (Collection), optional $employee (model|null)
     Optional Pusher config via data-* or meta tags:
     - data-pusher-key / data-pusher-cluster on the form
     - OR <meta name="pusher-key"> and <meta name="pusher-cluster"> in your layout
--}}

@php($emp = $employee ?? null)
@php
$pusher_key     = config('broadcasting.connections.pusher.key');
$pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');

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
        <div class="col-md-6">
            <label class="form-label">{{ __('adminlte::adminlte.full_name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $emp->name ?? '') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('adminlte::adminlte.email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $emp->email ?? '') }}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('adminlte::adminlte.password') }}</label>
            <input type="password" name="password" class="form-control" {{ $emp ? '' : 'required' }} placeholder="{{ $emp ? __('adminlte::adminlte.password') : '' }}">
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6">
            <x-upload-image :image="$emp->avatar ?? ''" label="{{ __('adminlte::adminlte.choose_image') }}" name="avatar" id="logo" />
        </div>

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
(function(){
  'use strict';

  // Load Pusher once (same pattern as your branch form)
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

  // Helpers
  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input',  { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setCheckbox = (selector, on) => {
    const el = (typeof selector === 'string')
      ? document.querySelector(selector)
      : selector;
    if (!el) return;
    el.checked = !!Number(on);
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setPermissions = (ids = []) => {
    const want = (ids || []).map(v => String(v));
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
      cb.checked = want.includes(String(cb.value));
    });
  };

  const previewAvatar = (url) => {
    const img = document.querySelector('#logo-preview, [data-role="logo-preview"]');
    if (img && url) img.src = url;
  };

  // Apply incoming payload {employee:{...}} or object itself
  const applyPayload = (payload) => {
    const e = payload?.employee ?? payload ?? {};

    setField('name',  e.name);
    setField('email', e.email);

    // Security: never fill password from payload; if you want to clear it:
    const pwd = document.querySelector('[name="password"]');
    if (pwd && e.clear_password) { pwd.value = ''; }

    // Permissions: accept array of ids or array of objects with id
    const permIds = e.permission_ids || (Array.isArray(e.permissions) ? e.permissions.map(p => p.id) : []);
    setPermissions(permIds);

    previewAvatar(e.avatar_url || e.avatar);

    if (window.bsCustomFileInput && document.querySelector('input[type="file"][name="avatar"]')) {
      try { bsCustomFileInput.init(); } catch (_) {}
    }
    if (window.toastr) { try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_){} }

    console.log('[employees] form updated', e);
  };

  // Optional manual reset (if you want to clear after event)
  window.resetEmployeeForm = function(){
    setField('name', ''); setField('email', '');
    const pwd = document.querySelector('[name="password"]'); if (pwd) pwd.value = '';
    setPermissions([]);
    previewAvatar('');
    if (window.bsCustomFileInput && document.querySelector('input[type="file"][name="avatar"]')) {
      try { bsCustomFileInput.init(); } catch (_) {}
    }
  };

  // Setup Pusher listener (pure Pusher, same as branch pattern)
  document.addEventListener('DOMContentLoaded', async () => {
    const form = document.getElementById('employee-form');
    if (!form) return;

    const channel = form.dataset.channel || 'employees';
    const events  = JSON.parse(form.dataset.events || '["EmployeeUpdated"]');

    // Prefer data-*; fallback to <meta> tags
    let key     = form.dataset.pusherKey || document.querySelector('meta[name="pusher-key"]')?.content || '';
    let cluster = form.dataset.pusherCluster || document.querySelector('meta[name="pusher-cluster"]')?.content || '';

    if (!key || !cluster) {
      console.warn('[employees] Missing Pusher key/cluster. Add data-pusher-key/data-pusher-cluster or <meta> tags.');
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

      console.log(`[employees] Pusher listening on "${channel}" for`, events);
    } catch (e) {
      console.error('[employees] Failed to init Pusher:', e);
    }
  });
})();
</script>
@endonce
@endpush
