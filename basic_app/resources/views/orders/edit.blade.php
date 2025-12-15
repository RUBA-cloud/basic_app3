{{-- resources/views/orders/edit.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit_order') . " #{$order->id}")

@section('adminlte_css')

@section('content')
<div class="order-edit-wrap">

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="order-hero">
    <div class="title">
      <div>
        <h2>{{ __('adminlte::adminlte.edit') }} #{{ $order->id }}</h2>
        <div class="meta">
          <span class="pill"><i class="fas fa-user"></i> {{ $order->user->name ?? __('adminlte::adminlte.user') }}</span>
          <span class="pill"><i class="fas fa-calendar-alt"></i> {{ optional($order->created_at)->format('Y-m-d H:i') }}</span>
          @if($order->offer)
            <span class="pill">
              <i class="fas fa-tag"></i>
              {{ app()->isLocale('ar') ? ($order->offer->name_ar ?? $order->offer->name_en) : ($order->offer->name_en ?? $order->offer->name_ar) }}
            </span>
          @endif
        </div>
      </div>

      <a href="{{ route('orders.show',$order) }}" class="btn btn-outline-secondary btn-round">
        <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
      </a>
    </div>
  </div>

  <form method="POST" action="{{ route('orders.update',$order) }}">
    @csrf
    @method('PUT')

    <div class="card glass-card mb-3">
      <div class="card-header">
        <span><i class="fas fa-receipt mr-2"></i>{{ __('adminlte::adminlte.order') }}</span>
        <p class="hint">{{ __('adminlte::adminlte.update_information') }}</p>
      </div>

      <div class="section">
        <label class="form-label">{{ __('adminlte::adminlte.notes') }}</label>
        <textarea name="notes" class="form-control soft-field" rows="4">{{ old('notes',$order->notes) }}</textarea>
      </div>

      <div class="section">
        <div class="grid two">
          <div>
            <label class="form-label">{{ __('adminlte::adminlte.status') }}</label>
            <select name="status" id="status" class="form-control soft-field" required>
              <option value="">{{ __('adminlte::adminlte.select') }}</option>
              @foreach($orderStatus as $st)
                @php
                  $label = app()->isLocale('ar') ? ($st->name_ar ?? $st->name_en) : ($st->name_en ?? $st->name_ar);
                @endphp
                <option value="{{ $st->status }}"
                  @selected((string)old('status',$order->status) === (string)$st->status)>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>

          <div id="employeeContainer" style="display:none;">
            <label class="form-label">{{ __('adminlte::adminlte.assign_employee') }}</label>
            <select name="employee_id" id="employee_id" class="form-control soft-field">
              <option value="">{{ __('adminlte::adminlte.select') }}</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}"
                  @selected((string)old('employee_id',$order->employee_id) === (string)$emp->id)>
                  {{ $emp->name }}
                </option>
              @endforeach
            </select>
            <small class="text-muted d-block mt-2">
              {{ __('adminlte::adminlte.required_when_accepted') }}
            </small>
          </div>
        </div>

        <div class="grid mt-3">
          <div id="rejectReasonContainer" style="display:none;">
            <label class="form-label">{{ __('adminlte::adminlte.reject_reason') }}</label>
            <textarea name="reject_reason" id="reject_reason" class="form-control soft-field" rows="4">{{ old('reject_reason', $order->reject_reason ?? '') }}</textarea>
            <small class="text-muted d-block mt-2">
              {{ __('adminlte::adminlte.required_when_rejected') }}
            </small>
          </div>
        </div>

        {{-- ✅ Shipment (Region -> Cities via fetch) --}}
        <div class="grid mt-3">
          <div id="shipmentContainer" style="display:none;">
            <label class="form-label">{{ __('adminlte::adminlte.shipment') }}</label>

            <div class="row">
              {{-- FROM --}}
              <div class="col-lg-6">
                <div class="mb-2 font-weight-bold">{{ __('adminlte::adminlte.from') }}</div>

                <div class="mb-3">
                  <label class="form-label">{{ __('adminlte::adminlte.region') }}</label>
                  <select name="from_region_id" id="from_region_id" class="form-control soft-field">
                    <option value="">{{ __('adminlte::adminlte.select') }}</option>
                    @foreach($regions as $r)
                      @php
                        $rLabel = app()->isLocale('ar')
                          ? ($r->name_ar ?? $r->name_en ?? $r->country_ar ?? $r->country_en ?? $r->name ?? '')
                          : ($r->name_en ?? $r->name_ar ?? $r->country_en ?? $r->country_ar ?? $r->name ?? '');
                      @endphp
                      <option value="{{ $r->id }}"
                        @selected((string)old('from_region_id', $order->from_region_id ?? '') === (string)$r->id)>
                        {{ $rLabel }}
                      </option>
                    @endforeach
                  </select>
                </div>



              {{-- TO --}}
              <div class="col-lg-6">
                <div class="mb-2 font-weight-bold">{{ __('adminlte::adminlte.to') }}</div>

                <div class="mb-3">
                  <label class="form-label">{{ __('adminlte::adminlte.region') }}</label>
                  <select name="to_region_id" id="to_region_id" class="form-control soft-field">
                    <option value="">{{ __('adminlte::adminlte.select') }}</option>
                    @foreach($regions as $r)
                      @php
                        $rLabel = app()->isLocale('ar')
                          ? ($r->name_ar ?? $r->name_en ?? $r->country_ar ?? $r->country_en ?? $r->name ?? '')
                          : ($r->name_en ?? $r->name_ar ?? $r->country_en ?? $r->country_ar ?? $r->name ?? '');
                      @endphp
                      <option value="{{ $r->id }}"
                        @selected((string)old('to_region_id', $order->to_region_id ?? '') === (string)$r->id)>
                        {{ $rLabel }}
                      </option>
                    @endforeach
                  </select>
                </div>



            <small class="text-muted d-block mt-2">
              {{ __('adminlte::adminlte.required_when_shipped') }}
            </small>
          </div>
        </div>

      </div>

      <div class="actions">
        <button class="btn btn-primary btn-round" type="submit">
          <i class="fas fa-save mr-1"></i> {{ __('adminlte::adminlte.save') }}
        </button>
        <a class="btn btn-outline-secondary btn-round" href="{{ route('orders.show',$order) }}">
          {{ __('adminlte::adminlte.cancel') }}
        </a>
      </div>
    </div>
  </form>

</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const statusSel = document.getElementById('status');
  const empBox    = document.getElementById('employeeContainer');
  const empSel    = document.getElementById('employee_id');
  const rejectBox = document.getElementById('rejectReasonContainer');
  const rejectTxt = document.getElementById('reject_reason');

  const shipBox    = document.getElementById('shipmentContainer');
  const fromRegion = document.getElementById('from_region_id');
  const fromCity   = document.getElementById('from_city_id');
  const toRegion   = document.getElementById('to_region_id');
  const toCity     = document.getElementById('to_city_id');

  const ACCEPTED_ID = '1';
  const REJECTED_ID = '2';
  const SHIPPED_ID  = '3';

  function show(el){ if(!el) return; el.style.display=''; el.classList.add('fade-in'); setTimeout(()=>el.classList.remove('fade-in'), 200); }
  function hide(el){ if(!el) return; el.style.display='none'; }


  function toggleFields(){
    const v = String(statusSel.value || '');

    const isAccepted = (v === ACCEPTED_ID);
    if (isAccepted) show(empBox); else { hide(empBox); if(empSel) empSel.value=''; }
    if (empSel) empSel.required = isAccepted;

    const isRejected = (v === REJECTED_ID);
    if (isRejected) show(rejectBox); else { hide(rejectBox); if(rejectTxt) rejectTxt.value=''; }
    if (rejectTxt) rejectTxt.required = isRejected;

    const isShipped = (v === SHIPPED_ID);
    if (isShipped) show(shipBox); else {
      hide(shipBox);
      if(fromRegion) fromRegion.value = '';
      if(toRegion)   toRegion.value   = '';
      if(fromCity)   fromCity.innerHTML = `<option value="">{{ __('adminlte::adminlte.select') }}</option>`;
      if(toCity)     toCity.innerHTML   = `<option value="">{{ __('adminlte::adminlte.select') }}</option>`;
    }

    if (fromRegion) fromRegion.required = isShipped;
    if (fromCity)   fromCity.required   = isShipped;
    if (toRegion)   toRegion.required   = isShipped;
    if (toCity)     toCity.required     = isShipped;
  }

  if (fromRegion) fromRegion.addEventListener('change', () => loadCities(fromRegion.value, fromCity, ''));
  if (toRegion)   toRegion.addEventListener('change',   () => loadCities(toRegion.value, toCity, ''));

  statusSel.addEventListener('change', toggleFields);
  toggleFields();

  const oldFromCity = @json(old('from_city_id', $order->from_city_id ?? ''));
  const oldToCity   = @json(old('to_city_id',   $order->to_city_id ?? ''));

  if (fromRegion && fromRegion.value) loadCities(fromRegion.value, fromCity, oldFromCity);
  if (toRegion && toRegion.value)     loadCities(toRegion.value, toCity, oldToCity);
});
</script>
@endpush
