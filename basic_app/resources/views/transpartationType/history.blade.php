@extends('adminlte::page')

@section('title', __('adminlte::adminlte.transpartation_type'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">
                {{ __('adminlte::adminlte.transpartation_type') }}
            </h2>

            {{-- Action Buttons --}}
            <x-action_buttons
                addRoute="transpartation_types.create"
                historyRoute="transpartation_types.index"
                :showAdd="false"
            />

            {{-- Table Field Definitions --}}
            @php
                $fields = [
                    ['key' => 'name_en',  'label' => __('adminlte::adminlte.name_en')],
                    ['key' => 'name_ar',  'label' => __('adminlte::adminlte.name_ar')],
                    ['key' => 'is_active','label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                    ['key' => 'user.name','label' => __('adminlte::adminlte.user_name')],
                    ['key' => 'user.id',  'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp

            <livewire:adminlte.data-table
                :fields="$fields"
                :model="\App\Models\TranspartationTypeHistory::class"
                detailsRoute="transpartation_types.show"
                edit-route="transpartation_types.edit"
                delete-route="transpartation_types.destroy"
                reactive-route="transpartation_types.reactivate"
                initial-route="{{ route('transpartation_types.index') }}"
                :search-in="['name_en','name_ar']"
                :per-page="12"
            />
        </div>
    </main>
</div>
@endsection
