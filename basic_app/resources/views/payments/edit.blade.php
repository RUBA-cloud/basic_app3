@extends('adminlte::page')
@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.payment'))

@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
            {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.payment') }}
        </h2>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('payment.update', $payment) }}">
            @csrf
            @method('PUT')

            {{-- Name EN --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="{{ __('adminlte::adminlte.name_en') }}"
                :value="old('name_en', $payment->name_en)"
            />

            {{-- Name AR --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="{{ __('adminlte::adminlte.name_ar') }}"
                dir="rtl"
                :value="old('name_ar', $payment->name_ar)"
            />

            {{-- Is Active (with hidden default 0) --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="hidden" name="is_active" value="0">
                <label>
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', (bool) $payment->is_active) ? 'checked' : '' }}>
                    {{ __('adminlte::adminlte.is_active') }}
                </label>
            </div>

            <div class="d-flex">
                <x-adminlte-button
                    label="{{ __('adminlte::adminlte.save_information') }}"
                    type="submit"
                    theme="success"
                    class="mr-2"
                    icon="fas fa-save"
                />
                <a href="{{ route('payment.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
