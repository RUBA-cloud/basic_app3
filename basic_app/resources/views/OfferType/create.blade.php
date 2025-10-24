@extends('adminlte::page')

@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.offers_type'))

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="padding: 24px; width: 100%;">
        <h2 class="mb-4">{{  __('adminlte::adminlte.create')}} {{ __('adminlte::adminlte.offers_type') }}</h2>

        @include('OfferType.form', data: [
            'action'     => route('offers_type.store'),
            'method'     => 'POST',
            'offerType' => null,
        ])
    </div>
</div>
@endsection
