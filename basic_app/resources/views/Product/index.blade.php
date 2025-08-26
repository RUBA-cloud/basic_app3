@extends('adminlte::page')

@section('title', __('adminlte::adminlte.product'))

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}

           <x-action_buttons   label="{{__('adminlte::adminlte.product')}}"
                addRoute="product.create"
                historyRoute="product.history"
                historyParams="true"
                :showAdd="true"
            />


        {{-- Additional Table Card --}}
        <div class="card">
            <div class="card-header">
   <h2 class="font-weight-bold text-dark">{{__('adminlte::adminlte.product')}}</h2>            </div>

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
                    :value="$products"
                    :details_route="'product.show'"
                    :edit_route="'product.edit'"
                    :delete_route="'product.destroy'"
                    :reactive_route="'product.reactive'"
                    :search_route="'product.search'"
                />

            </div>
        </div>
    </div>
@endsection
