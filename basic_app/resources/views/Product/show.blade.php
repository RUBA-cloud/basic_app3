@extends('adminlte::page')

@section('title', __('adminlte::adminlte.product'))

@section('content')
<div class="container-fluid">
    <x-adminlte-card title="{{ $product->name_en }} ({{ $product->name_ar }})"
                     theme="light"
                     theme-mode="outline"
                     icon="fas fa-box"
                     class="shadow rounded-3">

        {{-- Status & Price --}}
        <div class="mb-3">
            <x-adminlte-badge
                label="{{ $product->is_active ? __('adminlte::adminlte.is_active') : __('adminlte::adminlte.is_not_active') }}"
                theme="{{ $product->is_active ? 'success' : 'danger' }}"
            />
            <x-adminlte-badge
                label="Price: {{ number_format($product->price, 2) }} JD"
                theme="secondary"
                class="ms-2"
            />
        </div>

        {{-- Type & Category --}}
        <div class="mb-3">
            <strong>{{ __('adminlte::adminlte.type') }}:</strong> {{ optional($product->type)->name_en ?? '-' }}<br>
            <strong>{{ __('adminlte::adminlte.category') }}:</strong> {{ optional($product->category)->name_en ?? '-' }}
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <strong>{{ __('adminlte::adminlte.description') }} (EN):</strong>
            <p>{{ $product->description_en ?? '-' }}</p>

            <strong>{{ __('adminlte::adminlte.description') }} (AR):</strong>
            <p>{{ $product->description_ar ?? '-' }}</p>
        </div>

        {{-- Colors --}}
        @if(!empty($product->colors) && is_array($product->colors))
            <div class="mb-3">
                <strong>{{ __('adminlte::adminlte.colors') }}:</strong>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach($product->colors as $color)
                        <span class="badge rounded-pill border"
                              style="background-color: {{ $color }}; color: #fff;">
                            {{ $color }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Sizes --}}
        @if($product->sizes && $product->sizes->count())
            <div class="mb-3">
                <strong>{{ __('adminlte::adminlte.size') }}:</strong>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach($product->sizes as $size)
                        <x-adminlte-badge label="{{ $size->name_en }}" theme="primary" />
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Additionals --}}
        @if($product->additionals && $product->additionals->count())
            <div class="mb-3">
                <strong>{{ __('adminlte::adminlte.additional') }}:</strong>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach($product->additionals as $additional)
                        <x-adminlte-badge label="{{ $additional->name_en }}" theme="info" />
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Images --}}
        @if($product->images && $product->images->count())
            <div class="mb-3">
                <strong>{{ __('adminlte::adminlte.image') }}:</strong>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    @foreach($product->images as $image)
                        <div class="border rounded-3 overflow-hidden shadow-sm" style="width:120px; height:120px;">
                            <img src="{{ $image->image_path }}" alt="Product Image"
                                 class="img-fluid object-fit-cover w-100 h-100">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </x-adminlte-card>
</div>
@endsection
