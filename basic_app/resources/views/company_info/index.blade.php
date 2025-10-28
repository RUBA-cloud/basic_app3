@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_info'))

@section('content')
<div class="container-fluid py-4">
    <x-adminlte-card class="header_card"
                     title="{{ __('adminlte::adminlte.company_info') }}"
                     icon="fas fa-building"
                     collapsible maximizable>

        <div class="d-flex flex-wrap justify-content-end align-items-center mt-4">
            <a href="{{ route('companyInfo.history') }}" class="btn btn-outline-primary">
                <i class="fas fa-history me-2"></i>
                {{ __('adminlte::adminlte.view_history') }}
            </a>
        </div>

        <form method="POST" action="{{ route('companyInfo.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Company Logo --}}
            <x-upload-image
                :image="$company->image ?? null"
                label="{{ __('adminlte::adminlte.choose_file') }}"
                name="image"
                id="logo" />

            {{-- Company Information Fields --}}
            <x-adminlte-input name="name_en"
                label="{{ __('adminlte::adminlte.company_name_en') }}"
                placeholder="{{ __('adminlte::adminlte.company_name_en_placeholder') }}"
                value="{{ old('name_en', $company->name_en ?? '') }}" />

            <x-adminlte-input name="name_ar"
                label="{{ __('adminlte::adminlte.company_name_ar') }}"
                placeholder="{{ __('adminlte::adminlte.company_name_ar_placeholder') }}"
                value="{{ old('name_ar', $company->name_ar ?? '') }}" />

            <x-adminlte-input name="email"
                label="{{ __('adminlte::adminlte.company_email') }}"
                type="email"
                placeholder="{{ __('adminlte::adminlte.company_email_placeholder') }}"
                value="{{ old('email', $company->email ?? '') }}" />

            <x-adminlte-input name="phone"
                label="{{ __('adminlte::adminlte.company_phone') }}"
                type="text"
                placeholder="{{ __('adminlte::adminlte.company_phone_placeholder') }}"
                value="{{ old('phone', $company->phone ?? '') }}" />

            <x-adminlte-textarea name="address_en"
                label="{{ __('adminlte::adminlte.company_address_en') }}"
                rows="2">{{ old('address_en', $company->address_en ?? '') }}</x-adminlte-textarea>

            <x-adminlte-textarea name="address_ar"
                label="{{ __('adminlte::adminlte.company_address_ar') }}"
                rows="2" dir="rtl">{{ old('address_ar', $company->address_ar ?? '') }}</x-adminlte-textarea>

            <x-adminlte-input name="location"
                label="{{ __('adminlte::adminlte.company_location') }}"
                placeholder="{{ __('adminlte::adminlte.company_location_placeholder') }}"
                value="{{ old('location', $company->location ?? '') }}" />

            <x-adminlte-textarea name="about_us_en"
                label="{{ __('adminlte::adminlte.about_us_en') }}"
                rows="3">{{ old('about_us_en', $company->about_us_en ?? '') }}</x-adminlte-textarea>

            <x-adminlte-textarea name="about_us_ar"
                label="{{ __('adminlte::adminlte.about_us_ar') }}"
                rows="3" dir="rtl">{{ old('about_us_ar', $company->about_us_ar ?? '') }}</x-adminlte-textarea>

            <x-adminlte-textarea name="mission_en"
                label="{{ __('adminlte::adminlte.mission_en') }}"
                rows="2">{{ old('mission_en', $company->mission_en ?? '') }}</x-adminlte-textarea>

            <x-adminlte-textarea name="mission_ar"
                label="{{ __('adminlte::adminlte.mission_ar') }}"
                rows="2" dir="rtl">{{ old('mission_ar', $company->mission_ar ?? '') }}</x-adminlte-textarea>

            <x-adminlte-textarea name="vision_en"
                label="{{ __('adminlte::adminlte.vision_en') }}"
                rows="2">{{ old('vision_en', $company->vision_en ?? '') }}</x-adminlte-textarea>

            <x-adminlte-textarea name="vision_ar"
                label="{{ __('adminlte::adminlte.vision_ar') }}"
                rows="2" dir="rtl">{{ old('vision_ar', $company->vision_ar ?? '') }}</x-adminlte-textarea>

            {{-- Colors --}}
            @php
              $colors = $colors ?? [
                ['name' => 'main_color', 'label' => __('adminlte::adminlte.main_color')],
                ['name' => 'sub_color', 'label' => __('adminlte::adminlte.sub_color')],
                ['name' => 'text_color', 'label' => __('adminlte::adminlte.text_color')],
                ['name' => 'button_color', 'label' => __('adminlte::adminlte.button_color')],
                ['name' => 'button_text_color', 'label' => __('adminlte::adminlte.button_text_color')],
                ['name' => 'icon_color', 'label' => __('adminlte::adminlte.icon_color')],
                ['name' => 'text_field_color', 'label' => __('adminlte::adminlte.text_field_color')],
                ['name' => 'card_color', 'label' => __('adminlte::adminlte.card_color')],
                ['name' => 'label_color', 'label' => __('adminlte::adminlte.label_color')],
                ['name' => 'hint_color', 'label' => __('adminlte::adminlte.hint_color')],
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
                      value="{{ old($c['name'], data_get($company, $c['name']) ?? '#ffffff') }}"/>
                </div>
              @endforeach
            </div>

            <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit" theme="success"
                class="full-width-btn mt-3" icon="fas fa-save" />
        </form>
    </x-adminlte-card>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ---------- helpers ----------
  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${name}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input',  { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };
  const setColorsIfAny = (obj) => {
    [
      'main_color','sub_color','text_color','button_color',
      'button_text_color','icon_color','text_field_color',
      'card_color','label_color','hint_color'
    ].forEach(n => setField(n, obj?.[n]));
  };
  const applyPayload = (payload) => {
    // Accept both {company: {...}} and flat fields
    const data = payload?.company ?? payload ?? {};
    setField('name_en',     data.name_en);
    setField('name_ar',     data.name_ar);
    setField('email',       data.email);
    setField('phone',       data.phone);
    setField('address_en',  data.address_en);
    setField('address_ar',  data.address_ar);
    setField('location',    data.location);
    setField('about_us_en', data.about_us_en);
    setField('about_us_ar', data.about_us_ar);
    setField('mission_en',  data.mission_en);
    setField('mission_ar',  data.mission_ar);
    setField('vision_en',   data.vision_en);
    setField('vision_ar',   data.vision_ar);
    setColorsIfAny(data);

    // optional: update logo preview if your component exposes an <img>
    const src = data.image_url || data.logo_url;
    if (src) {
      const img = document.querySelector('#logo-preview, [data-role="logo-preview"]');
      if (img) img.src = src;
    }

    if (window.bsCustomFileInput && document.querySelector('input[type="file"][name="image"]')) {
      bsCustomFileInput.init();
    }
    if (window.toastr) toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
    console.log('[company_info] patched company form from payload', data);
  };

  // ---------- Pusher ONLY: channel=order_status, event=order_status_updated ----------
  (function initPusher() {
    const script = document.createElement('script');
    script.src = 'https://js.pusher.com/8.4/pusher.min.js';
    script.onload = () => {
      // Replace with your real key/cluster in .env
      const pusher = new Pusher('{{ env('PUSHER_APP_KEY', 'b6ecb13acb55900e518a') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER', 'ap2') }}',
        forceTLS: true,
      });

      // Public channel named "order_status"
      const channel = pusher.subscribe('company_info');

      // Listen for "order_status_updated" and apply to company form
      channel.bind('company_info_updated', applyPayload);

      console.log('[company_info_updated] listening via Pusher → event: order_status_updated');
    };
    document.body.appendChild(script);
  })();

  // If you later switch to private-per-order channels, example:
  // const orderId = document.querySelector('[name="order_id"]')?.value;
  // const channel = pusher.subscribe('private-order_status.' + orderId);
  // channel.bind('order_status_updated', applyPayload);
});
</script>
@endpush
