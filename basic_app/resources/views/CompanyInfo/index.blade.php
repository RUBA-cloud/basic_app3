@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_info'))

@section('content')
<div class="container-fluid py-4">
    <x-adminlte-card class="header_card"
        title="{{ __('adminlte::adminlte.company_info') }}"
        icon="fas fa-building" collapsible maximizable>

       <div class="d-flex flex-wrap justify-content-end align-items-center mt-4">
        <a href="{{ route('company_history') }}" class="btn btn-outline-primary">
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
                name="image" id="logo" />

            {{-- Company Information Fields --}}
            <x-adminlte-textarea name="name_en"
                label="{{ __('adminlte::adminlte.company_name_en') }}" rows=2 igroup-size="sm"
                placeholder="{{ __('adminlte::adminlte.company_name_en_placeholder') }}">
                {{ old('name_en', $company->name_en ?? '') }}
            </x-adminlte-textarea>

            <x-adminlte-textarea name="name_ar"
                label="{{ __('adminlte::adminlte.company_name_ar') }}" rows=2 dir="rtl" igroup-size="sm"
                placeholder="{{ __('adminlte::adminlte.company_name_ar_placeholder') }}">
                {{ old('name_ar', $company->name_ar ?? '') }}
            </x-adminlte-textarea>

            <x-adminlte-input name="email"
                label="{{ __('adminlte::adminlte.company_email') }}"
                type="email" igroup-size="sm"
                placeholder="{{ __('adminlte::adminlte.company_email_placeholder') }}"
                value="{{ old('email', $company->email ?? '') }}"/>

            <x-adminlte-input name="phone"
                label="{{ __('adminlte::adminlte.company_phone') }}"
                type="text" igroup-size="sm"
                placeholder="{{ __('adminlte::adminlte.company_phone_placeholder') }}"
                value="{{ old('phone', $company->phone ?? '') }}"/>

            <x-adminlte-textarea name="address_en"
                label="{{ __('adminlte::adminlte.company_address_en') }}" rows=2 igroup-size="sm">
                {{ old('address_en', $company->address_en ?? '') }}
            </x-adminlte-textarea>

            <x-adminlte-textarea name="address_ar"
                label="{{ __('adminlte::adminlte.company_address_ar') }}" rows=2 dir="rtl" igroup-size="sm">
                {{ old('address_ar', $company->address_ar ?? '') }}
            </x-adminlte-textarea>

            <x-adminlte-input name="location"
                label="{{ __('adminlte::adminlte.company_location') }}"
                type="text" igroup-size="sm"
                placeholder="{{ __('adminlte::adminlte.company_location_placeholder') }}"
                value="{{ old('location', $company->location ?? '') }}"/>

            <x-adminlte-textarea name="about_us_en"
                label="{{ __('adminlte::adminlte.about_us_en') }}" rows=3 igroup-size="sm">
                {{ old('about_us_en', $company->about_us_en ?? '') }}
            </x-adminlte-textarea>

            <x-adminlte-textarea name="about_us_ar"
                label="{{ __('adminlte::adminlte.about_us_ar') }}" rows=3 dir="rtl" igroup-size="sm">
                {{ old('about_us_ar', $company->about_us_ar ?? '') }}
            </x-adminlte-textarea>

            <x-adminlte-textarea name="mission_en"
                label="{{ __('adminlte::adminlte.mission_en') }}" rows=2 igroup-size="sm">
                {{ old('mission_en', $company->mission_en ?? '') }}
            </x-adminlte-textarea>

            <x-adminlte-textarea name="mission_ar"
                label="{{ __('adminlte::adminlte.mission_ar') }}" rows=2 dir="rtl" igroup-size="sm">
                {{ old('mission_ar', $company->mission_ar ?? '') }}
            </x-adminlte-textarea>

            <x-adminlte-textarea name="vision_en"
                label="{{ __('adminlte::adminlte.vision_en') }}" rows=2 igroup-size="sm">
                {{ old('vision_en', $company->vision_en ?? '') }}
            </x-adminlte-textarea>

            <x-adminlte-textarea name="vision_ar"
                label="{{ __('adminlte::adminlte.vision_ar') }}" rows=2 dir="rtl" igroup-size="sm">
                {{ old('vision_ar', $company->vision_ar ?? '') }}
            </x-adminlte-textarea>

            {{-- Color selections --}}
            <div class="row">
                @php
                    $colors = [
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
                class="full-width-btn" icon="fas fa-save"/>
        </form>
    </x-adminlte-card>
</div>
@endsection
