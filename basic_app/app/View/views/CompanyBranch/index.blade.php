@extends('adminlte::page')

@section('title', 'Company Branches')

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">


    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">Company Branches</h2>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                <a href="{{ route('companyBranch.create') }}" style="background: #6C63FF; color: #fff; font-weight: 600; border-radius: 12px; padding: 10px 28px; text-decoration: none; box-shadow: 0 2px 8px 0 rgba(108,99,255,0.10);">
                    <i class="fas fa-plus" style="margin-right: 8px;"></i> Add
                </a>
                <a href="{{ route('branches.index', ['isHistory' => 'true']) }}"
                   style="background: #f7f7fa; color: #6C63FF; font-weight: 600; border-radius: 12px; padding: 10px 28px; text-decoration: none; border: 1.5px solid #6C63FF;">
                    <i class="fas fa-history" style="margin-right: 8px;"></i> History
                </a>
            </div>

            {{-- Define Table Fields --}}
            @php
                $fields = [
                    ['key' => 'name_en', 'label' => 'Branch Name (EN)'],
                    ['key' => 'name_ar', 'label' => 'Branch Name (AR)'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'address_en', 'label' => 'Address'],

                    ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => 'User Name'],
                    ['key' => 'user.id', 'label' => 'User Id '],

                ];
            @endphp

 {{-- Table Component --}}
 <x-main_table :fields="$fields" :value="$branches" :details_route="'companyBranch.show'"
    :edit_route="'companyBranch.edit'"
    :delete_route="'companyBranch.destroy'"
    :reactive_route="'reactive_branch'"/>

        </div>

        {{-- Pagination --}}
        <div style="margin-top: 24px;">
            {{ $branches->links() }}
        </div>
    </main>
</div>
@endsection
