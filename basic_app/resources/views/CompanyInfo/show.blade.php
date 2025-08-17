@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_info'))

@section('content')
@php
    $labels = [
        'name_en' =>  __('adminlte::adminlte.company_name_en'),
        'name_ar' =>  __('adminlte::adminlte.company_name_ar'),
        'email' => __('adminlte::adminlte.email'),
        'phone' => __('adminlte::adminlte.phone'),
        'address_en' => __('adminlte::adminlte.company_address_en'),
        'address_ar' => __('adminlte::adminlte.company_address_ar'),
        'location' => __('adminlte::adminlte.location'),
        'about_us_en' => __('adminlte::adminlte.about_us_en'),
        'about_us_ar' => __('adminlte::adminlte.about_us_ar'),
        'mission_en' => __('adminlte::adminlte.mission_en'),
        'mission_ar' => __('adminlte::adminlte.mission_ar'),
        'vision_en' => __('adminlte::adminlte.vision_en'),
        'vision_ar' => __('adminlte::adminlte.vision_ar'),
    ];

    $brandLabels = [
        'main_color' => __('adminlte::adminlte.main_color'),
        'sub_color' => __('adminlte::adminlte.sub_color'),
        'text_color' => __('adminlte::adminlte.text_color'),
        'button_color' => __('adminlte::adminlte.button_color'),
        'button_text_color' => __('adminlte::adminlte.button_text_color'),
        'icon_color' => __('adminlte::adminlte.icon_color'),
        'text_field_color' => __('adminlte::adminlte.text_field_color'),
        'card_color' => __('adminlte::adminlte.card_color'),
        'label_color' => __('adminlte::adminlte.label_color'),
        'hint_color' => __('adminlte::adminlte.hint_color'),
    ];

    $isRtl = app()->getLocale() === 'ar';
@endphp

<div class="container-fluid py-4" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    {{-- Main Card --}}
    <x-adminlte-card title="{{ __('adminlte::adminlte.company_info') }}" theme="primary" icon="fas fa-building" collapsible>

        {{-- Basic Info --}}
        <div class="mb-4">
            <h4> {{ __('adminlte::adminlte.basic_info')}} </h4>
        </div>

        <div class="row">
            {{-- Left Column --}}
            <div class="col-md-6">
    <x-adminlte-card title="{{ __('adminlte::adminlte.company_info') }}" theme="primary" icon="fas fa-building" collapsible>
                    <dl class="row">
                        <dt class="col-sm-4">{{ $labels['name_en'] }}</dt>
                        <dd class="col-sm-8">{{ $company->name_en }}</dd>

                        <dt class="col-sm-4">{{ $labels['name_ar'] }}</dt>
                        <dd class="col-sm-8 text-{{ $isRtl ? 'end' : 'start' }}">{{ $company->name_ar }}</dd>

                        <dt class="col-sm-4">{{ $labels['email'] }}</dt>
                        <dd class="col-sm-8">{{ $company->email }}</dd>

                        <dt class="col-sm-4">{{ $labels['phone'] }}</dt>
                        <dd class="col-sm-8">{{ $company->phone }}</dd>
                    </dl>
                </x-adminlte-card>
            </div>

            {{-- Right Column --}}
            <div class="col-md-6">
                <x-adminlte-card title="{{ __('adminlte::adminlte.location') }}" theme="teal" icon="fas fa-map-marker-alt" collapsible>
                    <dl class="row">
                        <dt class="col-sm-4">{{ $labels['address_en'] }}</dt>
                        <dd class="col-sm-8">{{ $company->address_en }}</dd>

                        <dt class="col-sm-4">{{ $labels['address_ar'] }}</dt>
                        <dd class="col-sm-8 text-{{ $isRtl ? 'end' : 'start' }}">{{ $company->address_ar }}</dd>

                        <dt class="col-sm-4">{{ $labels['location'] }}</dt>
                        <dd class="col-sm-8">{{ $company->location }}</dd>
                    </dl>
                </x-adminlte-card>
            </div>
        </div>

        {{-- Company Overview --}}
        <div class="col-12 mt-3">
            <x-adminlte-card title="{{ __('adminlte::adminlte.company_overview') }}" theme="purple" icon="fas fa-eye" collapsible>
                <dl class="row">
                    @foreach(['about_us', 'mission', 'vision'] as $field)
                        <dt class="col-sm-3">{{ $labels[$field.'_en'] }}</dt>
                        <dd class="col-sm-9">{{ $company->{$field.'_en'} }}</dd>

                        <dt class="col-sm-3">{{ $labels[$field.'_ar'] }}</dt>
                        <dd class="col-sm-9 text-{{ $isRtl ? 'end' : 'start' }}">{{ $company->{$field.'_ar'} }}</dd>
                    @endforeach
                </dl>
            </x-adminlte-card>
        </div>

        {{-- Brand Colors --}}
        <div class="col-12 mt-3">
            <x-adminlte-card title="{{ __('adminlte::adminlte.brand_colors') }}" theme="cyan" icon="fas fa-palette" collapsible>
                <div class="row text-center">
                    @foreach ($brandLabels as $key => $label)
                        <div class="col-md-3 col-sm-4 col-6 mb-3">
                            <strong>{{ $label }}</strong>
                            <div style="width: 100%; height: 40px; border-radius: 8px; background-color: {{ $company->$key ?? '#F0F0F0' }};"></div>
                            <small class="d-block mt-1 text-muted">{{ $company->$key ?? '--' }}</small>
                        </div>
                    @endforeach
                </div>
            </x-adminlte-card>
        </div>

    </x-adminlte-card>
</div>
@endsection
