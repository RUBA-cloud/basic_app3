

 @extends('adminlte::page')
@section('title', _('adminlte::adminlte.company_branch'))
@section('content')
<div class="container-fluid py-4" style="margin: 10px">
    <x-adminlte-card class="header_card"
        title="{{ __('adminlte::adminlte.company_branch') }}"
        icon="fas fa-building" collapsible maximizable>
    </div>
        <form method="POST" action="{{ route('companyBranch.store') }}" enctype="multipart/form-data" style="margin: 10px">
            @csrf
            {{-- Company Logo --}}
            <x-upload-image
                :image="$company->image ?? null"
                label="{{ __('adminlte::adminlte.choose_file') }}"
                name="image" id="logo" />
                 <x-form.textarea id="name_en" name="name_en" label="{{ __('adminlte::adminlte.branch_name_en') }}" :value="old('name_en')" />
       {{-- Branch Name Arabic --}}
            <x-form.textarea id="name_ar" name="name_ar" label="{{__('adminlte::adminlte.branch_name_ar') }}" dir="rtl" :value="old('name_ar')" />
            {{-- Branch Phone --}}
            <x-form.textarea id="phone" name="phone" label="{{ __('adminlte::adminlte.branch_phone') }}" :value="old('  phone')" rows="1" />

            {{-- Branch Fax --}}
               <x-form.textarea id="email" name="email" label="{{__('adminlte::adminlte.company_email') }}" dir="rtl" :value="old('email')" />
            {{-- Branch Phone --}}
            <x-form.textarea id="address_en" name="address_en" label="{{ __('adminlte::adminlte.company_address_en') }}" :value="old('address_en')" rows="1" />

          <x-form.textarea id="address_ar" name="address_ar" label="{{ __('adminlte::adminlte.company_address_ar') }}" :value="old('address_ar')" rows="1" />

            {{-- Branch Fax --}}
            <x-form.textarea id="fax" name=" fax" label="{{ __('adminlte::adminlte.fax') }}" :value="old('fax')" rows="1" />


            {{-- Branch Location --}}
            <x-form.textarea id="location" name="location" label="{{ __('adminlte::adminlte.location') }}" :value="old('location')" rows="1" />


            <x-working-days-hours
                :working_days="old('working_days', $company->working_days ?? [])"
                :working_hours="old('working_hours', $company->working_hours ?? [])"
                label="Working Days and Hours"
            />
<input type="checkbox" name="is_active" value="1" {{ old('is_active')? 'checked' : '' }}/> {{ __('adminlte::adminlte.active')}}
{{-- Submit Button --}}

  <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit" theme="success"
                class="full-width-btn" icon="fas fa-save"/>
        </form>
        </x-adminlte-card>
</div>
@endsection
