@extends('adminlte::page')


@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-3" style="padding: 24px">
            <div class="col">
                <h2 class="font-weight-bold text-dark">{{__('adminlte::adminlte.size')}}</h2>
            </div>
           <x-action_buttons
                addRoute="sizes.create"
                historyRoute="sizes.history"
                historyParams="true"
                :showAdd="true"
            />
        </div>
        

        {{-- Sizes Table Card --}}
        <div class="card">
            <div class="card-header">
   <h2 class="font-weight-bold text-dark">{{__('adminlte::adminlte.size')}}</h2>            </div>

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
                    :value="$sizes"
                    :details_route="'sizes.show'"
                    :edit_route="'sizes.edit'"
                    :delete_route="'sizes.destroy'"
                    :reactive_route="'sizes.reactive'"
                    :search_route="'size_search'"
                />
            </div>
        </div>
    </div>
@endsection
