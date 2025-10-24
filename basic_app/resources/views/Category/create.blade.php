

 @extends('adminlte::page')
@section('title', __('adminlte::adminlte.category'))
@section('content')
<div class="container-fluid py-4" style="margin: 10px">
    <x-adminlte-card class="header_card" style="padding: 10px"
        title="{{ __('adminlte::adminlte.category') }}"
        icon="fas fa-building" collapsible maximizable>
    </div>
@include('Category.form', [
    'action'     => route('categories.store'),
    'method'     => 'POST',
    'category' => null
]);

        </x-adminlte-card>
</div>
@endsection
