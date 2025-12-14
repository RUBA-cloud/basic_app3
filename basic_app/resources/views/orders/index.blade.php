{{-- resources/views/orders/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::menu.orders'))

@section('content')
<div class="card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="card-title mb-0">{{ __('adminlte::menu.orders') }}</h3>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      <x-action_buttons
          label="{{ __('adminlte::adminlte.orders') }}"
          addRoute="orders.create"
          historyRoute="orders.history"
          :showAdd="false"
          :goBack="false"
      />

      {{-- Status Filter --}}
      <form class="d-flex" method="GET" action="{{ url()->current() }}">
        <select class="form-control" name="status" onchange="this.form.submit()">
          <option value="">{{ __('adminlte::adminlte.all') }}</option>

          @isset($orderStatus)
            @foreach($orderStatus as $st)
              <option value="{{ $st->id }}" @selected((string)request('status') === (string)$st->id)>
                {{ app()->isLocale('ar') ? ($st->name_ar ?? $st->name_en) : ($st->name_en ?? $st->name_ar) }}
              </option>
            @endforeach
          @endisset
        </select>

        {{-- keep other query params if you have any --}}
        @foreach(request()->except('status', 'page') as $k => $v)
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
      </form>
    </div>
  </div>

  <div class="card-body">
    @php
      $fields = [
          [
              'key'   => app()->isLocale('ar') ? 'statusRel.name_ar' : 'statusRel.name_en',
              'label' => __('adminlte::adminlte.status') ?: 'Status',
          ],
          ['key' => 'user.name',     'label' => __('adminlte::adminlte.user_name') ?: 'User Name'],
          ['key' => 'offer.name_en', 'label' => (__('adminlte::adminlte.offer_name') ?: 'Offer Name').' (EN)'],
          ['key' => 'offer.name_ar', 'label' => (__('adminlte::adminlte.offer_name') ?: 'Offer Name').' (AR)'],
          ['key' => 'user.id',       'label' => __('adminlte::adminlte.user_id') ?: 'User ID'],
      ];
    @endphp

    {{-- Table Component --}}
    <livewire:adminlte.data-table
        :fields="$fields"
        :model="\App\Models\Order::class"
        details-route="orders.show"
        edit-route="orders.edit"
        delete-route="orders.destroy"
        reactive-route="orders.reactivate"
        initial-route="{{ request()->fullUrl() }}"
        :search-in="['id']"
        :per-page="12"
        :filters="['status' => request('status')]"
    />
  </div>
</div>
@endsection
