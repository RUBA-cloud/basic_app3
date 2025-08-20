@extends('adminlte::page')


@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Sidebar --}}
    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">{{__('adminlte::adminlte.comapny_category')}}</h2>


{{-- Action Buttons --}}
            <x-action_buttons
                addRoute="categories.create"
                historyRoute="category_history"
                :showAdd="true"
            />

            {{-- Table Field Definitions --}}
            @php
                $fields = [
                     ['key' => 'name_en', 'label' => __('adminlte::adminlte.name_en')],
                    ['key' => 'name_ar', 'label' => __('adminlte::adminlte.name_ar')],
                    ['key' => 'is_active', 'label' => __('adminlte::adminlte.active'), 'type' => 'bool'],
                    ['key' => 'user.name', 'label' => __('adminlte::adminlte.user_name')],
                    ['key' => 'user.id', 'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp

            {{-- Main Table Component --}}
            <x-main_table :fields="$fields" :value="$categories" :details_route="'categories.show'"
    :edit_route="'categories.edit'"
    :delete_route="'categories.destroy'"
    :reactive_route="'reactive_category'"/>
        </div>
    </main>
</div>
@endsection
