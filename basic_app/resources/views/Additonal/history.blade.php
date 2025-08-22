@extends('layouts.app')

@section('title', 'Company Additionals History')

@section('content')
<div style="min-height: 100vh; display: flex;">
    {{-- Sidebar --}}

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table" style="background: #fff; border-radius: 18px; box-shadow: 0 8px 32px rgba(31,38,135,0.10); padding: 32px;">

            {{-- Page Title & Back Button --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="font-size: 2rem; font-weight: 700; color: var(--text-color);">
                    Company Additionals History
                </h2>
                <a href="{{ route('additional.index') }}"
                   style="background: #f7f7fa; color: var(--main-color); font-weight: 600; border-radius: 12px; padding: 10px 24px; text-decoration: none; border: 1.5px solid var(--main-color); display: inline-flex; align-items: center;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back to Additionals
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
                :reactive_route="'additional.reactive'"
            />

        </div>
    </main>
</div>
@endsection
