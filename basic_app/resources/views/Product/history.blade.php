@extends('adminlte::page')

@section('title', 'Company Products')

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">

    {{-- Sidebar --}}

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
<div style="flex-shrink: 0; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">


                            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">Company Products History</h2>

                    <a href="{{ route('product.index') }}"
                       style="background: #f7f7fa; color: #6C63FF; font-weight: 600; border-radius: 12px; padding: 10px 28px; text-decoration: none; border: 1.5px solid #6C63FF;">
                        <i class="fas fa-history" style="margin-right: 8px;"></i> Go Back
                    </a>
            </div>

            {{-- Table Field Definitions --}}
            @php
                $fields = [
                    ['key' => 'name_en', 'label' => 'Product Name (EN)'],
                    ['key' => 'name_ar', 'label' => '$type Name (AR)'],
                    ['key' => 'name_ar', 'label' => 'Product Name (AR)'],
                    ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => 'User Name'],
                    ['key' => 'user.id', 'label' => 'User Id '],
                ];
            @endphp

            {{-- Main Table Component --}}
            <x-main_table :fields="$fields" :value="$products" :details_route="'product.show'"

    :reactive_route="'product.reactive'"/>
        </div>
    </main>
</div>
@endsection
