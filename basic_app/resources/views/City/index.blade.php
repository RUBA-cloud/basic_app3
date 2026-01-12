@extends('adminlte::page')

@section('title', __('adminlte::adminlte.company_branch'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card-table" style="padding: 24px">

            <x-action_buttons
                label="{{ __('adminlte::adminlte.city') }}"
                addRoute="cities.create"
                historyRoute="cities.history"
               :historyParams="true"
                :showAdd="true"
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
            model="\App\Models\city"                            {{-- Eloquent model --}}
            details-route="cities.show"                         {{-- route names --}}
                        edit-route="cities.edit"

            delete-route="cities.destroy"
            reactive-route="cities.reactivate"
            initial-route="{{ route('cities.index')}}"
            :search-in="['name_en','name_ar']"
            :pagination-in-table="true"                         {{-- 🔹 pagination in table --}}
        />

    </main>
</div>
@endsection
