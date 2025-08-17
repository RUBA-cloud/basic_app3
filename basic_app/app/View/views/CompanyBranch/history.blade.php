@extends('adminlte::page')

@section('title', 'Company Branches')

@section('content')
<div style="min-height: 100vh; display: flex;">
    {{-- Sidebar --}}
    <x-sidebar />

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin: 0;">
                    Company Branches
                </h2>
                <a href="{{route('branches.index',false) }}"
                   style="background: #f7f7fa; color: #6C63FF; font-weight: 600; border-radius: 12px; padding: 10px 28px; text-decoration: none; border: 1.5px solid #6C63FF;">
                    <i class="fas fa-history" style="margin-right: 8px;"></i> Go Back
                </a>
            </div>

            {{-- Define Table Fields --}}
            @php
                $fields = [
                    ['key' => 'name_en', 'label' => 'Branch Name (EN)'],
                    ['key' => 'name_ar', 'label' => 'Branch Name (AR)'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'address_en', 'label' => 'Address (EN)'],
                    ['key' => 'address_ar', 'label' => 'Address (AR)'],
                    ['key' => 'is_main_branch', 'label' => 'Main Branch', 'type' => 'bool'],
                    ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => 'User Name'],
                    ['key' => 'user.id', 'label' => 'User ID'],
                ];
            @endphp

            {{-- Table Component --}}
<x-main_table :fields="$fields" :value="$branches" :details_route="'companyBranch.show'"

    :reactive_route="'reactive_branch'"/>

        </div>
        </div>
    </main>
</div>
@endsection
