@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.employee'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">{{ __('adminlte::adminlte.edit') }}{{ __('adminlte::adminlte.employee') }}</h1>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>{{ __('adminlte::adminlte.go_back') }}</a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('employees.update',$employee) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('Put')
            @include('employee.form', ['employee' => $employee, 'permissions' => $permissions])
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ __('adminlte::adminlte.save_information') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
