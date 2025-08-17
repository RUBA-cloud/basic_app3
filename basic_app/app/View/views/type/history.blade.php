@extends('adminlte::page')

@section('title', 'Company Types History')

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">


    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table" style="background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin: 0;">
                    Company Types History
                </h2>

                <a href="{{ route('type.index') }}"
                   style="
                       background: #f7f7fa;
                       color: #6C63FF;
                       font-weight: 600;
                       border-radius: 12px;
                       padding: 10px 20px;
                       text-decoration: none;
                       border: 1.5px solid #6C63FF;
                       display: flex;
                       align-items: center;
                   ">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Go Back
                </a>
            </div>

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
                :value="$types"
                :details_route="'type.show'"
                :reactive_route="'type.reactive'"
            />
        </div>
    </main>
</div>
@endsection
