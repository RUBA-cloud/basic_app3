@extends('adminlte::page')

@section('title', ' ' . __('adminlte::adminlte.category'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">
    {{-- Sidebar --}}
    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card-table" style="padding: 24px">
            {{-- Action Buttons --}}
            <x-action_buttons
            label="{{__('adminlte::adminlte.company_categories')}}"
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

    <livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\Category"       {{-- any Eloquent model --}}
        detailsRoute="categories.show"   {{-- optional: blade partial for modal --}}
        edit-route="categories.edit"        {{-- route names (optional) --}}
        delete-route="categories.destroy"   {{-- when set, delete uses form+route --}}
        reactive-route="categories.reactivate"
        initial-route="{{ route('categories.index') }}" {{-- will reload to here if called --}}
        :search-in="['name_en','name_ar']"
        :per-page="12"
    />
        </div>

        </div>
    </main>
</div>

@php
    // Prefer config() here (env() is for config files)
    $broadcast = $broadcast ?? [
        'channel'        => 'categories',
        'events'         => ['category_updated'],
        'pusher_key'     => config('broadcasting.connections.pusher.key'),
        'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster', 'mt1'),
    ];
@endphp
@endsection
