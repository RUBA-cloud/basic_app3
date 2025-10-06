@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.modules'))

@section('content_header')
    <h1>{{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.modules') }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('modules.update', $module) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Company Modules --}}
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white font-weight-bold">
                                {{ __('adminlte::adminlte.group_company') }}
                            </div>
                            <div class="card-body">
                                @foreach ([
                                    'company_dashboard_module'   => __('adminlte::adminlte.module_dashboard'),
                                    'company_info_module'        => __('adminlte::adminlte.module_info'),
                                    'company_branch_module'      => __('adminlte::adminlte.module_branch'),
                                    'company_category_module'    => __('adminlte::adminlte.module_category'),
                                    'company_type_module'        => __('adminlte::adminlte.module_type'),
                                    'company_size_module'        => __('adminlte::adminlte.module_size'),
                                    'company_offers_type_module' => __('adminlte::adminlte.module_offers_type'),
                                    'company_offers_module'      => __('adminlte::adminlte.module_offers'),
                                    'order_status_module'        => __('adminlte::adminlte.order_status_module'),
                                    'region_module'              => __('adminlte::adminlte.region_module'),
                                    'company_delivery_module'    => __('adminlte::adminlte.company_delivery_module'),
                                    'payment_module'             => __('adminlte::adminlte.payment_module'),
                                ] as $field => $label)
                                    <div class="form-check mb-2">
                                        {{-- ensure unchecked posts 0 --}}
                                        <input type="hidden" name="{{ $field }}" value="0">
                                        <input type="checkbox"
                                               name="{{ $field }}"
                                               value="1"
                                               class="form-check-input"
                                               id="{{ $field }}"
                                               {{ old($field, (bool) $module->$field) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Other Modules / Flags --}}
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white font-weight-bold">
                                {{ __('adminlte::adminlte.group_other') }}
                            </div>
                            <div class="card-body">
                                @foreach ([
                                    'product_module'  => __('adminlte::adminlte.module_product'),
                                    'employee_module' => __('adminlte::adminlte.module_employee'),
                                    'order_module'    => __('adminlte::adminlte.module_order'),
                                    'is_active'       => __('adminlte::adminlte.active'),
                                ] as $field => $label)
                                    <div class="form-check mb-2">
                                        <input type="hidden" name="{{ $field }}" value="0">
                                        <input type="checkbox"
                                               name="{{ $field }}"
                                               value="1"
                                               class="form-check-input"
                                               id="{{ $field }}"
                                               {{ old($field, (bool) $module->$field) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="form-group">
                    <x-adminlte-button
                        label="{{ __('adminlte::adminlte.save_information') }}"
                        type="submit"
                        theme="success"
                        class="w-100"
                        icon="fas fa-save"
                    />
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
