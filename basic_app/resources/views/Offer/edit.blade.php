@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.offers'))

@section('content_header')
    <h1>{{ __('adminlte::adminlte.offers') }}</h1>
@stop

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    // Build selected categories safely from old input or relation
    $selectedCategoryIds = collect(old('category_ids', $offer->categories?->pluck('id')->all() ?? []));
@endphp
@include('Offer.form', [
            'action'     => route('offers.update', $offer),
            'method'     => 'PUT',
            'offer'      => $offer,
            'isAr'       => $isAr,
            'selectedCategoryIds' => $selectedCategoryIds,
        ])
