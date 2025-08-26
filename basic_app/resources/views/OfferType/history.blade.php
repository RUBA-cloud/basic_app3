@extends('adminlte::page')


@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}

           <x-action_buttons   label="{{__('adminlte::adminlte.offers_type')}}"
                addRoute="offers_type.create"
                historyRoute="offers_type.index"
                historyParams="true"
                :showAdd="false"
            />


        {{-- Additional Table Card --}}
        <div class="card">
            <div class="card-header">
   <h2 class="font-weight-bold text-dark">{{__('adminlte::adminlte.offers_type')}}</h2>            </div>

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
                    :value="$offerTypes"
                    :details_route="'offers_type.show'"
                    :edit_route="'offers_type.edit'"
                    :delete_route="'offers_type.destroy'"
                    :reactive_route="'offers_type.reactive'"
                    :search_route="'offer_type.search_history'"
                />

            </div>
        </div>
    </div>
@endsection
