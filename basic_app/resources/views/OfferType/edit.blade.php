@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.offers_type'))

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="padding: 24px; width: 100%;">
        <h2 class="mb-4">{{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.offers_type') }}</h2>

        @include('OfferType.form', [
            'action'     => route('offers_type.update', $offerType->id),
            'method'     => 'PUT',
            'offerType' => $offerType,
        ])
    </div>
</div>
@endsection
