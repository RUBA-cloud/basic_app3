@extends('adminlte::page')

@section('title', __('adminlte::adminlte.additional'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
       <h2 class="h3 mb-0 fw-bold text-dark">
            @if (app()->getLocale() === 'ar')
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.additional') }}
            @else
                {{ __('adminlte::adminlte.additional') }} {{ __('adminlte::adminlte.details') }}
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
                        src="{{ $additional->image ? asset($additional->image) : 'https://placehold.co/500x300?text=Branch+Image' }}"
                        alt="Branch Image"
                        class="img-fluid rounded-3"
                        style="max-height: 280px; object-fit: cover;"
                    >
                </div>
            </div>

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">
                    {{-- Branch Name --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $additional->name_en}}</div>
                    </div>
                    {{-- Branch Name --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $additional->name_ar }}</div>
                    </div>
                                    {{-- Branch Name --}}
                                    {{-- Status --}}
                    <div class="col-12">
                        @if($additional->is_active)
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            </span>
                        @endif
                    </div>
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.price') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $additional->price }} JD</div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.descripation') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $additional->description	 }}</div>
                    </div>
              {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('additionals.edit', $additional->id) }}"
                           class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{route('additionals.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>
@endsection
