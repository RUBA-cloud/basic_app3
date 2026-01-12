@extends('adminlte::page')

@section('title', __('adminlte::adminlte.country_history'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card-table" style="padding: 24px">

            <x-action_buttons
                label="{{ __('adminlte::adminlte.country') }}"
                addRoute="countries.index"
                historyRoute="countries.index"
               :historyParams="false"
                :showAdd="false"
            />
        </div>

        {{-- Define Table Fields --}}
        @php
            $fields = [
                [
                    'key'   => 'name_en',
                    'label' => __('adminlte::adminlte.name_en'),
                ],
                [
                    'key'   => 'name_ar',
                    'label' => __('adminlte::adminlte.name_ar'),
                ],
                [
                    'key'   => 'is_active',
                    'label' => __('adminlte::adminlte.active'),
                    'type'  => 'bool',
                ],
                [
                    'key'   => 'user.name',
                    'label' => __('adminlte::adminlte.user_name'),
                ],
                [
                    'key'   => 'user.id',
                    'label' => __('adminlte::adminlte.user_id'),
                ],
            ];
        @endphp

        {{-- Table Component --}}
        <livewire:adminlte.data-table
            :fields="$fields"                                   {{-- columns --}}
            model="\App\Models\CountryHistory"                   {{-- Eloquent model --}}
            details-route="countries.show"                  {{-- route names --}}
            edit-route="countries.edit"
            delete-route="countries.destroy"
            reactive-route="countries.reactivate"
            initial-route="{{ route('countries.index') }}"
            :search-in="['name_en','name_ar']"
            :pagination-in-table="true"                         {{-- 🔹 pagination in table --}}
        />

    </main>
</div>
@endsection
