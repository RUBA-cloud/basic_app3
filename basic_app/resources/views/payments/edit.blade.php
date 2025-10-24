@extends('adminlte::page')
@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.payment'))

@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">

        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
            {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.payment') }}
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
@include('payments.form', [
            'action'     => route('payment.update', $payment->id),
            'method'     => 'PUT',
            'payment' => $payment,
        ])
    </div>
</div>
@endsection
