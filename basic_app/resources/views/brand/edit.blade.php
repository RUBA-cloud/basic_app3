{{-- resources/views/brands/edit.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit_brand'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">{{ __('adminlte::adminlte.edit_brand') }}</h1>
        <a href="{{ route('brands.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
        </a>
    </div>
@endsection

@section('content')
<div class="card shadow-sm" style="max-width:680px;margin:0 auto;">
    <div class="card-body">
        @include('brand.form', [
            'action'    => route('brands.update', $brand),
            'method'    => 'PUT',
            'companies' => $companies,
            'brand'     => $brand,
        ])
    </div>
</div>
@endsection