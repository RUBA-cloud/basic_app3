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

                    ['key' => 'is_active', 'label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => __('adminlte::adminlte.user_name')],
                    ['key' => 'user.id', 'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp

 {{-- Table Component --}}

    <livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\companyBranch"       {{-- any Eloquent model --}}
        detailsRoute="companyBranch.show"   {{-- optional: blade partial for modal --}}
        editRoute="companyBranch.edit"        {{-- route names (optional) --}}
        deleteRoute="companyBranch.destroy"   {{-- when set, delete uses form+route --}}
        reactiveRoute="companyBranch.reactivate"
        initial-route="{{ route('companyBranch.index') }}" {{-- will reload to here if called --}}
        :search-in="['name_en','name_ar','email','company_address_en','company_address_ar']"
        :per-page="12"
                routeParamName="additional"

    />
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 24px;">
            {{ $branches->links() }}
        </div>
    </main>
</div>
@endsection
