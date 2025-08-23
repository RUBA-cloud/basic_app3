@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_branch'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">


    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card-table" style="padding: 24px">

<x-action_buttons label="{{ __('adminlte::adminlte.company_branch') }}"
    addRoute="companyBranch.create"
    historyRoute="branches.index"
    :historyParams="['isHistory' => 'true']"
    :showAdd="true"
/>
        </div>
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
    :search_route="'companyBranch_search'"
    :reactive_route="'reactive_branch'"/>
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 24px;">
            {{ $branches->links() }}
        </div>
    </main>
</div>
@endsection
