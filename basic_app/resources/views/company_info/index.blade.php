@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_info'))


@section('content')
<div class="ci-page">

    {{-- ══ HERO HEADER ══════════════════════════════════════════ --}}
    <div class="ci-hero">
        <div class="ci-hero-left">
            <div class="ci-hero-icon"><i class="fas fa-building"></i></div>
            <div>
                <h1 class="ci-hero-title">{{ __('adminlte::adminlte.company_info') }}</h1>
                <p class="ci-hero-sub">{{ now()->translatedFormat('d F Y') }}</p>
            </div>
        </div>
        <a href="{{ route('companyInfo.history', ['isHistory' => true]) }}"
           class="ci-btn ci-btn-secondary" target="_blank">
            <i class="fas fa-history" style="font-size:13px"></i>
            {{ __('adminlte::adminlte.history') }}
        </a>
    </div>

    {{-- ══ STEPPER ══════════════════════════════════════════════ --}}
    <div class="ci-stepper">
        <div class="ci-step-node">
            <div class="ci-step-circle active" id="sc1">
                <span class="ci-num">1</span>
                <i class="fas fa-check ci-chk"></i>
            </div>
            <span class="ci-step-label active" id="sl1">{{ __('adminlte::adminlte.basic_info') }}</span>
        </div>
        <div class="ci-step-line" id="sl-line-1"></div>
        <div class="ci-step-node">
            <div class="ci-step-circle" id="sc2">
                <span class="ci-num">2</span>
                <i class="fas fa-check ci-chk"></i>
            </div>
            <span class="ci-step-label" id="sl2">{{ __('adminlte::adminlte.contact_location') }}</span>
        </div>
        <div class="ci-step-line" id="sl-line-2"></div>
        <div class="ci-step-node">
            <div class="ci-step-circle" id="sc3">
                <span class="ci-num">3</span>
                <i class="fas fa-check ci-chk"></i>
            </div>
            <span class="ci-step-label" id="sl3">{{ __('adminlte::adminlte.branding') }}</span>
        </div>
    </div>

    {{-- ══ FORM ═════════════════════════════════════════════════ --}}
    <form method="POST"
          action="{{ route('companyInfo.store') }}"
          enctype="multipart/form-data"
          id="company-info-form">
        @csrf

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius:var(--ci-r);border:none;margin-bottom:1rem;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- ══════════════════════════════════════════════════════
             STAGE 1 — Basic Info  (logo + name + about/mission/vision)
        ══════════════════════════════════════════════════════ --}}
        <div id="stage-1" class="ci-stage">

            {{-- Logo upload --}}
            <div class="ci-card">
                <div class="ci-card-header">
                    <i class="fas fa-image"></i>
                    {{ __('adminlte::adminlte.company_logo') ?? 'Company Logo' }}
                </div>
                <div class="ci-card-body">
                    <label for="logo-file" style="display:block;cursor:pointer;">
                        <div class="ci-logo-wrap" id="logo-drop">
                            @if(!empty($company->image))
                                <img src="{{ $company->image }}" alt="logo" class="ci-logo-preview" id="logo-preview">
                            @else
                                <div class="ci-logo-placeholder" id="logo-placeholder">
                                    <i class="fas fa-building"></i>
                                </div>
                                <img src="" alt="logo" class="ci-logo-preview" id="logo-preview" style="display:none;">
                            @endif
                            <div class="ci-logo-text">
                                <div class="ci-logo-title">{{ __('adminlte::adminlte.choose_file') ?? 'Choose logo' }}</div>
                                <div class="ci-logo-sub">{{ __('adminlte::adminlte.logo_hint') ?? 'PNG, JPG up to 2 MB — displayed in navbar & receipts' }}</div>
                            </div>
                            <i class="fas fa-upload" style="opacity:.3;font-size:1.1rem;"></i>
                        </div>
                    </label>
                    <input type="file" name="image" id="logo-file" accept="image/*" style="display:none;">
                </div>
            </div>

            {{-- Company name --}}
            <div class="ci-card">
                <div class="ci-card-header">
                    <i class="fas fa-font"></i>
                    {{ __('adminlte::adminlte.company_name') ?? 'Company Name' }}
                </div>
                <div class="ci-card-body">
                    <div class="ci-grid-2">
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.company_name_en') ?? 'Name (English)' }}</label>
                            <input type="text" name="name_en" class="ci-input" dir="ltr"
                                   placeholder="{{ __('adminlte::adminlte.company_name_en_placeholder') ?? 'e.g. Acme Corp' }}"
                                   value="{{ old('name_en', data_get($company, 'name_en')) }}">
                            @error('name_en')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                        </div>
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.company_name_ar') ?? 'Name (Arabic)' }}</label>
                            <input type="text" name="name_ar" class="ci-input" dir="rtl"
                                   placeholder="{{ __('adminlte::adminlte.company_name_ar_placeholder') ?? 'مثال: شركة أكمي' }}"
                                   value="{{ old('name_ar', data_get($company, 'name_ar')) }}">
                            @error('name_ar')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- About / Mission / Vision --}}
            <div class="ci-card">
                <div class="ci-card-header">
                    <i class="fas fa-align-left"></i>
                    {{ __('adminlte::adminlte.about_mission_vision') }}
                </div>
                <div class="ci-card-body">

                    <div class="ci-section-tag" style="margin-top:.25rem">
                        <i class="fas fa-info-circle" style="font-size:11px"></i>
                        {{ __('adminlte::adminlte.about_us') }}
                    </div>
                    <div class="ci-grid-2" style="margin-bottom:1rem">
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.en') }}</label>
                            <textarea name="about_us_en" class="ci-textarea" dir="ltr">{{ old('about_us_en', data_get($company, 'about_us_en')) }}</textarea>
                        </div>
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.ar') }}</label>
                            <textarea name="about_us_ar" class="ci-textarea" dir="rtl">{{ old('about_us_ar', data_get($company, 'about_us_ar')) }}</textarea>
                        </div>
                    </div>

                    <div class="ci-section-tag">
                        <i class="fas fa-rocket" style="font-size:11px"></i>
                        {{ __('adminlte::adminlte.mission') }}
                    </div>
                    <div class="ci-grid-2" style="margin-bottom:1rem">
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.en') }}</label>
                            <textarea name="mission_en" class="ci-textarea" dir="ltr">{{ old('mission_en', data_get($company, 'mission_en')) }}</textarea>
                        </div>
                        <div class="ci-field-group">
                            <label class="ci-label">AR</label>
                            <textarea name="mission_ar" class="ci-textarea" dir="rtl">{{ old('mission_ar', data_get($company, 'mission_ar')) }}</textarea>
                        </div>
                    </div>

                    <div class="ci-section-tag">
                        <i class="fas fa-eye" style="font-size:11px"></i>
                        {{ __('adminlte::adminlte.vision') }}
                    </div>
                    <div class="ci-grid-2">
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.en') }}</label>
                            <textarea name="vision_en" class="ci-textarea" dir="ltr">{{ old('vision_en', data_get($company, 'vision_en')) }}</textarea>
                        </div>
                        <div class="ci-field-group">
                            <label class="ci-label">AR</label>
                            <textarea name="vision_ar" class="ci-textarea" dir="rtl">{{ old('vision_ar', data_get($company, 'vision_ar')) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ci-footer">
                <span class="ci-footer-hint">
                    <i class="fas fa-circle-info" style="font-size:12px"></i>
                    {{ __('adminlte::adminlte.step_1_of_3') }}
                </span>
                <button type="button" class="ci-btn ci-btn-primary" id="btn-next-1">
                    {{ __('adminlte::adminlte.next') ?? 'Next' }}
                    <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'left' : 'right' }}" style="font-size:12px"></i>
                </button>
            </div>
        </div>{{-- /stage-1 --}}


        {{-- ══════════════════════════════════════════════════════
             STAGE 2 — Contact · Location · Country · City
        ══════════════════════════════════════════════════════ --}}
        <div id="stage-2" class="ci-stage" style="display:none;">

            {{-- Contact --}}
            <div class="ci-card">
                <div class="ci-card-header">
                    <i class="fas fa-address-book"></i>
                    {{ __('adminlte::adminlte.contact_details') }}
                </div>
                <div class="ci-card-body">
                    <div class="ci-grid-2">
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.company_email') ?? 'Email' }}</label>
                            <input type="email" name="email" class="ci-input" dir="ltr"
                                   placeholder="{{ __('adminlte::adminlte.company_email_placeholder') ?? 'info@company.com' }}"
                                   value="{{ old('email', data_get($company, 'email')) }}">
                            @error('email')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                        </div>
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.company_phone') ?? 'Phone' }}</label>
                            <input type="text" name="phone" class="ci-input" dir="ltr"
                                   placeholder="{{ __('adminlte::adminlte.company_phone_placeholder') ?? '+971 50 000 0000' }}"
                                   value="{{ old('phone', data_get($company, 'phone')) }}">
                            @error('phone')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Country · City --}}
            <div class="ci-card">
                <div class="ci-card-header">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ __('adminlte::adminlte.location') ?? 'Location' }}
                </div>
                <div class="ci-card-body">

                    {{-- Country / City selects --}}
                    <div class="ci-grid-2" style="margin-bottom:1rem">
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.choose_country') ?? 'Country' }}</label>
                            <select name="country_id" class="ci-select" id="ci-country">
                                <option value="">{{ __('adminlte::adminlte.choose_country') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ (string)$country->id === (string)data_get($company, 'country_id') ? 'selected' : '' }}>
                                        {{ app()->isLocale('ar')
                                            ? ($country->name_ar ?? $country->name_en)
                                            : ($country->name_en ?? $country->name_ar) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.choose_city') ?? 'City' }}</label>
                            <select name="city_id" class="ci-select" id="ci-city">
                                <option value="">{{ __('adminlte::adminlte.choose_city') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ (string)$city->id === (string)data_get($company, 'city_id') ? 'selected' : '' }}>
                                        {{ app()->isLocale('ar')
                                            ? ($city->name_ar ?? $city->name_en)
                                            : ($city->name_en ?? $city->name_ar) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Address EN / AR --}}
                    <div class="ci-grid-2" style="margin-bottom:1rem">
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.company_address_en') ?? 'Address (EN)' }}</label>
                            <input type="text" name="address_en" class="ci-input" dir="ltr"
                                   value="{{ old('address_en', data_get($company, 'address_en')) }}">
                        </div>
                        <div class="ci-field-group">
                            <label class="ci-label">{{ __('adminlte::adminlte.company_address_ar') ?? 'Address (AR)' }}</label>
                            <input type="text" name="address_ar" class="ci-input" dir="rtl"
                                   value="{{ old('address_ar', data_get($company, 'address_ar')) }}">
                        </div>
                    </div>

                    {{-- Location / map link --}}
                    <div class="ci-field-group">
                        <label class="ci-label">{{ __('adminlte::adminlte.company_location') ?? 'Location / Map URL' }}</label>
                        <input type="text" name="location" class="ci-input" dir="ltr"
                               placeholder="{{ __('adminlte::adminlte.company_location_placeholder') ?? 'https://maps.google.com/...' }}"
                               value="{{ old('location', data_get($company, 'location')) }}">
                    </div>
                </div>
            </div>

            <div class="ci-footer">
                <button type="button" class="ci-btn ci-btn-secondary" id="btn-back-2">
                    <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}" style="font-size:12px"></i>
                    {{ __('adminlte::adminlte.back') ?? 'Back' }}
                </button>
                <span class="ci-footer-hint">{{ __('adminlte::adminlte.step_2_of_3') }}</span>
                <button type="button" class="ci-btn ci-btn-primary" id="btn-next-2">
                    {{ __('adminlte::adminlte.next') ?? 'Next' }}
                    <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'left' : 'right' }}" style="font-size:12px"></i>
                </button>
            </div>
        </div>{{-- /stage-2 --}}


        {{-- ══════════════════════════════════════════════════════
             STAGE 3 — Branding & Colors
        ══════════════════════════════════════════════════════ --}}
        <div id="stage-3" class="ci-stage" style="display:none;">

            @php
                $colors = $colors ?? [
                    ['name' => 'main_color',        'label' => __('adminlte::adminlte.main_color')        ?? 'Primary Color'],
                    ['name' => 'sub_color',         'label' => __('adminlte::adminlte.sub_color')         ?? 'Secondary Color'],
                    ['name' => 'text_color',        'label' => __('adminlte::adminlte.text_color')        ?? 'Text Color'],
                    ['name' => 'button_color',      'label' => __('adminlte::adminlte.button_color')      ?? 'Button Color'],
                    ['name' => 'button_text_color', 'label' => __('adminlte::adminlte.button_text_color') ?? 'Button Text'],
                    ['name' => 'icon_color',        'label' => __('adminlte::adminlte.icon_color')        ?? 'Icon Color'],
                    ['name' => 'text_filed_color',  'label' => __('adminlte::adminlte.text_field_color')  ?? 'Field Color'],
                    ['name' => 'card_color',        'label' => __('adminlte::adminlte.card_color')        ?? 'Card Color'],
                    ['name' => 'label_color',       'label' => __('adminlte::adminlte.label_color')       ?? 'Label Color'],
                    ['name' => 'hint_color',        'label' => __('adminlte::adminlte.hint_color')        ?? 'Hint Color'],
                ];
            @endphp

            <div class="ci-card">
                <div class="ci-card-header">
                    <i class="fas fa-palette"></i>
                    {{ __('adminlte::adminlte.brand_colors') ?? 'Brand Colors' }}
                </div>
                <div class="ci-card-body">
                    <p style="font-size:.78rem;opacity:.45;margin-bottom:1rem;">
                        {{ __('adminlte::adminlte.brand_colors_hint') }}
                    </p>
                    <div class="ci-color-grid">
                        @foreach($colors as $c)
                            @php $val = old($c['name'], data_get($company, $c['name']) ?? '#ffffff'); @endphp
                            <div class="ci-color-item">
                                <label class="ci-label">{{ $c['label'] }}</label>
                                <div class="ci-color-row">
                                    <div class="ci-color-swatch">
                                        <input type="color"
                                               name="{{ $c['name'] }}"
                                               id="cp-{{ $c['name'] }}"
                                               value="{{ $val }}"
                                               title="{{ $c['label'] }}">
                                    </div>
                                    <input type="text"
                                           class="ci-color-hex"
                                           id="hex-{{ $c['name'] }}"
                                           value="{{ strtoupper($val) }}"
                                           maxlength="7"
                                           placeholder="#FFFFFF">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="ci-footer">
                <button type="button" class="ci-btn ci-btn-secondary" id="btn-back-3">
                    <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}" style="font-size:12px"></i>
                    {{ __('adminlte::adminlte.back') ?? 'Back' }}
                </button>
                <span class="ci-footer-hint">{{ __('adminlte::adminlte.step_3_of_3') }}</span>
                <button type="submit" class="ci-btn ci-btn-success">
                    <i class="fas fa-save" style="font-size:13px"></i>
                    {{ __('adminlte::adminlte.save_information') ?? 'Save' }}
                </button>
            </div>
        </div>{{-- /stage-3 --}}

    </form>
</div>

{{-- Broadcast anchor --}}
<div id="company-info-form-listener"
     data-channel="company-info"
     data-events='@json(["company_info_updated"])'
     data-current-id="{{ $company?->id ?? '' }}"
     style="display:none;"></div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Stage elements ──────────────────────────────────────── */
    var stages   = [
        document.getElementById('stage-1'),
        document.getElementById('stage-2'),
        document.getElementById('stage-3'),
    ];
    var circles  = [
        document.getElementById('sc1'),
        document.getElementById('sc2'),
        document.getElementById('sc3'),
    ];
    var labels   = [
        document.getElementById('sl1'),
        document.getElementById('sl2'),
        document.getElementById('sl3'),
    ];
    var lines    = [
        document.getElementById('sl-line-1'),
        document.getElementById('sl-line-2'),
    ];

    var current = 0; // 0-indexed

    /* ── Go to stage ─────────────────────────────────────────── */
    function goTo(idx) {
        stages.forEach(function (s, i) { s.style.display = i === idx ? 'block' : 'none'; });

        circles.forEach(function (c, i) {
            c.classList.remove('active', 'done');
            if (i < idx)       c.classList.add('done');
            else if (i === idx) c.classList.add('active');
        });

        labels.forEach(function (l, i) {
            l.classList.remove('active', 'done');
            if (i < idx)       l.classList.add('done');
            else if (i === idx) l.classList.add('active');
        });

        lines.forEach(function (l, i) {
            l.classList.toggle('done', i < idx);
        });

        current = idx;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ── Wire buttons ────────────────────────────────────────── */
    document.getElementById('btn-next-1').addEventListener('click', function () { goTo(1); });
    document.getElementById('btn-back-2').addEventListener('click', function () { goTo(0); });
    document.getElementById('btn-next-2').addEventListener('click', function () { goTo(2); });
    document.getElementById('btn-back-3').addEventListener('click', function () { goTo(1); });

    /* ── Logo preview ────────────────────────────────────────── */
    var logoFile    = document.getElementById('logo-file');
    var logoPreview = document.getElementById('logo-preview');
    var logoHolder  = document.getElementById('logo-placeholder');

    if (logoFile) {
        logoFile.addEventListener('change', function () {
            if (!this.files || !this.files[0]) return;
            var url = URL.createObjectURL(this.files[0]);
            logoPreview.src     = url;
            logoPreview.style.display = 'block';
            if (logoHolder) logoHolder.style.display = 'none';
        });
    }

    /* ── Color picker ↔ hex input sync ──────────────────────── */
    var colorNames = [
        'main_color','sub_color','text_color','button_color',
        'button_text_color','icon_color','text_filed_color',
        'card_color','label_color','hint_color',
    ];

    colorNames.forEach(function (name) {
        var picker = document.getElementById('cp-' + name);
        var hex    = document.getElementById('hex-' + name);
        if (!picker || !hex) return;

        /* picker → hex text */
        picker.addEventListener('input', function () {
            hex.value = this.value.toUpperCase();
        });

        /* hex text → picker */
        hex.addEventListener('input', function () {
            var v = this.value.trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(v)) picker.value = v;
        });
    });

    /* ── Jump to stage 3 if branding field has validation error ─ */
    var stage3Fields = colorNames;
    var hasStage3Err = stage3Fields.some(function (n) {
        return document.querySelector('small.text-danger[data-field="' + n + '"]');
    });
    if (hasStage3Err) goTo(2);

    /* ── Broadcast: field setter ─────────────────────────────── */
    function setField(name, value) {
        if (value == null) return;
        var el = document.querySelector('[name="' + name + '"]');
        if (!el) return;
        el.value = value;
        el.dispatchEvent(new Event('input',  { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function applyPayload(payload) {
        var data = (payload && payload.company) ? payload.company : (payload || {});

        [
            'name_en','name_ar','email','phone',
            'address_en','address_ar','location',
            'about_us_en','about_us_ar',
            'mission_en','mission_ar',
            'vision_en','vision_ar',
        ].forEach(function (f) { setField(f, data[f]); });

        colorNames.forEach(function (f) {
            setField(f, data[f]);
            if (window.AppBrand && data[f]) {
                var cssVar = window.AppBrand.map && window.AppBrand.map[f];
                if (cssVar) document.documentElement.style.setProperty(cssVar, data[f]);
            }
        });

        var src = data.image_url || data.image;
        if (src && logoPreview) {
            logoPreview.src           = src;
            logoPreview.style.display = 'block';
            if (logoHolder) logoHolder.style.display = 'none';
        }

        if (window.toastr) toastr.success('{{ __('adminlte::adminlte.saved_successfully') }}');
    }

    /* ── Register with AppBroadcast ──────────────────────────── */
    var CH = 'company-info', EV = 'company_info_updated';
    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(CH, EV, applyPayload);
    } else {
        window.__pageBroadcasts = window.__pageBroadcasts || [];
        window.__pageBroadcasts.push({ channel: CH, event: EV, handler: applyPayload });
    }

});
</script>
@endpush