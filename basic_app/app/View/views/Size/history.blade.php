@extends('adminlte::page')
@section('title', 'Company Sizes History')
@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">Company Sizes History</h2>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                <a href="{{ route('sizes.index') }}"
                   style="background: #f7f7fa; color: #6C63FF; font-weight: 600; border-radius: 12px; padding: 10px 28px; text-decoration: none; border: 1.5px solid #6C63FF;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Go Back
                </a>
            </div>

            {{-- Define Table Fields --}}
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

            {{-- Table Component --}}
            <x-main_table
                :fields="$fields"
                :value="$sizes"
                :details_route="'sizes.show'"
                :reactive_route="'sizes.reactive'"
            />
        </div>
    </main>
</div>
@endsection
