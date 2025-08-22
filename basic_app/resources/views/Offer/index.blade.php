@extends('layouts.app')

@section('title', 'Offer Type')

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Sidebar --}}

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">Company Sizes</h2>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                <a href="{{ route('offers_type.create') }}"
                   style="background: #6C63FF; color: #fff; font-weight: 600; border-radius: 12px; padding: 10px 28px; text-decoration: none; box-shadow: 0 2px 8px 0 rgba(108,99,255,0.10);">
                    <i class="fas fa-plus" style="margin-right: 8px;"></i> Add
                </a>
                <a href="{{ route('offers_type.store', ['isHistory' => 'true']) }}"
                   style="background: #f7f7fa; color: #6C63FF; font-weight: 600; border-radius: 12px; padding: 10px 28px; text-decoration: none; border: 1.5px solid #6C63FF;">
                    <i class="fas fa-history" style="margin-right: 8px;"></i> History
                </a>
            </div>

            {{-- Define Table Fields --}}
            @php
                $fields = [
                    ['key' => 'name_en', 'label' => 'Name (EN)'],
                    ['key' => 'name_ar', 'label' => 'Name (AR)'],
                    ['key' => 'description_en', 'label' => 'Description (EN)'],
                    ['key' => 'description_ar', 'label' => 'Description (AR)'],

                    ['key' => 'user.name', 'label' => 'User Name'],
                    ['key' => 'user.id', 'label' => 'User ID'],
                    ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool'],
                ];
            @endphp

            {{-- Table Component --}}
            <x-main_table
                :fields="$fields"
                :value="$offersType"
                :details_route="'sizes.show'"
                :edit_route="'sizes.edit'"
                :delete_route="'sizes.destroy'"
                :reactive_route="'sizes.reactive'"
            />
        </div>
    </main>
</div>
@endsection
