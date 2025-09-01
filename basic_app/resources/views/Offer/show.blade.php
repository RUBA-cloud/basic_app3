@extends('adminlte::page')

@section('title', __('adminlte::adminlte.show') . ' ' . __('adminlte.adminlte.offers'))

@section('content_header')
    <h1 class="mb-2">{{ __('adminlte::adminlte.offers') }}</h1>
@stop

@section('content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title text-primary">
            <i class="fas fa-info-circle"></i>
            {{ __('adminlte::adminlte.show') }}: <strong>{{ $offer->name_en }}</strong>
        </h3>
        <a href="{{ route('offers.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('adminlte::adminlte.go_back') }}
        </a>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.name_en') }}</label>
                <div class="font-weight-bold">{{ $offer->name_en }}</div>
            </div>
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</label>
                <div class="font-weight-bold">{{ $offer->name_ar }}</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                <div class="text-wrap">{{ $offer->description_en }}</div>
            </div>
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                <div class="text-wrap">{{ $offer->description_ar }}</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.category') }}</label>
                <div>
                    @foreach($offer->categories ?? [] as $category)
                    @if(app()->getLocal()=="ar")
                         <span class="badge badge-info mr-1">{{ $category->name_ar }}</span>
                        @else
                        <span class="badge badge-info mr-1">{{ $category->name_en }}</span>

                </div>
            </div>
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.type') }}</label>
                    @if(app()->getLocal()=="ar")

                <div class="font-weight-bold">{{ $offer->type->name_ar ?? '-' }}</div>
                @else
                <div class="font-weight-bold">{{ $offer->type->name_en ?? '-' }}</div>

                @endif
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.start_date') }}</label>
                <div>{{ $offer->start_date ? $offer->start_date: '-' }}</div>
            </div>
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.end_date') }}</label>
                <div>{{ $offer->end_date ? $offer->end_date : '-' }}</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.is_active') }}</label>
                <div>
                    @if($offer->is_active)
                        <span class="badge badge-success">{{ __('adminlte::adminlte.active') }}</span>
                    @else
                        <span class="badge badge-danger">{{ __('adminlte::adminlte.inactive') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop
