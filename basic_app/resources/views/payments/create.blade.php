@extends('adminlte::page')
@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.payement'))

@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
            {{ __('adminlte::adminlte.create') }} {{ __('adminlte::adminlte.payment') }}
        </h2>

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('payment.store') }}">
            @csrf

            {{-- Country EN --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="{{ __('adminlte::adminlte.name_en') }}"
                :value="old('name_en')"
            />

            {{-- Country AR --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="{{ __('adminlte::adminlte.name_ar') }} AR"
                dir="rtl"
                :value="old('name_ar')"
            />

            {{-- Is Active Checkbox (with hidden default 0) --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="hidden" name="is_active" value="0">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    {{ __('adminlte::adminlte.is_active') }}
                </label>
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
