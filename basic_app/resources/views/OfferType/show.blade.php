@extends('adminlte::page')
 @section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.offers_type'))
@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">

        <x-adminlte-card title="{{ __('adminlte::adminlte.offers_type') }}" theme="info" icon="fas fa-info-circle" collapsible>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>{{ __('adminlte::adminlte.name_en')}}:</strong>
                    <div class="form-control-plaintext">{{ $offerType->name_en ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <strong>{{ __('adminlte::adminlte.name_ar')}}:</strong>
                    <div class="form-control-plaintext">{{ $offerType->name_ar ?? '-' }}</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>{{ __('adminlte::adminlte.descripation')}}:(EN)</strong>
                    <div class="form-control-plaintext">{{ $offerType->description_en ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <strong>{{ __('adminlte::adminlte.descripation')}}:(AR)</strong>
                    <div class="form-control-plaintext">{{ $offerType->description_ar ?? '-' }}</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>{{ __('adminlte::adminlte.descripation')}}: (AR)</strong><br>
                    @if($offerType->is_discount)
                        <span class="badge bg-success">{{ __('Yes') }}</span>
                    @else
                        <span class="badge bg-secondary">{{ __('No') }}</span>
                    @endif
                </div>
                <div class="col-md-4">
                    <strong>{{__('adminlte::adminlte.is_total_gift')}}</strong><br>
                    @if($offerType->is_total_gift)
                        <span class="badge bg-success">{{ __('Yes') }}</span>
                    @else
                        <span class="badge bg-secondary">{{ __('No') }}</span>
                    @endif
                </div>
                <div class="col-md-4">
                    <strong>{{__('adminlte::adminlte.is_product_count_gift')}}</strong><br>
                    @if($offerType->is_product_count_gift)
                        <span class="badge bg-success">{{ __('Yes') }}</span>
                    @else
                        <span class="badge bg-secondary">{{ __('No') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <strong>{{ __('Active') }}:</strong><br>
                @if($offerType->is_active)
                    <strong>{{__('adminlte::adminlte.is_discount')}}</strong><br>
                    <span class="badge bg-success">{{__('adminlte::adminlte.active')}}</span>
                @else
                    <span class="badge bg-success">{{__('adminlte::adminlte.inactive')}}</span>
                @endif
            </div>

            @if($offerType->is_discount)
                <x-adminlte-card title="{{ __('adminlte::adminlte.discount') }} {{ __('adminlte::adminlte.details')}}" theme="lightblue" icon="fas fa-percent" class="mb-3">
                    <div class="mb-2">
                        <strong>{{ __('adminlte::adminlte.discount')}}:</strong>
                        <div class="form-control-plaintext">{{ $offerType->discount_value_product ?? '-' }}</div>
                    </div>
                    <div>
                        <strong>{{ __('Discount Value Delivery') }}:</strong>
                        <div class="form-control-plaintext">{{ $offerType->discount_value_delivery ?? '-' }}</div>
                    </div>
                </x-adminlte-card>
            @endif

            @if($offerType->is_product_count_gift || $offerType->is_total_gift)
                <x-adminlte-card title="{{ __('adminlte::adminlte.gift') }} {{ __('adminlte::adminlte.details')}}" theme="lightblue" icon="fas fa-percent" class="mb-3">
                    @if($offerType->is_product_count_gift)
                        <div class="mb-2">
                            <strong>{{ __('adminlte::adminlte.product_count_gift') }}:</strong>
                            <div class="form-control-plaintext">{{ $offerType->products_count_to_get_gift_offer ?? '-' }}</div>
                        </div>
                    @endif
                    @if($offerType->is_total_gift)
                        <div>
                            <strong>{{ __('adminlte::adminlte.total_gift') }}:</strong>
                            <div class="form-control-plaintext">{{ $offerType->total_gift ?? '-' }}</div>
                        </div>
                    @endif
                </x-adminlte-card>
            @endif

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('offers_type.edit', $offerType->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-1"></i> {{ __('adminlte::adminlte.edit') }}
                </a>
            </div>

        </x-adminlte-card>

    </div>
</div>
@endsection
