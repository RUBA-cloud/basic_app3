@extends('adminlte::page')

@section('title', __('adminlte::adminlte.type') )

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}

           <x-action_buttons   label="{{__('adminlte::adminlte.type')}}"
                addRoute="type.create"
                historyRoute="type.history"
                historyParams="true"
                :showAdd="true"
            />


        {{-- Sizes Table Card --}}
        <div class="card">
            <div class="card-header">
   <h2 class="font-weight-bold text-dark">{{__('adminlte::adminlte.type')}}</h2>            </div>

            <div class="card-body table-responsive p-0">
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
                    :value="$types"
                    :details_route="'type.show'"
                    :edit_route="'type.edit'"
                    :delete_route="'type.destroy'"
                    :reactive_route="'type.reactive'"
                    :search_route="'type.search'"
                />

            </div>
        </div>
    </div>
@endsection
