@extends('adminlte::page')

@section('title', __('adminlte::adminlte.category'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-code-branch me-2 text-primary"></i>
            @if (app()->getLocale() === 'ar')
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.category') }}
            @else
                {{ __('adminlte::adminlte.category') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>

        @if($category->is_main_branch)
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
                        src="{{ $category->image ? asset($category->image) : 'https://placehold.co/500x300?text=Branch+Image' }}"
                        alt="Branch Image"
                        class="img-fluid rounded-3"
                        style="max-height: 280px; object-fit: cover;"
                    >
                </div>
            </div>

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Branch Name EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $category->name_en }}</div>
                    </div>

                    {{-- Branch Name AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $category->name_ar }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($category->is_active)
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
                        <div class="fw-semibold">{{ $category->address_en ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_ar') }}</small>
                        <div class="fw-semibold">{{ $category->address_ar ?? '-' }}</div>
                    </div>

                    {{-- Branches --}}
                    <div class="col-12">
                        <h6 class="font-weight-bold text-secondary">{{ __('adminlte::adminlte.branch') }}</h6>
                        @if($category->branches->count())
                            <ul class="list-unstyled ps-2">
                                @foreach($category->branches as $branch)
                                    <li>
                                        <a href="{{ route('companyBranch.show', $branch->id) }}" class="text-primary fw-bold">
                                            <i class="fas fa-code-branch me-1"></i> {{ $branch->name_en }}
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
                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </x-adminlte-card>
</div>
@endsection
