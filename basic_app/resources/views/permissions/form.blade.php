{{-- resources/views/permissions/_form.blade.php --}}
@php
    use Illuminate\Support\Str;

    // Inputs expected:
    //$action (string), $method ('POST'|'PUT'|'PATCH')
    // Optional: $permission (model|null), $featuresForRadios (array), $defaultFeatureKey (string|null)
    // Optional Pusher config (like your branch form):
    //   - $pusher_key, $pusher_cluster
    //   - $channel   (default 'company_branch')
    //   - $events    (default ['company_branch_updated'])
    $featuresForRadios = $featuresForRadios ?? [];
    $permissionObj     = $permission ?? null;
    $checked           = fn ($f) => old($f, data_get($permissionObj, $f, false)) ? 'checked' : '';
    $selectedFeature   = (string) old('module_name', (string) ($defaultFeatureKey ?? ''));
    $pusher_cluster = env('PUSHER_APP_KEY');
    $pusher_key     =  env('PUSHER_APP_CLUSTER', 'mt1');
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="permission-form"
      style="margin: 10px"
      data-channel="{{ $channel ?? 'payements' }}"
      data-events='@json($events ?? ["payments_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless (in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endunless

    {{-- OPTIONAL hidden module id if you have a selected module --}}
    @if(!empty($module?->id))
        <input type="hidden" name="module_id" value="{{ $module->id }}">
    @endif

    <style>
        /* Radio-card design */
        .radio-grid { display:grid; gap:.75rem; grid-template-columns: 1fr; }
        @media (min-width: 576px){ .radio-grid { grid-template-columns: 1fr; } }
        @media (min-width: 992px){ .radio-grid { grid-template-columns: 1fr; } }

        .radio-card {
            border:1px solid #e5e7eb;
            border-radius:12px;
            padding:12px 14px;
            display:flex;
            align-items:flex-start;
            gap:12px;
            background:#fff;
            transition: box-shadow .15s ease, border-color .15s ease, background .15s ease;
            cursor:pointer;
        }
        .radio-card:hover { background:#f8fafc; border-color:#d1d5db; }
        .radio-card:has(input:checked) {
            border-color:#1d4ed8; box-shadow:0 0 0 3px rgba(29,78,216,.12);
            background:#f0f6ff;
        }
        .radio-card-input { margin-top:3px; flex:0 0 auto; }
        .radio-card-body { flex:1 1 auto; }
        .radio-card-title { font-weight:600; line-height:1.25; color:#111827; }
        .radio-card-desc { font-size:.875rem; color:#6b7280; margin-top:2px; }
        .radio-badge {
            display:inline-flex; align-items:center; gap:6px;
            font-size:.75rem; padding:2px 8px; border-radius:999px;
            background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe;
        }
        .radio-card:focus-within { outline: 2px solid #1d4ed8; outline-offset: 2px; }
    </style>

    <div class="row g-3">
        {{-- FEATURE RADIOS (left) --}}
        <div class="col-12 col-lg-6">
            <label class="form-label d-block mb-2">
                <i class="fas fa-layer-group me-1"></i> {{ __('adminlte::adminlte.capabilities') }}
            </label>

            @if(!empty($featuresForRadios))
                <div class="radio-grid">
                    @foreach($featuresForRadios as $key => $meta)
                        @php
                            // Allow meta as:
                            // ['label' => 'Users', 'desc' => 'Manage users', 'badge' => 'Core', 'icon' => 'users']
                            $label = is_array($meta) ? ($meta['label'] ?? (string)$key) : (string)$meta;
                            $desc  = is_array($meta) ? ($meta['desc']  ?? null)        : null;
                            $badge = is_array($meta) ? ($meta['badge'] ?? null)        : null;
                            $icon  = is_array($meta) ? ($meta['icon']  ?? null)        : null;
                            $id    = 'feature_'.Str::slug((string)$key, '_');
                        @endphp

                        <label class="radio-card" for="{{ $id }}">
                            <input
                                type="radio"
                                name="module_name"
                                id="{{ $id }}"
                                value="{{ $key }}"
                                class="radio-card-input form-check-input"
                                {{ $selectedFeature === (string)$key ? 'checked' : '' }}
                            />
                            <div class="radio-card-body">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="radio-card-title">
                                        @if($icon)
                                            <i class="fas fa-{{ $icon }} me-2" aria-hidden="true"></i>
                                        @endif
                                        {{ $label }}
                                    </div>
                                    @if($badge)
                                        <span class="radio-badge">
                                            <i class="fas fa-star" aria-hidden="true"></i>{{ $badge }}
                                        </span>
                                    @endif
                                </div>
                                @if($desc)
                                    <div class="radio-card-desc">{{ $desc }}</div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning mb-0">
                    {{ __('adminlte::adminlte.no_features') }}
                </div>
            @endif

            {{-- Correct error target: name="module_name" --}}
            @error('module_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            @error('module_id')   <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
        </div>

        {{-- META FIELDS (right) --}}
        <div class="col-12 col-lg-6">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">{{ __('adminlte::adminlte.name_en') }}</label>
                    <input type="text" name="name_en" class="form-control"
                           value="{{ old('name_en', data_get($permissionObj, 'name_en', '')) }}" required>
                    @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">{{ __('adminlte::adminlte.name_ar') }}</label>
                    <input type="text" name="name_ar" class="form-control"
                           value="{{ old('name_ar', data_get($permissionObj, 'name_ar', '')) }}" required>
                    @error('name_ar') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label d-block">
                        <i class="fas fa-user-shield me-1"></i> {{ __('adminlte::adminlte.capabilities') }}
                    </label>

                    <div class="form-check mb-1">
                        <input type="checkbox" name="can_edit" id="can_edit" value="1"
                               class="form-check-input" {{ $checked('can_edit') }}>
                        <label for="can_edit" class="form-check-label">{{ __('adminlte::adminlte.edit') }}</label>
                    </div>
                    <div class="form-check mb-1">
                        <input type="checkbox" name="can_delete" id="can_delete" value="1"
                               class="form-check-input" {{ $checked('can_delete') }}>
                        <label for="can_delete" class="form-check-label">{{ __('adminlte::adminlte.delete') }}</label>
                    </div>
                    <div class="form-check mb-1">
                        <input type="checkbox" name="can_add" id="can_add" value="1"
                               class="form-check-input" {{ $checked('can_add') }}>
                        <label for="can_add" class="form-check-label">{{ __('adminlte::adminlte.add') }}</label>
                    </div>
                    <div class="form-check mb-1">
                        <input type="checkbox" name="can_view_history" id="can_view_history" value="1"
                               class="form-check-input" {{ $checked('can_view_history') }}>
                        <label for="can_view_history" class="form-check-label">{{ __('adminlte::adminlte.view_history') }}</label>
                    </div>

                    <hr class="my-2">

                    <div class="form-check">
                        @php $isActive = old('is_active', data_get($permissionObj, 'is_active', 1)); @endphp
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               class="form-check-input" {{ $isActive ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.active') }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit (optional) --}}
    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> {{ __('adminlte::adminlte.save') }}
        </button>
    </div>
</form>

@once
    {{-- CDN Echo + Pusher (safe if you’re not bundling) --}}
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
@endonce

@push('js')
<script>
(function () {
    const form = document.getElementById('permission-form');
    if (!form) return;

    // Read config from data-attrs or <meta> fallbacks (like your branch form)
    const ds = form.dataset;
    const pusherKey     = ds.pusherKey || (document.querySelector('meta[name="pusher-key"]')?.content || '');
    const pusherCluster = ds.pusherCluster || (document.querySelector('meta[name="pusher-cluster"]')?.content || '');
    const channelName   = ds.channel || 'company_branch';

    let events = [];
    try { events = JSON.parse(ds.events || '[]'); } catch (_) { events = []; }
    if (!Array.isArray(events) || events.length === 0) events = ['company_branch_updated'];

    // Guard: need keys for Pusher
    if (!pusherKey || !pusherCluster) {
        console.warn('[permission-form] Missing Pusher key/cluster. Add data-pusher-key / data-pusher-cluster on the form or <meta> tags.');
        return;
    }

    // Bootstrap Echo if needed
    if (!window.Echo) {
        try {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster,
                forceTLS: true,            // set false if you’re on plain ws in dev
                enabledTransports: ['ws','wss']
            });
        } catch (e) {
            console.error('[permission-form] Echo init failed:', e);
            return;
        }
    }

    const channel = window.Echo.channel(channelName);
    if (!channel) {
        console.error('[permission-form] Could not subscribe to channel:', channelName);
        return;
    }

    // Helper: apply payload keys to form inputs by name
    function applyPayloadToForm(payload) {
        if (!payload || typeof payload !== 'object') return;

        Object.entries(payload).forEach(([name, value]) => {
            const inputs = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
            if (!inputs.length) return;

            inputs.forEach((el) => {
                const type = (el.getAttribute('type') || el.tagName).toLowerCase();

                if (type === 'radio') {
                    el.checked = (String(el.value) === String(value));
                } else if (type === 'checkbox') {
                    el.checked = Boolean(value) && String(value) !== '0';
                } else {
                    el.value = (value ?? '');
                }
            });
        });
    }

    // Listen to provided events
    events.forEach((evt) => {
        channel.listen('.' + evt, (e) => {
            // Expect payload like: { module_name: 'users', can_edit: 1, name_en: 'Foo', ... }
            const payload = e?.payload || e;
            applyPayloadToForm(payload);

            // Optional: tiny blink to show it updated
            form.classList.add('border', 'border-success');
            setTimeout(() => form.classList.remove('border', 'border-success'), 800);
        });
    });

    console.info('[permission-form] Listening on', channelName, 'for', events);
})();
</script>
@endpush
