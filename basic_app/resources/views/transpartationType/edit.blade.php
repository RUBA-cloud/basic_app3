@extends('adminlte::page')
@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.transpartation_type'))
@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">{{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.transpartation_type') }}   </h2>

        @include('TranspartationType.form', [
            'action'     => route('transpartation_types.update', $transpartationType),
            'method'     => 'PUT',
            'transpartationType' => $transpartationType,
        ])
    </div>
    </div>
@endsection
