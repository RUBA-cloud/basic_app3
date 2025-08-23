@extends('adminlte::page')

@section('title', __('adminlte::adminlte.type'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <i class="fas fa-code-branch fa-2x text-primary me-3"></i>
        <h2 class="h3 mb-0 fw-bold text-dark">
            @if (app()->getLocale() === 'ar')
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.type') }}
            @else
                {{ __('adminlte::adminlte.type') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>
    </div>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow rounded-3 border-0">

        {{-- Fancy Header --}}
        <div class="p-4 rounded-top" style="background: linear-gradient(135deg,#4e73df,#224abe); color:white;">
            <h4 class="fw-bold mb-0">
                <i class="fas fa-info-circle me-2"></i> {{ __('adminlte::adminlte.type') }}
            </h4>
        </div>

        <div class="p-4">
            <div class="row g-4">

                {{-- Name English --}}
                <div class="col-12">
                    <small class="text-muted">
                        <i class="fas fa-language me-1 text-secondary"></i> {{ __('adminlte::adminlte.name_en') }}
                    </small>
                    <div class="fs-5 fw-bold text-dark border-bottom pb-2">{{ $type->name_en }}</div>
                </div>

                {{-- Name Arabic --}}
                <div class="col-12">
                    <small class="text-muted">
                        <i class="fas fa-language me-1 text-secondary"></i> {{ __('adminlte::adminlte.name_ar') }}
                    </small>
                    <div class="fs-5 fw-bold text-dark border-bottom pb-2" dir="rtl">{{ $type->name_ar }}</div>
                </div>

                {{-- Status --}}
                <div class="col-12">
                    @if($type->is_active)
                        <span class="badge bg-success px-3 py-2 shadow-sm">
                            <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                        </span>
                    @else
                        <span class="badge bg-danger px-3 py-2 shadow-sm">
                            <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                        </span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="col-12 pt-3">
                    <a href="{{ route('type.edit', $type->id) }}"
                       class="btn btn-primary px-4 py-2 shadow-sm hover-shadow">
                        <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                    </a>
                    <a href="{{ route('type.index') }}"
                       class="btn btn-outline-secondary ms-2 px-4 py-2 shadow-sm hover-shadow">
                        <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                    </a>
                </div>

            </div>
        </div>
    </x-adminlte-card>
</div>
@endsection
