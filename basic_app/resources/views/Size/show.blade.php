@extends('adminlte::page')

@section('title', __('adminlte::adminlte.size'))

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
@endphp

<style>
  /* logical spacing works for LTR & RTL */
  .mie-1 { margin-inline-end: .25rem; }
  .mie-2 { margin-inline-end: .5rem; }
  .mis-2 { margin-inline-start: .5rem; }
</style>

<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 fw-bold text-dark">
            @if ($isAr)
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.size') }}
            @else
                {{ __('adminlte::adminlte.size') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>
    </div>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">

            {{-- Image --}}
            <div class="col-lg-4 col-md-5">
                <div class="border rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center p-2 h-100">
                    <img
                        src="{{ $size->image ? asset($size->image) : 'https://placehold.co/500x300?text=Size+Image' }}"
                        alt="{{ __('adminlte::adminlte.size') }} {{ __('adminlte::adminlte.image') }}"
                        class="img-fluid rounded-3"
                        style="max-height: 280px; object-fit: cover;"
                    >
                </div>
            </div>

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Name EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $size->name_en ?? '—' }}</div>
                    </div>

                    {{-- Name AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $size->name_ar ?? '—' }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($size->is_active)
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle mie-1"></i>{{ __('adminlte::adminlte.active') }}
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle mie-1"></i>{{ __('adminlte::adminlte.inactive') }}
                            </span>
                        @endif
                    </div>

                    {{-- Price --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.price') }}</small>
                        <div class="fs-5 fw-bold text-dark">
                            {{ number_format((float) $size->price, 2) }} JD
                        </div>
                    </div>

                    {{-- Description (typo fixed: use the same field name everywhere) --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.descripation') }}</small>
                        <div class="fs-5 fw-bold text-dark">
                            {{ $size->descripation ?? '—' }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('sizes.edit', $size->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit mie-2"></i>{{ __('adminlte::adminlte.edit') }}
                        </a>

                        <a href="{{ route('sizes.index') }}" class="btn btn-outline-secondary mis-2 px-4 py-2">
                            <i class="fas fa-arrow-left mie-2"></i>{{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>
@endsection
