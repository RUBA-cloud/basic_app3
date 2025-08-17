@extends('adminlte::page')

@section('title', 'Size Info')

@section('content_header')
    <h1>Size Info</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-adminlte-card theme="light" theme-mode="outline" title="{{ $size->name_en }}">
                {{-- Subtitle --}}
                <h5 class="text-muted">{{ $size->name_ar }}</h5>

                {{-- Status Badge --}}
                <p class="mt-3">
                    <x-adminlte-badge label="{{ $size->is_active ? 'Active' : 'Inactive' }}" theme="{{ $size->is_active ? 'success' : 'danger' }}" />

                </p>

                {{-- Size Details --}}
                <p class="text-muted mb-0">
                    <i>Size ID:</i> {{ $size->id }}
                </p>

                {{-- Action Button --}}
                <x-slot name="footerSlot">
                    <a href="{{ route('sizes.edit', $size->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i> Edit Size
                    </a>
                </x-slot>
            </x-adminlte-card>
        </div>
    </div>
@stop
