@extends('adminlte::page')

@section('title', __('adminlte::adminlte.modules_title'))

@section('content')
<div class="container">
    <div class="card p-3">
        <h2 class="mb-4">{{ __('adminlte::adminlte.create') }}</h2>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('modules.store') }}" method="POST">
            @csrf

            <div class="row">
                {{-- Company Modules Card --}}
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-primary text-white font-weight-bold">
                            {{ __('adminlte::adminlte.group_company') }}
                        </div>
                        <div class="card-body">
                            @php
                                $companyFields = [
                                    'company_dashboard_module'   => __('adminlte::adminlte.company_dashboard_module'),
                                    'company_info_module'        => __('adminlte::adminlte.company_info_module'),
                                    'company_branch_module'      => __('adminlte::adminlte.company_branch_module'),
                                    'company_category_module'    => __('adminlte::adminlte.company_category_module'),
                                    'company_type_module'        => __('adminlte::adminlte.company_type_module'),
                                    'company_size_module'        => __('adminlte::adminlte.company_size_module'),
                                    'company_offers_type_module' => __('adminlte::adminlte.company_offers_type_module'),
                                    'company_offers_module'      => __('adminlte::adminlte.company_offers_module'),
                                    'order_status_module'        => __('adminlte::adminlte.order_status_module'),
                                    'region_module'              => __('adminlte::adminlte.region_module'),
                                    'payment_module'             => __('adminlte::adminlte.payment_module'),
                                    'company_delivery_module'    => __('adminlte::adminlte.company_delivery_module'),
                                ];
                            @endphp

                            @foreach ($companyFields as $field => $label)
                                <div class="form-check mb-2">
                                    {{-- ensure unchecked posts 0 --}}
                                    <input type="hidden" name="{{ $field }}" value="0">
                                    <input type="checkbox"
                                           name="{{ $field }}"
                                           value="1"
                                           class="form-check-input"
                                           id="{{ $field }}"
                                           {{ old($field) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Other Modules Card --}}
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-success text-white font-weight-bold">
                            {{ __('adminlte::adminlte.group_other') }}
                        </div>
                        <div class="card-body">
                            @php
                                $otherFields = [
                                    'product_module'  => __('adminlte::adminlte.product_module'),
                                    'employee_module' => __('adminlte::adminlte.employee_module'),
                                    'order_module'    => __('adminlte::adminlte.order_module'),
                                    'is_active'       => __('adminlte::adminlte.is_active'),
                                ];
                            @endphp

                            @foreach ($otherFields as $field => $label)
                                <div class="form-check mb-2">
                                    <input type="hidden" name="{{ $field }}" value="0">
                                    <input type="checkbox"
                                           name="{{ $field }}"
                                           value="1"
                                           class="form-check-input"
                                           id="{{ $field }}"
                                           {{-- default "is_active" to true on create --}}
                                           {{ $field === 'is_active'
                                                ? (old($field, true) ? 'checked' : '')
                                                : (old($field) ? 'checked' : '') }}>
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
@endsection
