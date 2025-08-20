@extends('adminlte::page')

@section('title', __('adminlte::adminlte.branch_details'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-code-branch me-2 text-primary"></i>
            {{ __('adminlte::adminlte.branch_details') }}
        </h2>

        @if($branch->is_main_branch)
            <span class="badge bg-purple text-white px-3 py-2">
                <i class="fas fa-star me-1"></i>
                {{ __('adminlte::adminlte.main_branch') }}
            </span>
        @endif
    </div>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">

            {{-- Image --}}
            <div class="col-lg-4 col-md-5">
                <div class="border rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center p-2 h-100">
                    <img
                        src="{{ $branch->image ? asset($branch->image) : 'https://placehold.co/500x300?text=Branch+Image' }}"
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
                        <small class="text-muted">{{ __('adminlte::adminlte.branch_name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $branch->name_ar }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($branch->is_active)
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            </span>
                        @endif
                    </div>

                    {{-- Contact Info --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_phone') }}</small>
                        <div class="fw-semibold">{{ $branch->phone ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_email') }}</small>
                        <div class="fw-semibold">{{ $branch->email ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.fax') }}</small>
                        <div class="fw-semibold">{{ $branch->fax ?? '-' }}</div>
                    </div>

                    {{-- Addresses --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_en') }}</small>
                        <div class="fw-semibold">{{ $branch->address_en ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_ar') }}</small>
                        <div class="fw-semibold">{{ $branch->address_ar ?? '-' }}</div>
                    </div>

                    {{-- Location --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.location') }}</small>
                        <div>
                            @if($branch->location)
                                <a href="{{ $branch->location }}" target="_blank" class="text-primary fw-semibold">
                                    <i class="fas fa-map-marker-alt me-1"></i> {{ __('adminlte::adminlte.view_on_map') }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    {{-- Working Days / Hours --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.working_days') }}</small>
                        <div class="fw-semibold">
                            @php
                                $days = $branch->working_days ? explode(',', $branch->working_days) : [];
                                $days = array_map('trim', $days);
                            @endphp
                            {{ $days ? implode(', ', $days) : '-' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.working_hours') }}</small>
                        <div class="fw-semibold">
                            {{ $branch->working_hours_from ?? '-' }} - {{ $branch->working_hours_to ?? '-' }}
                        </div>
                    </div>

                    {{-- Company Info --}}
                    @if ($branch->companyInfo)
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('adminlte::adminlte.company_name_en') }}</small>
                            <div class="fw-semibold">{{ $branch->companyInfo->name_en ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('adminlte::adminlte.company_name_ar') }}</small>
                            <div class="fw-semibold">{{ $branch->companyInfo->name_ar ?? '-' }}</div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('companyBranch.edit', $branch->id) }}"
                           class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{route('companyBranch.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>
@endsection
