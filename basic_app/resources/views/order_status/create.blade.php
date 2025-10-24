@extends('adminlte::page')
@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.payement'))

@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
            {{ __('adminlte::adminlte.create') }} {{ __('adminlte::adminlte.order_status') }}
        </h2>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
 @include('order_status.form', [
            'action'     => route('order_status.store'),
            'method'     => 'POST',
            'order_status' => null,
        ])
    </div>
</div>
@endsection
