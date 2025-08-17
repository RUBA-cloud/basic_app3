@extends('adminlte::page')

@section('title', 'Company Sizes')

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-3" style="padding: 24px">
            <div class="col">
                <h2 class="font-weight-bold text-dark">Company Sizes</h2>
            </div>
            <div class="col-auto" >
                {{-- Action Buttons --}}
                <a href="{{ route('sizes.create') }}" class="btn btn-primary mr-2">
                    <i class="fas fa-plus mr-1"></i> Add
                </a>
                <a href="{{ route('sizes.history', ['isHistory' => 'true']) }}" class="btn btn-outline-primary">
                    <i class="fas fa-history mr-1"></i> History
                </a>
            </div>
        </div>

        {{-- Sizes Table Card --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">List of Sizes</h3>
            </div>

            <div class="card-body table-responsive p-0">
                @php
                    $fields = [
                        ['key' => 'name_en', 'label' => 'Name (EN)'],
                        ['key' => 'name_ar', 'label' => 'Name (AR)'],
                        ['key' => 'price', 'label' => 'Price'],
                        ['key' => 'user.name', 'label' => 'User Name'],
                        ['key' => 'user.id', 'label' => 'User ID'],
                        ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool'],
                    ];
                @endphp

                <x-main_table
                    :fields="$fields"
                    :value="$sizes"
                    :details_route="'sizes.show'"
                    :edit_route="'sizes.edit'"
                    :delete_route="'sizes.destroy'"
                    :reactive_route="'sizes.reactive'"
                />
            </div>
        </div>
    </div>
@endsection
