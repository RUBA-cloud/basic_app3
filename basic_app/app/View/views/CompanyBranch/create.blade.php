


 @extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_info'))

@section('content')
<div class="container-fluid py-4">
    <x-adminlte-card class="header_card"
        title="{{ __('adminlte::adminlte.company_info') }}"
        icon="fas fa-building" collapsible maximizable>

       <div class="d-flex flex-wrap justify-content-end align-items-center mt-4">
        <a href="{{ route('company_histroy') }}" class="btn btn-outline-primary">
            <i class="fas fa-history me-2"></i>
            {{ __('adminlte::adminlte.history') }}
        </a>
    </div>

        <form method="POST" action="{{ route('companyBranch.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Company Logo --}}
            <x-upload-image
                :image="$company->image ?? null"
                label="{{ __('adminlte::adminlte.choose_file') }}"
                name="image" id="logo" />
                 <x-form.textarea id="name_en" name="name_en" label="  label="{{ __('adminlte::adminlte.branch_name_en') }}" :value="old('name_en')" />
       {{-- Branch Name Arabic --}}
            <x-form.textarea id="name_ar" name="name_ar" label="{{__('adminlte::adminlte.branch_name_ar') }}" dir="rtl" :value="old('name_ar')" />
            {{-- Branch Phone --}}
            <x-form.textarea id="phone" name="phone" label="{{ __('adminlte::adminlte.branch_phone') }}" :value="old('  phone')" rows="1" />
            {{-- Branch Email --}}
            <x-form.textarea id="email" name="email" label="{{__('adminlte::adminlte.branch_phone')}}":value="old('email')" rows="1" />
            {{-- Branch Address English --}}
            <x-form.textarea id="address_en" name="address_en" label="{{ __('adminlte::adminlte.branch_address_en') }}":value="old('address_en')" />
            {{-- Branch Address Arabic --}}
            <x-form.textarea id="address_ar" name="address_ar" label="{{ __('adminlte::adminlte.branch_address_ar') }}" dir="rtl" :value="old('address_ar')" />
            {{-- Branch Working Days --}}
            {{-- Branch Fax --}}
            <x-form.textarea id="fax" name=" fax" label="{{ __('adminlite::adminlite.branch_fax') }}" :value="old('fax')" rows="1" />
            {{-- Branch Location --}}
            <x-form.textarea id="location" name="location" label="{{ __('adminlite::adminlite.location') }}":value="old('location')" rows="1" />

            <x-working-days-hours
                :working_days="old('working_days', $company->working_days ?? [])"
                :working_hours="old('working_hours', $company->working_hours ?? [])"
                label="Working Days and Hours"
            />
            {{-- Submit Button --}}
            <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit" theme="success"
                class="full-width_btn" icon="fas fa-save"/>

        </form>
        </x-adminlte-card>
</div>
@endsection
