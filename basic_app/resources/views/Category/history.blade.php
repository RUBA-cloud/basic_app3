@extends('adminlte::page')
@section('title', ' ' . __('adminlte::adminlte.category'))


@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Sidebar --}}
    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">{{-- Action Buttons --}}
            <x-action_buttons
            label="{{__('adminlte::adminlte.company_categories')}}"
                addRoute="categories.create"
                historyRoute="categories.index"
                :showAdd="false"
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


    <x-main_table
    :fields="$fields"
    :value="$categories"
    :search_route="'category-search-history'"
   :details_route="'categories.show'"
    :edit_route="'categories.edit'"
     :reactive_route="'reactive_category'"/>

        </div>

        </div>
    </main>
</div>
@endsection
