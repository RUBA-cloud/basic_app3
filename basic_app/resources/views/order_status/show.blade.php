@extends('adminlte::page')

@section('title', __('adminlte::adminlte.orderStatus'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-code-branch me-2 text-primary"></i>
            @if (app()->getLocale() === 'ar')
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.orderStatus') }}
            @else
                {{ __('adminlte::adminlte.orderStatus') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Branch Name EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $orderStatus->name_en }}</div>
                    </div>

                    {{-- Branch Name AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $orderStatus->name_ar }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($orderStatus->is_active)
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            </span>
                        @endif
                    </div>

                    {{-- Addresses --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_en') }}</small>
                        <div class="fw-semibold">{{ $orderStatus->address_en ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_ar') }}</small>
                        <div class="fw-semibold">{{ $orderStatus->address_ar ?? '-' }}</div>
                    </div>

                    {{-- Branches --}}
                    <div class="col-12">
                        <h6 class="font-weight-bold text-secondary">{{ __('adminlte::menu.branches') }}</h6>
                        @if($orderStatus->branches->count())
                            <ul class="list-unstyled ps-2">
                                @foreach($orderStatus->branches as $branch)
                                    <li>
                                        <a href="{{ route('companyBranch.show', $branch->id) }}" class="text-primary fw-bold">
                                            @if(app()->getLocale()=="ar")
                                             <i class="fas fa-code-branch me-1"></i> {{ $branch->name_ar}}@else
                                            <i class="fas fa-code-branch me-1"></i> {{ $branch->name_en }}

                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">{{ __('adminlte::adminlte.no_branches') }}</p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('orderStatus.edit', $orderStatus->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('orderStatus.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </x-adminlte-card>
</div>
@endsection
