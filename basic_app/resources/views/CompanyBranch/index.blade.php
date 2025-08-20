@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_branch'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">


    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">{{ __('adminlte::adminlte.company_branch') }}</h2>

            {{-- Action Buttons --}}


          {{-- filepath: /Users/rubahammad/Desktop/basic_app3/basic_app/resources/views/CompanyBranch/index.blade.php --}}
<x-action_buttons
    addRoute="companyBranch.create"
    historyRoute="branches.index"
    :historyParams="['isHistory' => 'true']"
    :showAdd="true"
/>
            {{-- Define Table Fields --}}
            @php
                $fields = [
                    ['key' => 'name_en', 'label' => __('adminlte::adminlte.branch_name_en')],
                    ['key' => 'name_ar', 'label' => __('adminlte::adminlte.branch_name_ar')],
                    ['key' => 'email', 'label' => __('adminlte::adminlte.email')],
                    ['key' => 'address_en', 'label' => __('adminlte::adminlte.company_address_en')],
                    ['key' => 'address_ar', 'label' => __('adminlte::adminlte.company_address_ar')],
                    ['key' => 'is_active', 'label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => __('adminlte::adminlte.user_name')],
                    ['key' => 'user.id', 'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp

 {{-- Table Component --}}
 <x-main_table :fields="$fields" :value="$branches" :details_route="'companyBranch.show'"
    :edit_route="'companyBranch.edit'"
    :delete_route="'companyBranch.destroy'"
    :search_route="'compnyBranch_search'"
    :reactive_route="'reactive_branch'"/>
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 24px;">
            {{ $branches->links() }}
        </div>
    </main>
</div>
@endsection
