@extends('layouts.app')

@section('title', 'Company Additionals')

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">

    {{-- Sidebar --}}
    <x-sidebar />

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">

            {{-- Page Title --}}
            <h2 style="font-size: 2rem; font-weight: 700; color: var(--text-color); margin-bottom: 24px;">
                Company Additionals
            </h2>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: 16px; margin-bottom: 24px;">
                <a href="{{ route('additional.create') }}"
                   style="background: var(--main-color); color: #fff; font-weight: 600; border-radius: 12px; padding: 10px 24px; text-decoration: none; box-shadow: 0 2px 8px rgba(108,99,255,0.10); display: inline-flex; align-items: center;">
                    <i class="fas fa-plus" style="margin-right: 8px;"></i> Add New
                </a>
                <a href="{{ route('additional.history', ['isHistory' => 'true']) }}"
                   style="background: #f7f7fa; color: var(--main-color); font-weight: 600; border-radius: 12px; padding: 10px 24px; text-decoration: none; border: 1.5px solid var(--main-color); display: inline-flex; align-items: center;">
                    <i class="fas fa-history" style="margin-right: 8px;"></i> View History
                </a>
            </div>

            {{-- Define Table Fields --}}
            @php
                $fields = [
                    ['key' => 'name_en', 'label' => 'Name (EN)'],
                    ['key' => 'name_ar', 'label' => 'Name (AR)'],
                    ['key' => 'user.name', 'label' => 'User Name'],
                    ['key' => 'user.id', 'label' => 'User ID'],
                    ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool'],
                ];
            @endphp

            {{-- Table Component --}}
            <x-main_table
                :fields="$fields"
                :value="$additionals"
                :details_route="'additional.show'"
                :edit_route="'additional.edit'"
                :delete_route="'additional.destroy'"
                :reactive_route="'additional.reactive'"
            />

        </div>
    </main>
</div>
@endsection
