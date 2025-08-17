@extends('layouts.app')

@section('title', 'Company  Categories History')

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">

    {{-- Sidebar --}}
    <x-sidebar />

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">


            {{-- Action Buttons --}}
            <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                 <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">Company Branches</h2>

                    <a href="{{ route('categories.index',false) }}"
                       style="background: #f7f7fa; color: #6C63FF; font-weight: 600; border-radius: 12px; padding: 10px 28px; text-decoration: none; border: 1.5px solid #6C63FF;">
                        <i class="fas fa-history" style="margin-right: 8px;"></i> Go Back
                    </a>

            </div>

            {{-- Table Field Definitions --}}
            @php
                $fields = [
                    ['key' => 'name_en', 'label' => 'Category Name (EN)'],
                    ['key' => 'name_ar', 'label' => 'Category Name (AR)'],
                    ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => 'User Name'],
                    ['key' => 'user.id', 'label' => 'User Id '],

                ];
            @endphp

            {{-- Main Table Component --}}
            <x-main_table :fields="$fields" :value="$categories" :details_route="'categories.show'"


    :reactive_route="'reactive_category'"/>
        </div>
    </main>
</div>
@endsection
