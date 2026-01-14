@extends('adminlte::page')

@section('title', __('adminlte::adminlte.transportation_way') . ' ' . __('adminlte::adminlte.edit'))

@section('content')
<div class="container py-4">
  <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
    @include('transpartationWay.form', [
      'action' => route('transpartation_ways.update', $transpartationWay->id),
      'method' => 'PUT',
      'transpartationWay' => $transpartationWay,
      'countries' => $countries,
      'cities' => $cities,
    ])
  </x-adminlte-card>
</div>
@endsection
