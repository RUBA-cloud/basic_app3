@extends('adminlte::page')
@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.regions'))

@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
            {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.regions') }}
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

        <form method="POST" action="{{ route('regions.update', $region) }}">
            @csrf
            @method('PUT')

            {{-- Country EN --}}
            <x-form.textarea
                id="country_en"
                name="country_en"
                label="{{ __('adminlte::adminlte.country') }} EN"
                :value="old('country_en', $region->country_en)"
            />

            {{-- Country AR --}}
            <x-form.textarea
                id="country_ar"
                name="country_ar"
                label="{{ __('adminlte::adminlte.country') }} AR"
                dir="rtl"
                :value="old('country_ar', $region->country_ar)"
            />

            {{-- City EN --}}
            <x-form.textarea
                id="city_en"
                name="city_en"
                label="{{ __('adminlte::adminlte.city') }} EN"
                :value="old('city_en', $region->city_en)"
            />

            {{-- City AR --}}
            <x-form.textarea
                id="city_ar"
                name="city_ar"
                label="{{ __('adminlte::adminlte.city') }} AR"
                dir="rtl"
                :value="old('city_ar', $region->city_ar)"
            />

            {{-- Expected Day Count (number) --}}
            <div class="form-group">
                <label for="excepted_day_count">{{ __('adminlte::adminlte.excepted_delivery_days') }}</label>
                <input
                    id="excepted_day_count"
                    type="number"
                    min="0"
                    class="form-control @error('excepted_day_count') is-invalid @enderror"
                    name="excepted_day_count"
                    value="{{ old('excepted_day_count', $region->excepted_day_count) }}"
                >
                @error('excepted_day_count')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Is Active Checkbox (with hidden default 0) --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="hidden" name="is_active" value="0">
                <label>
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', (bool) $region->is_active) ? 'checked' : '' }}>
                    {{ __('adminlte::adminlte.is_active') }}
                </label>
            </div>

            <div class="d-flex gap-2">
                <x-adminlte-button
                    label="{{ __('adminlte::adminlte.save_information') }}"
                    type="submit"
                    theme="success"
                    class="mr-2"
                    icon="fas fa-save"
                />
                <a href="{{ route('regions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
