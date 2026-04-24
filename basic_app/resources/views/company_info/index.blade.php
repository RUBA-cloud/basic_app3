@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_info'))

@section('content')
<div class="container-fluid py-4">

    <x-adminlte-card
        title="{{ __('adminlte::adminlte.company_info') }}"
        icon="fas fa-building"
        removable collapsible
        class="lw-list-card">

        {{-- Header Actions --}}
        <div class="d-flex flex-wrap justify-content-end align-items-center mb-3">
            <a href="{{ route('companyInfo.history', ['isHistory' => true]) }}"
               class="btn btn-outline-secondary mr-2"
               target="_blank"
               title="{{ __('adminlte::adminlte.history') }}">
                <i class="fas fa-history"></i>
                {{ __('adminlte::adminlte.history') }}
            </a>
        </div>

        <form method="POST"
              action="{{ route('companyInfo.store') }}"
              enctype="multipart/form-data"
              id="company-info-form">
            @csrf

            {{-- Company Logo --}}
            <x-upload-image
                :image="$company->image ?? null"
                label="{{ __('adminlte::adminlte.choose_file') }}"
                name="image"
                id="logo" />

            {{-- Name EN / AR --}}
            <div class="row">
                <div class="col-md-6">
                    <x-form.textarea
                        name="name_en"
                        label="{{ __('adminlte::adminlte.company_name_en') }}"
                        dir="ltr"
                        placeholder="{{ __('adminlte::adminlte.company_name_en_placeholder') }}"
                        :value="data_get($company, 'name_en')" />
                    @error('name_en')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6">
                    <x-form.textarea
                        name="name_ar"
                        label="{{ __('adminlte::adminlte.company_name_ar') }}"
                        dir="rtl"
                        placeholder="{{ __('adminlte::adminlte.company_name_ar_placeholder') }}"
                        :value="data_get($company, 'name_ar')" />
                    @error('name_ar')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            {{-- Email / Phone --}}
            <div class="row">
                <div class="col-md-6">
                    <x-form.textarea
                        name="email"
                        label="{{ __('adminlte::adminlte.company_email') }}"
                        type="email"
                        dir="ltr"
                        placeholder="{{ __('adminlte::adminlte.company_email_placeholder') }}"
                        :value="data_get($company, 'email')" />
                    @error('email')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6">
                    <x-form.textarea
                        name="phone"
                        label="{{ __('adminlte::adminlte.company_phone') }}"
                        type="text"
                        dir="ltr"
                        placeholder="{{ __('adminlte::adminlte.company_phone_placeholder') }}"
                        :value="data_get($company, 'phone')" />
                    @error('phone')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            {{-- Address EN / AR --}}
            <div class="row">
                <div class="col-md-6">
                    <x-form.textarea
                        name="address_en"
                        label="{{ __('adminlte::adminlte.company_address_en') }}"
                        dir="ltr"
                        :value="data_get($company, 'address_en')" />
                </div>
                <div class="col-md-6">
                    <x-form.textarea
                        name="address_ar"
                        label="{{ __('adminlte::adminlte.company_address_ar') }}"
                        dir="rtl"
                        :value="data_get($company, 'address_ar')" />
                </div>
            </div>

            {{-- Location --}}
            <x-form.textarea
                name="location"
                label="{{ __('adminlte::adminlte.company_location') }}"
                dir="ltr"
                placeholder="{{ __('adminlte::adminlte.company_location_placeholder') }}"
                :value="data_get($company, 'location')" />

            {{-- About / Mission / Vision --}}
            <div class="row">
                <div class="col-md-6">
                    <x-form.textarea
                        name="about_us_en"
                        label="{{ __('adminlte::adminlte.about_us_en') }}"
                        dir="ltr"
                        :value="data_get($company, 'about_us_en')" />
                </div>
                <div class="col-md-6">
                    <x-form.textarea
                        name="about_us_ar"
                        label="{{ __('adminlte::adminlte.about_us_ar') }}"
                        dir="rtl"
                        :value="data_get($company, 'about_us_ar')" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-form.textarea
                        name="mission_en"
                        label="{{ __('adminlte::adminlte.mission_en') }}"
                        dir="ltr"
                        :value="data_get($company, 'mission_en')" />
                </div>
                <div class="col-md-6">
                    <x-form.textarea
                        name="mission_ar"
                        label="{{ __('adminlte::adminlte.mission_ar') }}"
                        dir="rtl"
                        :value="data_get($company, 'mission_ar')" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-form.textarea
                        name="vision_en"
                        label="{{ __('adminlte::adminlte.vision_en') }}"
                        dir="ltr"
                        :value="data_get($company, 'vision_en')" />
                </div>
                <div class="col-md-6">
                    <x-form.textarea
                        name="vision_ar"
                        label="{{ __('adminlte::adminlte.vision_ar') }}"
                        dir="rtl"
                        :value="data_get($company, 'vision_ar')" />
                </div>
            </div>

            {{-- Colors --}}
            @php
                $colors = $colors ?? [
                    ['name' => 'main_color',        'label' => __('adminlte::adminlte.main_color')],
                    ['name' => 'sub_color',         'label' => __('adminlte::adminlte.sub_color')],
                    ['name' => 'text_color',        'label' => __('adminlte::adminlte.text_color')],
                    ['name' => 'button_color',      'label' => __('adminlte::adminlte.button_color')],
                    ['name' => 'button_text_color', 'label' => __('adminlte::adminlte.button_text_color')],
                    ['name' => 'icon_color',        'label' => __('adminlte::adminlte.icon_color')],
                    ['name' => 'text_filed_color',  'label' => __('adminlte::adminlte.text_field_color')],
                    ['name' => 'card_color',        'label' => __('adminlte::adminlte.card_color')],
                    ['name' => 'label_color',       'label' => __('adminlte::adminlte.label_color')],
                    ['name' => 'hint_color',        'label' => __('adminlte::adminlte.hint_color')],
                ];
            @endphp

            <div class="row">
                @foreach($colors as $c)
                    <div class="col-sm-6 col-md-4 mb-3">
                        <x-adminlte-input
                            name="{{ $c['name'] }}"
                            label="{{ $c['label'] }}"
                            type="color"
                            igroup-size="sm"
                            value="{{ old($c['name'], data_get($company, $c['name']) ?? '#ffffff') }}" />
                    </div>
                @endforeach
            </div>

            {{-- ══ SINGLE BOTTOM SAVE BUTTON ══ --}}
            <div class="row mt-2 mb-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-success btn-lg btn-block">
                        <i class="fas fa-save mr-2"></i>
                        {{ __('adminlte::adminlte.save_information') }}
                    </button>
                </div>
            </div>

        </form>
    </x-adminlte-card>
</div>
@endsection

{{-- Broadcast listener anchor --}}
<div id="company-info-form-listener"
     data-channel="company-info"
     data-events='@json(["company_info_updated"])'
     data-current-id="{{ $company?->id ?? '' }}"
     style="display:none;">
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Field setter ────────────────────────────────────
    function setField(name, value) {
        if (value === undefined || value === null) return;
        var el = document.querySelector('[name="' + name + '"]');
        if (!el) return;
        el.value = value;
        el.dispatchEvent(new Event('input',  { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // ── 2. Payload handler ─────────────────────────────────
    function applyPayload(payload) {
        var data = (payload && payload.company) ? payload.company : (payload || {});
        console.log('[company_info] broadcast payload:', data);

        [
            'name_en','name_ar','email','phone',
            'address_en','address_ar','location',
            'about_us_en','about_us_ar',
            'mission_en','mission_ar',
            'vision_en','vision_ar',
            'pusher_app_id','pusher_key','pusher_cluster',
        ].forEach(function (f) { setField(f, data[f]); });

        [
            'main_color','sub_color','text_color','button_color',
            'button_text_color','icon_color','text_filed_color',
            'card_color','label_color','hint_color',
        ].forEach(function (f) {
            setField(f, data[f]);
            if (window.AppBrand && data[f]) {
                var cssVar = window.AppBrand.map[f];
                if (cssVar) document.documentElement.style.setProperty(cssVar, data[f]);
            }
        });

        var src = data.image_url || data.image;
        if (src) {
            var img = document.querySelector('#logo-preview, [data-role="logo-preview"]');
            if (img) img.src = src;
        }

        if (window.bsCustomFileInput) bsCustomFileInput.init();

        if (window.toastr) {
            toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }
    }

    // ── 3. Register with AppBroadcast ──────────────────────
    var CHANNEL = 'company-info';
    var EVENT   = 'company_info_updated';

    if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
        window.AppBroadcast.subscribe(CHANNEL, EVENT, applyPayload);
        console.info('[company_info] subscribed via AppBroadcast →', CHANNEL, '/', EVENT);
    } else {
        window.__pageBroadcasts = window.__pageBroadcasts || [];
        window.__pageBroadcasts.push({
            channel: CHANNEL,
            event:   EVENT,
            handler: applyPayload,
        });
        console.info('[company_info] queued in __pageBroadcasts →', CHANNEL, '/', EVENT);
    }

});
</script>
@endpush