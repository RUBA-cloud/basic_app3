{{-- resources/views/permissions/edit.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.permissions'))

@section('content_header')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1 class="m-0">{{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.permissions') }}</h1>

        </div>
        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> {{ __('adminlte::adminlte.go_back') ?? 'Back' }}
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success"><i class="far fa-check-circle me-1"></i>{{ session('success') }}</div>
            @endif

            <form action="{{ route('permissions.update', $permission) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                @include('permissions.form', [
                    'permission'        => $permission,
                    'modulesRow'        => $modulesRow,
                    'featuresForRadios' => $featuresForRadios,
                    'defaultFeatureKey' => $defaultFeatureKey,
                ])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times me-1"></i> {{ __('adminlte::adminlte.cancel') ?? 'Cancel' }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> {{ __('adminlte::adminlte.update') ?? 'Update' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
/* radio card styles (same as create) */
.radio-card { cursor: pointer; position: relative; border: 1px solid #e5e7eb; border-radius: .75rem; }
.radio-card-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.radio-card-body { border-radius: .75rem; transition: box-shadow .15s ease, border-color .15s ease; }
.radio-card:hover .radio-card-body { box-shadow: 0 .25rem .75rem rgba(0,0,0,.05); }
.radio-card-input:focus + .radio-card-body { outline: 2px solid #3b82f6; outline-offset: 2px; }
.radio-card-input:checked + .radio-card-body { border: 2px solid #3b82f6; }
.radio-card-indicator { width: 1.25rem; height: 1.25rem; border: 2px solid #cbd5e1; border-radius: 999px; display: grid; place-items: center; }
.radio-card-input:checked + .radio-card-body .radio-card-indicator { border-color: #3b82f6; }
.radio-card-indicator .dot { width: .6rem; height: .6rem; border-radius: 999px; background: transparent; }
.radio-card-input:checked + .radio-card-body .dot { background: #3b82f6; }
.feature-badge { border-color: #e5e7eb !important; font-weight: 500; }
</style>
@endpush
