@extends('adminlte::page')

@section('title', __('adminlte::adminlte.payment'))

@section('content')
<div class="container py-4">



    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Branch Name EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $payment->name_en }}</div>
                    </div>

                    {{-- Branch Name AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $payment->name_ar }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($payment->is_active)
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
                        <div class="fw-semibold">{{ $payment->address_en ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_ar') }}</small>
                        <div class="fw-semibold">{{ $payment->address_ar ?? '-' }}</div>
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('payment.edit', $payment->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('payment.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </x-adminlte-card>
</div>
@endsection
