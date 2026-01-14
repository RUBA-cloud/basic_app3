@extends('adminlte::page')
@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.transpartation_type'))
@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
  {{ __('adminlte::adminlte.create') }} {{ __('adminlte::adminlte.transpartation_type') }}

        </h2>

        @include('transpartationType.form', [
            'action'     => route('transpartation_types.store'),
            'method'     => 'POST',
            'transpartationType' => null,
        ])
    </div>
    </div>
@endsection
