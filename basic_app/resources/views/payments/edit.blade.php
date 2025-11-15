@extends('adminlte::page')

@section('title', __('adminlte::adminlte.payment'))

@section('content')
    @include('payment._form', [
        'payment' => $payment,
        'action'  => 'payment.update',
        'method'  => 'PUT',
    ])
@endsection
