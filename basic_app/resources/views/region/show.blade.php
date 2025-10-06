@extends('adminlte::page')

@section('title', __('adminlte::adminlte.region'))

@section('content')
<div class="container py-4">
{{-- Header --}}
 <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-code-branch me-2 text-primary"></i>
            @if (app()->getLocale() === 'ar') {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.regions') }}@else{{ __('adminlte::adminlte.regions') }} {{ __('adminlte::adminlte.details') }}@endif
        </h2>
    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">



            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">
                     {{-- Name in English --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.country') }} EN</small>
                        <div class="fs-5 fw-bold text-dark">{{ $region->country_en }}</div>
                    </div>
                    {{-- Name in Arabic --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }} AR</small>
                        <div class="fs-5 fw-bold text-dark">{{ $region->country_ar }}</div>
                    </div>
                     <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.city') }} EN</small>
                        <div class="fs-5 fw-bold text-dark">{{ $region->city_en}}</div>
                    </div>
                    {{-- Name in Arabic --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.city') }} AR</small>
                        <div class="fs-5 fw-bold text-dark">{{ $region->city_ar}}</div>
                    </div>
   {{-- Excepted count --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.excepted_delivery_days') }} /small>
                        <div class="fs-5 fw-bold text-dark">{{ $region->excepted_day_count}}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($region->is_active)
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            </span>
                        @endif
                    </div>



                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('regions.edit', $region->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('regions.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>
@endsection
