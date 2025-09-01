@extends('adminlte::page')
@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.additional'))
@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
        {{ __('adminlte::adminlte.create') }} {{ __('adminlte::adminlte.additional') }}

        </h2>

        <form method="POST" action="{{ route('additional.store') }}" enctype="multipart/form-data">
            @csrf
    {{-- Category Image --}}
            <x-upload-image
                :image="old('image')"
                label="{{ __('adminlte::adminlte.image') }}"
                name="image"
                id="image"
            />

            {{-- Size Name English --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="{{__('adminlte::adminlte.name_en')}}"
                :value="old('name_en')"/>



            {{-- Size Name Arabic --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="{{__('adminlte::adminlte.name_ar')}}"
                dir="rtl"
                :value="old('name_ar')"
            />

    <x-form.textarea     id="price"      name="price" label="{{__('adminlte::adminlte.price')}}"    :value="old('price')"       dir="rtl"     />

  <x-form.textarea
                id="descripation"
                name="description"
                label="{{__('adminlte::adminlte.descripation')}}"
                :value="old('descripation')"/>

            {{-- Is Active Checkbox --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}/> {{__('adminlte::adminlte.is_active')}}
            </div>

             <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit"
                theme="success"
                class="w-100"
                icon="fas fa-save"
            />
    </div>
</div>
@endsection
