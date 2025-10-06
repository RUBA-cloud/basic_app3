@extends('adminlte::page')
@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.company_delivery'))

@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 24px;">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin:0;">
                {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.company_delivery') }}
            </h2>

            <a href="{{ route('company_delivery.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('adminlte::adminlte.back') }}
            </a>
        </div>

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

        <form method="POST" action="{{ route('company_delivery.update', $companyDelivery->id) }}">
            @csrf
            @method('PUT')

            {{-- Name EN --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="{{ __('adminlte::adminlte.name_en') }}"
                :value="old('name_en', $companyDelivery->name_en)"
            />

            {{-- Name AR --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="{{ __('adminlte::adminlte.name_ar') }} AR"
                dir="rtl"
                :value="old('name_ar', $companyDelivery->name_ar)"
            />

            {{-- Is Active Checkbox (with hidden default 0) --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="hidden" name="is_active" value="0">
                <label>
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', (int) $companyDelivery->is_active) ? 'checked' : '' }}
                    >
                    {{ __('adminlte::adminlte.is_active') }}
                </label>
            </div>

            <div class="d-grid gap-2">
                <x-adminlte-button
                    label="{{ __('adminlte::adminlte.save_changes') }}"
                    type="submit"
                    theme="success"
                    class="w-100"
                    icon="fas fa-save"
                />

                <a href="{{ route('company_delivery.index') }}" class="btn btn-secondary w-100">
                    <i class="fas fa-times"></i> {{ __('adminlte::adminlte.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
