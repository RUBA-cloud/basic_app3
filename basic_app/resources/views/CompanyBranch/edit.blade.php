@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.branches'))

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="flex: 1; padding: 2rem;">
        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">{{ __('adminlte::adminlte.edit') }} {{ __('adminlte::menu.branches') }}</h2>

        <form method="POST" action="{{ route('companyBranch.update', $branch->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Important for update routes --}}

            {{-- Branch Image --}}
            <x-upload-image
                :image="$branch->image"
                label="{{ __('adminlte::adminlte.image') }}"
                name="image"
                id="image"
            />

            {{-- Branch Name English --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="{{ __('adminlte::adminlte.branch_name_en')}}"
                :value="old('name_en', $branch->name_en)"
            />

            {{-- Branch Name Arabic --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="{{ __('adminlte::adminlte.branch_name_ar')}}"
                dir="rtl"
                :value="old('name_ar', $branch->name_ar)"
            />

            {{-- Branch Phone --}}
            <x-form.textarea
                id="phone"
                name="phone"
                label="{{ __('adminlte::adminlte.phone')}}"
                :value="old('phone', $branch->phone)"
                rows="1"
            />

            {{-- Branch Email --}}
            <x-form.textarea
                id="email"
                name="email"
                 label="{{ __('adminlte::adminlte.email')}}"
                :value="old('email', $branch->email)"
                rows="1"
            />

            {{-- Branch Address English --}}
            <x-form.textarea
                id="address_en"
                name="address_en"
            label="{{ __('adminlte::adminlte.branch_address_en')}}"
                :value="old('address_en', $branch->address_en)"
            />

            {{-- Branch Address Arabic --}}
            <x-form.textarea
                id="address_ar"
                name="address_ar"
            label="{{ __('adminlte::adminlte.branch_address_ar')}}"
                dir="rtl"
                :value="old('address_ar', $branch->address_ar)"
            />

            {{-- Branch Fax --}}
            <x-form.textarea
                id="fax"
                name="fax"
            label="{{ __('adminlte::adminlte.fax')}}"
                :value="old('fax', $branch->fax)"
                rows="1"
            />

            {{-- Branch Location --}}
            <x-form.textarea
                id="location"
                name="location"
            label="{{ __('adminlte::adminlte.location')}}"
                :value="old('location', $branch->location)"
                rows="1"
            />

            {{-- Working Days/Hours --}}
            <x-working-days-hours :branch="$branch" />
<input type="checkbox" name="is_active" value="1" {{ old('is_active', $branch?->is_active) ? 'checked' : '' }}/> {{ __('adminlte::adminlte.active')}}           {{-- Submit Button --}}
        <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit"
                theme="success"
                class="w-100"
                icon="fas fa-save"
            />
        </form>
    </div>
</div>
@endsection
