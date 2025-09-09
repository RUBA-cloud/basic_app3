@extends('adminlte::page')

@section('title', __('adminlte::adminlte.employee_module'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">{{ __('adminlte::adminlte.employee') }} #{{ $employee->id }}</h1>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>{{ __('adminlte::adminlte.go_back') }}</a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex gap-3 align-items-center mb-3" style="padding: 5px">
            <img src="{{ $employee->avatar_url }}" alt="avatar" class="rounded-circle" style="width:70px;height:70px;object-fit:cover; margin:5px">
            <div>
                <div class="h5 mb-1">{{ $employee->name }}</div>
                <div class="text-muted">{{ $employee->email }}</div>
            </div>
        </div>

        <h6 class="fw-bold">{{ __('adminlte::adminlte.permissions') }}</h6>
        @if($employee->permissions->isEmpty())
            <div class="text-muted">{{ __('adminlte::menu.permissions') }}</div>
        @else
            <ul class="mb-0">
                @foreach($employee->permissions as $p)
                    <li>{{ $p->display_name ?? ($p->name_en ?: $p->name_ar) }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
