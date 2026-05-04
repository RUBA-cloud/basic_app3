@extends('adminlte::page')

@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.permissions'))



@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

<div class="pf-page">

    {{-- ── Page header ── --}}
    <div class="pf-header">
        <div class="pf-header-icon"><i class="fas fa-lock"></i></div>
        <div>
            <h1 class="pf-header-title">
                {{ __('adminlte::adminlte.create') }}
                {{ __('adminlte::adminlte.permissions') }}
            </h1>
            <p class="pf-header-sub">
                {{ $isRtl ? 'تحديد قواعد الوصول لمستخدم أو دور' : 'Define access rules for a user or role' }}
            </p>
        </div>
    </div>

    {{-- Success alert --}}
    @if(session('success'))
        <div style="background:#EAF3DE;color:#27500A;border-radius:10px;padding:.75rem 1rem;margin-bottom:1rem;font-size:13px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-check-circle" style="font-size:14px"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
        <div style="background:#FCEBEB;color:#791F1F;border-radius:10px;padding:.75rem 1rem;margin-bottom:1rem;font-size:13px;">
            <div style="font-weight:700;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-exclamation-circle" style="font-size:14px"></i>
                {{ $isRtl ? 'يرجى تصحيح الأخطاء التالية:' : 'Please fix the following errors:' }}
            </div>
            <ul style="margin:0;padding-{{ $isRtl ? 'right' : 'left' }}:1.2rem;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('permissions.store') }}" id="pf-form">
        @csrf
        @method('POST')

        {{-- ══ §1  Basic information ══════════════════════════ --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <div class="pf-section-head-icon"><i class="fas fa-user"></i></div>
                <div>
                    <div class="pf-section-title">
                        {{ $isRtl ? 'المعلومات الأساسية' : 'Basic information' }}
                    </div>
                    <div class="pf-section-sub">
                        {{ $isRtl ? 'الاسم والمستخدم المخصص' : 'Names and assigned user' }}
                    </div>
                </div>
            </div>
            <div class="pf-section-body">
                <div class="pf-grid-2">
                    <div class="pf-fg">
                        <label class="pf-label" for="name_en">
                            {{ __('adminlte::adminlte.name_en') }}
                        </label>
                        <input type="text"
                               id="name_en" name="name_en"
                               class="pf-control"
                               value="{{ old('name_en') }}"
                               placeholder="{{ $isRtl ? 'مثال: إدارة المنتجات' : 'e.g. Products management' }}"
                               dir="ltr">
                        @error('name_en')
                            <small style="color:#791F1F;font-size:11px;margin-top:2px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="pf-fg">
                        <label class="pf-label" for="name_ar">
                            {{ __('adminlte::adminlte.name_ar') }}
                        </label>
                        <input type="text"
                               id="name_ar" name="name_ar"
                               class="pf-control"
                               value="{{ old('name_ar') }}"
                               placeholder="مثال: إدارة المنتجات"
                               dir="rtl">
                        @error('name_ar')
                            <small style="color:#791F1F;font-size:11px;margin-top:2px;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="pf-fg">
                    <label class="pf-label" for="user_id">
                        {{ __('adminlte::adminlte.user_name') }}
                    </label>
                    <select id="user_id" name="user_id" class="pf-control">
                        <option value="">
                            {{ $isRtl ? 'اختر المستخدم…' : 'Select a user…' }}
                        </option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}"
                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} — ID: {{ $user->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <small style="color:#791F1F;font-size:11px;margin-top:2px;">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ══ §2  Module radio cards ══════════════════════════ --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <div class="pf-section-head-icon"><i class="fas fa-th-large"></i></div>
                <div>
                    <div class="pf-section-title">
                        {{ $isRtl ? 'الوحدة' : 'Module' }}
                    </div>
                    <div class="pf-section-sub">
                        {{ $isRtl ? 'اختر القسم الذي تغطيه هذه الصلاحية' : 'Choose which section this permission covers' }}
                    </div>
                </div>
            </div>
            <div class="pf-section-body">
                <div class="pf-module-grid">
                    @foreach($modulesRow ?? [] as $module)
                        @php
                            $mKey   = is_array($module) ? ($module['key']      ?? $module) : $module;
                            $mLabelEn = is_array($module) ? ($module['label_en'] ?? $mKey)  : $module;
                            $mLabelAr = is_array($module) ? ($module['label_ar'] ?? '')      : '';
                            $mIcon  = is_array($module) ? ($module['icon']     ?? 'fas fa-cube') : 'fas fa-cube';
                            $mChecked = old('module_key', $defaultModuleKey ?? '') === $mKey;
                        @endphp
                        <label class="pf-mc">
                            <input type="radio"
                                   name="module_key"
                                   value="{{ $mKey }}"
                                   {{ $mChecked ? 'checked' : '' }}>
                            <div class="pf-mc-body">
                                <div class="pf-mc-top">
                                    <div class="pf-mc-icon">
                                        <i class="{{ $mIcon }}"></i>
                                    </div>
                                    <div class="pf-mc-indicator">
                                        <div class="pf-mc-dot"></div>
                                    </div>
                                </div>
                                <div class="pf-mc-name">{{ $isRtl && $mLabelAr ? $mLabelAr : $mLabelEn }}</div>
                                @if($mLabelAr && !$isRtl)
                                    <div class="pf-mc-sub">{{ $mLabelAr }}</div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('module_key')
                    <small style="color:#791F1F;font-size:11px;margin-top:6px;display:block;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        {{-- ══ §3  Features ════════════════════════════════════ --}}
        @if(!empty($featuresForRadios))
        <div class="pf-section">
            <div class="pf-section-head">
                <div class="pf-section-head-icon"><i class="fas fa-layer-group"></i></div>
                <div>
                    <div class="pf-section-title">
                        {{ $isRtl ? 'الميزات' : 'Features' }}
                    </div>
                    <div class="pf-section-sub">
                        {{ $isRtl ? 'اختر الميزات المطبّقة على هذه الوحدة' : 'Select which features apply to this module' }}
                    </div>
                </div>
            </div>
            <div class="pf-section-body">
                <div class="pf-feature-wrap">
                    @foreach($featuresForRadios as $fKey => $fLabel)
                        @php
                            $fChecked = in_array($fKey, old('features', $defaultFeatureKey ? [$defaultFeatureKey] : []));
                        @endphp
                        <label class="pf-pill {{ $fChecked ? 'is-active' : '' }}"
                               data-pf-pill>
                            <input type="checkbox"
                                   name="features[]"
                                   value="{{ $fKey }}"
                                   {{ $fChecked ? 'checked' : '' }}>
                            <span class="pf-pill-dot"></span>
                            {{ $fLabel }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ══ §4  Access rules / toggles ═════════════════════ --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <div class="pf-section-head-icon"><i class="fas fa-sliders-h"></i></div>
                <div>
                    <div class="pf-section-title">
                        {{ $isRtl ? 'قواعد الوصول' : 'Access rules' }}
                    </div>
                    <div class="pf-section-sub">
                        {{ $isRtl ? 'فعّل أو أوقف كل صلاحية' : 'Toggle each permission on or off' }}
                    </div>
                </div>
            </div>
            <div class="pf-section-body">

                @php
                $permToggles = [
                    'can_add'          => ['icon' => 'fas fa-plus',         'label' => __('adminlte::adminlte.add')          ?? 'Can add',      'default' => 1],
                    'can_edit'         => ['icon' => 'fas fa-edit',         'label' => __('adminlte::adminlte.edit')         ?? 'Can edit',     'default' => 1],
                    'can_delete'       => ['icon' => 'fas fa-trash',        'label' => __('adminlte::adminlte.delete')       ?? 'Can delete',   'default' => 0],
                    'can_view'         => ['icon' => 'fas fa-eye',          'label' => __('adminlte::adminlte.details')      ?? 'Can view',     'default' => 1],
                    'can_view_history' => ['icon' => 'fas fa-history',      'label' => __('adminlte::adminlte.view_history') ?? 'View history', 'default' => 0],
                    'is_active'        => ['icon' => 'fas fa-check-circle', 'label' => __('adminlte::adminlte.active')       ?? 'Is active',    'default' => 1],
                ];
                @endphp

                <div class="pf-toggle-grid">
                    @foreach($permToggles as $tKey => $tCfg)
                        @php $tOn = old($tKey, $tCfg['default']) == 1; @endphp
                        <label class="pf-toggle {{ $tOn ? 'is-on' : '' }}" data-pf-toggle>
                            <div class="pf-toggle-left">
                                <div class="pf-toggle-icon">
                                    <i class="{{ $tCfg['icon'] }}"></i>
                                </div>
                                <span class="pf-toggle-label">{{ $tCfg['label'] }}</span>
                            </div>
                            {{-- hidden 0 ensures the field is always posted --}}
                            <input type="hidden" name="{{ $tKey }}" value="0">
                            <input type="checkbox"
                                   name="{{ $tKey }}"
                                   value="1"
                                   {{ $tOn ? 'checked' : '' }}>
                            <div class="pf-sw"></div>
                        </label>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- ══ Form footer ═════════════════════════════════════ --}}
        <div class="pf-footer">
            <a href="{{ route('permissions.index') }}" class="pf-btn pf-btn-secondary">
                @if($isRtl)
                    {{ $isRtl ? 'إلغاء' : 'Cancel' }}
                    <i class="fas fa-times"></i>
                @else
                    <i class="fas fa-times"></i>
                    {{ $isRtl ? 'إلغاء' : 'Cancel' }}
                @endif
            </a>
            <button type="submit" class="pf-btn pf-btn-primary">
                @if($isRtl)
                    {{ __('adminlte::adminlte.save') ?? 'Save' }}
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-check"></i>
                    {{ __('adminlte::adminlte.save') ?? 'Save permission' }}
                @endif
            </button>
        </div>

    </form>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Feature pills ─────────────────────────────────── */
    document.querySelectorAll('[data-pf-pill]').forEach(function (pill) {
        var cb = pill.querySelector('input[type="checkbox"]');
        if (!cb) return;
        pill.addEventListener('click', function (e) {
            if (e.target === cb) return;          /* let native click pass through */
            cb.checked = !cb.checked;
            pill.classList.toggle('is-active', cb.checked);
        });
        cb.addEventListener('change', function () {
            pill.classList.toggle('is-active', cb.checked);
        });
    });

    /* ── Permission toggles ────────────────────────────── */
    document.querySelectorAll('[data-pf-toggle]').forEach(function (tog) {
        var cb = tog.querySelector('input[type="checkbox"]');
        if (!cb) return;
        tog.addEventListener('click', function (e) {
            if (e.target === cb) return;
            cb.checked = !cb.checked;
            tog.classList.toggle('is-on', cb.checked);
        });
        cb.addEventListener('change', function () {
            tog.classList.toggle('is-on', cb.checked);
        });
    });

});
</script>
@endpush

@endsection