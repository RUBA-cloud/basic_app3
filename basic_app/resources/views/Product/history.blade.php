@extends('adminlte::page')

@section('title',  ' ' . __('adminlte::adminlte.product'))

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}

           <x-action_buttons   label="{{__('adminlte::adminlte.product')}}"
                addRoute="product.create"
                historyRoute="product.index"
                historyParams="true"
                :showAdd="false"
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
            <livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\ProductHistory"       {{-- any Eloquent model --}}
        detailsRoute="product.show"   {{-- optional: blade partial for modal --}}
        editRoute="product.edit"        {{-- route names (optional) --}}
        deleteRsoute="product.destroy"   {{-- when set, delete uses form+route --}}
        reactiveRoute="product.reactivate"
        initial-route="{{ route('product.history') }}" {{-- will reload to here if called --}}
        :search-in="['name_en','name_ar','description_en','description_ar']"
        :per-page="12"
    />



            </div>
        </div>
    </div>
@endsection
