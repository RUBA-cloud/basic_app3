@extends('adminlte::page')

@section('title', __('adminlte::adminlte.details') . ' ' . __('adminlte::adminlte.offers'))

@section('content_header')
    <h1 class="mb-2">{{ __('adminlte::adminlte.offers') }}</h1>
@stop

@section('content')
<div class="card shadow" style="margin: 5px;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title text-primary mb-0">
            <i class="fas fa-info-circle"></i>
                {{ __('adminlte::adminlte.details') }}:
            <strong>{{ $offer->name_en ?? '-' }}</strong>
        </h3>
        <a href="{{ route('offers.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('adminlte::adminlte.go_back') }}
        </a>
    </div>

    <div class="card-body">
        {{-- Names --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.name_en') }}</label>
                <div class="font-weight-bold">{{ $offer->name_en ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</label>
                <div class="font-weight-bold">{{ $offer->name_ar ?? '-' }}</div>
            </div>
        </div>

        {{-- Descriptions --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                <div class="text-wrap">{!! nl2br(e($offer->description_en ?? '')) ?: '-' !!}</div>
            </div>
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                <div class="text-wrap">{!! nl2br(e($offer->description_ar ?? '')) ?: '-' !!}</div>
            </div>
        </div>

        {{-- Category & Type --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.category') }}</label>
                <div>
                    @php $isAr = app()->getLocale() === 'ar'; @endphp
                    @forelse(($offer->categories ?? []) as $category)
                        <span class="badge badge-info mr-1">
                            {{ $isAr ? ($category->name_ar ?? $category->name_en) : ($category->name_en ?? $category->name_ar) }}
                        </span>
                    @empty
                        <span class="text-muted">-</span>
                    @endforelse
                </div>
            </div>

            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.type') }}</label>
                <div class="font-weight-bold">
                    {{ $isAr ? ($offer->type->name_ar ?? $offer->type->name_en ?? '-') : ($offer->type->name_en ?? $offer->type->name_ar ?? '-') }}
                </div>
            </div>
        </div>

        {{-- Dates --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.start_date') }}</label>
                <div>
                    {{ $offer->start_date ? \Illuminate\Support\Carbon::parse($offer->start_date)->format('Y-m-d') : '-' }}
                </div>
            </div>
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.end_date') }}</label>
                <div>
                    {{ $offer->end_date ? \Illuminate\Support\Carbon::parse($offer->end_date)->format('Y-m-d') : '-' }}
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted">{{ __('adminlte::adminlte.is_active') }}</label>
                <div>
                    @if(!empty($offer->is_active))
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
