@extends('adminlte::page')
@section('title',__('adminlte::adminlte.permissions'))
@section('content_header')
    <h1>{{ __('adminlte::adminlte.permissions') }} #{{ $permission->id }}</h1>
@stop
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">{{ __('adminlte::adminlte.permissions') }}</dt>
                <dd class="col-sm-9">{{ $permission->module_name }}</dd>

                <dt class="col-sm-3">{{ __('adminlte::adminlte.name_en') }}</dt>
                <dd class="col-sm-9">{{ $permission->name_en }}</dd>

                <dt class="col-sm-3">{{ __('adminlte::adminlte.name_ar') }}</dt>
                <dd class="col-sm-9">{{ $permission->name_ar }}</dd>

                <dt class="col-sm-3">{{ __('adminlte::adminlte.name_en') }}</dt>
                <dd class="col-sm-9">
                    <span class="badge {{ $permission->can_edit ? 'badge-success' : 'badge-secondary' }}">{{ __('adminlte::adminlte.edit') }}</span>
                    <span class="badge {{ $permission->can_delete ? 'badge-success' : 'badge-secondary' }}">{{ __('adminlte::adminlte.delete') }}</span>
                    <span class="badge {{ $permission->can_add ? 'badge-success' : 'badge-secondary' }}">{{ __('adminlte::adminlte.add') }}</span>
                    <span class="badge {{ $permission->can_view_history ? 'badge-success' : 'badge-secondary' }}">{{ __('adminlte::adminlte.view_history') }}</span>
                </dd>

                <dt class="col-sm-3">{{ __('adminlte::adminlte.active') }}</dt>
                <dd class="col-sm-9">
    @if($permission->is_active)
        <span class="badge badge-success">{{ __('adminlte::adminlte.yes') }}</span>
    @else
        <span class="badge badge-secondary">{{ __('adminlte::adminlte.no') }}</span>
    @endif
</dd>

            </dl>
         <div class="d-flex justify-content-end mt-4">

                    <div class="col-12 pt-3">
                        <a href="{{ route('permissions.edit', $permission->id) }}"
                           class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{route('permissions.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>
                 </div>

    </div>
</div>
</div>
@endsection
