@extends('adminlte::page')

@section('title', __('adminlte::adminlte.offers'))

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}

           <x-action_buttons   label="{{__('adminlte::adminlte.offers_type')}}"
                addRoute="offers.create"
                historyRoute="offers.history"
                historyParams="true"
                :showAdd="true"
            />



        {{-- Additional Table Card --}}
        <div class="card">
            <div class="card-header">
   <h2 class="font-weight-bold text-dark">{{__('adminlte::adminlte.offers')}}</h2>            </div>

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
                    :value="$offers"
                    :details_route="'offers.show'"
                    :edit_route="'offers.edit'"
                    :delete_route="'offers.destroy'"
                    :reactive_route="'offers.reactive'"
                    :search_route="'offer.search'"
                />

            </div>
        </div>
    </div>
@endsection
