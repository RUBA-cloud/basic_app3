@extends('adminlte::page')

@section('title', ' ' . __('adminlte::adminlte.brands'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card-table" style="padding: 24px">

            {{-- Action Buttons --}}
            <x-action_buttons
                label="{{ __('adminlte::adminlte.brands') }}"
                addRoute="brands.create"
                historyRoute="brands.history"
                :showAdd="true"
            />

            {{-- Table Field Definitions --}}
            @php
                $fields = [
                    ['key' => 'image',          'label' => __('adminlte::adminlte.image'),       'type' => 'image'],
                    ['key' => 'name_en',         'label' => __('adminlte::adminlte.name_en')],
                    ['key' => 'name_ar',         'label' => __('adminlte::adminlte.name_ar')],
                    ['key' => 'company.name_en', 'label' => __('adminlte::adminlte.company_name')],
                    ['key' => 'is_active',       'label' => __('adminlte::adminlte.is_active'),   'type' => 'bool'],
                    ['key' => 'is_top',          'label' => __('adminlte::adminlte.is_top'),      'type' => 'bool'],
                    ['key' => 'user.name',       'label' => __('adminlte::adminlte.user_name')],
                    ['key' => 'user.id',         'label' => __('adminlte::adminlte.user_id')],
                ];
            @endphp

            <livewire:adminlte.data-table
                :fields="$fields"
                model="\App\Models\Brand"
                detailsRoute="brands.show"
                edit-route="brands.edit"
                delete-route="brands.destroy"
                reactive-route="brands.reactivate"
                initial-route="{{ route('brands.index') }}"
                :search-in="['name_en', 'name_ar']"
                :per-page="12"
            />

        </div>
    </main>

</div>
@endsection