@extends('adminlte::page')
@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.type'))
@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">
        <h2 style="font-type: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
                    {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.type') }}
        </h2>

        <form method="POST" action="{{ route('type.update',$type->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Type Name English --}}
            <x-form.textarea  id="name_en"    name="name_en" label="{{__('adminlte::adminlte.name_en')}}"     :value="$type->name_en"  />

            {{-- Type Name Arabic --}}
            <x-form.textarea id="name_ar" name="name_ar" label="{{__('adminlte::adminlte.name_ar')}}" dir="rtl"  :value="$type->name_ar"/>



            {{-- Is Active Checkbox --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="checkbox" name="is_active" value="1" {{ $type->is_active ? 'checked' : '' }}/> {{__('adminlte::adminlte.is_active')}}
            </div>

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
