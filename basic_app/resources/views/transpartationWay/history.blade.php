@extends('adminlte::page')

@section('title', __('adminlte::adminlte.transpartation_way'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">
                {{ __('adminlte::adminlte.transpartation_way') }}
            </h2>

            {{-- Action Buttons --}}
            <x-action_buttons
                addRoute="transpartation_ways.create"
                historyRoute="transpartation_ways.index"
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
                :model="\App\Models\TraspartationWayHistory::class"
                detailsRoute="transpartation_ways.show"
                edit-route="transpartation_ways.edit"
                delete-route="transpartation_ways.destroy"
                reactive-route="transpartation_ways.reactive"
                initial-route="{{ route('transpartation_ways.index') }}"
                :search-in="['name_en','name_ar']"
                :per-page="12"
            />
        </div>
    </main>
</div>
@endsection
