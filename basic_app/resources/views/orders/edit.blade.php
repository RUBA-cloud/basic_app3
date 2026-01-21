@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit_order') . " #{$order->id}")

@php
  $isAr = app()->isLocale('ar');

  // Status values
  $PENDING  = '0';
  $ACCEPTED = '1';
  $REJECTED = '2';
  $SHIPPED  = '3';

  // Defaults from relations (safe)
  $employeeCountryId = (string) (optional($order->employee)->country_id ?? '');
  $employeeCityId    = (string) (optional($order->employee)->city_id ?? '');

  $userCountryId     = (string) (optional($order->user)->country_id ?? '');
  $userCityId        = (string) (optional($order->user)->city_id ?? '');

  // old -> order -> relation defaults
  $oldStatus    = (string) old('status', $order->status ?? $PENDING);
  $oldEmployee  = (string) old('employee_id', $order->employee_id ?? '');

  $fromCountry  = (string) old('from_country_id', $order->from_country_id ?? $employeeCountryId);
  $fromCity     = (string) old('from_city_id',    $order->from_city_id    ?? $employeeCityId);

  $toCountry    = (string) old('to_country_id',   $order->to_country_id   ?? $userCountryId);
  $toCity       = (string) old('to_city_id',      $order->to_city_id      ?? $userCityId);

  // ✅ one way only
  $wayId        = (string) old('transportation_way_id', $order->transportation_way_id ?? $order->way_id ?? '');
  $daysCount    = (string) old('days_count', $order->days_count ?? '');
  $rejectReason = (string) old('reject_reason', $order->reject_reason ?? '');
@endphp

@section('content')

<div class="container-fluid">

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
      <div class="section-title">
        <i class="fas fa-edit"></i>
        <span>{{ __('adminlte::adminlte.edit_order') ?? 'Edit Order' }} #{{ $order->id }}</span>
      </div>
      <div class="mt-2 d-flex flex-wrap gap-2">
        <span class="pill"><i class="fas fa-user"></i> {{ $order->user->name ?? 'User' }}</span>
        <span class="pill"><i class="fas fa-calendar-alt"></i> {{ optional($order->created_at)->format('Y-m-d H:i') }}</span>
        @if($order->offer)
          <span class="pill"><i class="fas fa-tag"></i>
            {{ $isAr ? ($order->offer->name_ar ?? $order->offer->name_en) : ($order->offer->name_en ?? $order->offer->name_ar) }}
          </span>
        @endif
      </div>
    </div>

    <a href="{{ route('orders.show',$order) }}" class="btn btn-outline-secondary" style="border-radius:14px">
      <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') ?? 'Back' }}
    </a>
  </div>

  <form method="POST" action="{{ route('orders.update',$order) }}" id="order-edit-form">
    @csrf
    @method('PUT')

    {{-- ✅ Hidden defaults used by JS --}}
    <input type="hidden" id="employee_country_id_default" value="{{ $employeeCountryId }}">
    <input type="hidden" id="employee_city_id_default" value="{{ $employeeCityId }}">
    <input type="hidden" id="user_country_id_default" value="{{ $userCountryId }}">
    <input type="hidden" id="user_city_id_default" value="{{ $userCityId }}">

    <div class="card glass-card mb-3">
      <div class="card-body">

        {{-- Notes --}}
        <div class="mb-3">
          <label class="font-weight-bold">{{ __('adminlte::adminlte.notes') ?? 'Notes' }}</label>
          <textarea name="notes" class="form-control soft-field" rows="3">{{ old('notes',$order->notes) }}</textarea>
        </div>

        <div class="row">
          {{-- Status --}}
          <div class="col-lg-6 mb-3">
            <label class="font-weight-bold">{{ __('adminlte::adminlte.status') ?? 'Status' }}</label>
            <select name="status" id="status" class="form-control soft-field" required>
              <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
              @foreach($orderStatus as $st)
                @php $label = $isAr ? ($st->name_ar ?? $st->name_en) : ($st->name_en ?? $st->name_ar); @endphp
                <option value="{{ $st->status }}" @selected((string)$oldStatus === (string)$st->status)>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          {{-- Employee (Accepted only) --}}
          <div class="col-lg-6 mb-3" id="employeeBox" style="display:none;">
            <label class="font-weight-bold">{{ __('adminlte::adminlte.assign_employee') ?? 'Assign Employee' }}</label>
            <select name="employee_id" id="employee_id" class="form-control soft-field">
              <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}"
                        data-country="{{ $emp->country_id ?? '' }}"
                        data-city="{{ $emp->city_id ?? '' }}"
                        @selected((string)$oldEmployee === (string)$emp->id)>
                  {{ $emp->name }}
                </option>
              @endforeach
            </select>
            <div class="subtle mt-2">{{ __('adminlte::adminlte.required_when_accepted') ?? 'Required when accepted' }}</div>
          </div>
        </div>

        {{-- Rejected reason --}}
        <div class="mb-3" id="rejectBox" style="display:none;">
          <label class="font-weight-bold">{{ __('adminlte::adminlte.reject_reason') ?? 'Reject reason' }}</label>
          <textarea name="reject_reason" id="reject_reason" class="form-control soft-field" rows="3">{{ $rejectReason }}</textarea>
          <div class="subtle mt-2">{{ __('adminlte::adminlte.required_when_rejected') ?? 'Required when rejected' }}</div>
        </div>

        {{-- Shipment --}}
        <div id="shipmentBox" style="display:none;">
          <hr class="my-3">
          <div class="section-title mb-2"><i class="fas fa-truck"></i> {{ __('adminlte::adminlte.shipment') ?? 'Shipment' }}</div>

          <div class="row">
            {{-- FROM --}}
            <div class="col-lg-6 mb-3">
              <div class="mini-card">
                <x-country-city
                  prefix="from"
                  :countries="$countries"
                  :cities="$cities"
                  :isAr="$isAr"
                  countryName="from_country_id"
                  cityName="from_city_id"
                  countryId="from_country_id"
                  cityId="from_city_id"
                  :selectedCountry="$fromCountry"
                  :selectedCity="$fromCity"
                  :locked="false"
                  title="{{ __('adminlte::adminlte.from') ?? 'From' }}"
                  :required="false"
                />
              </div>
            </div>

            {{-- TO --}}
            <div class="col-lg-6 mb-3">
              <div class="mini-card">
                <x-country-city
                  prefix="to"
                  :countries="$countries"
                  :cities="$cities"
                  :isAr="$isAr"
                  countryName="to_country_id"
                  cityName="to_city_id"
                  countryId="to_country_id"
                  cityId="to_city_id"
                  :selectedCountry="$toCountry"
                  :selectedCity="$toCity"
                  :locked="false"
                  title="{{ __('adminlte::adminlte.to') ?? 'To' }}"
                  :required="false"
                />

                {{-- ✅ ONE Transportation Way --}}
                <div class="mb-2 mt-3">
                  <label class="subtle">{{ __('adminlte::adminlte.transportation_way') ?? 'Transportation Way' }}</label>

                  <select name="transpartation_id" id="transportation_way_id"
                          class="form-control soft-field"
                          data-selected="{{ $wayId }}">
                    <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
                  </select>

                  <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="badge badge-info badge-soft" id="daysBadge" style="display:none;"></span>
                    <span class="badge badge-primary badge-soft" id="typeBadge" style="display:none;"></span>
                  </div>

                  <input type="hidden" name="days_count" id="days_count" value="{{ $daysCount }}">
                </div>

              </div>
            </div>
          </div>

          <div class="subtle mt-2">
            {{ __('adminlte::adminlte.required_when_shipped') ?? 'Required when shipped' }}
          </div>
        </div>

      </div>

      <div class="card-footer bg d-flex flex-wrap gap-3" >
        <button class="btn btn-primary" style="border-radius:14px" type="submit">
          <i class="fas fa-save mr-1"></i> {{ __('adminlte::adminlte.save') ?? 'Save' }}
        </button>
        <a class="btn btn-outline-secondary" style="border-radius:14px" href="{{ route('orders.show',$order) }}">
          {{ __('adminlte::adminlte.cancel') ?? 'Cancel' }}
        </a>
      </div>
    </div>

  </form>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isAr = document.documentElement.getAttribute('lang') === 'ar';

  const PENDING  = @json($PENDING);
  const ACCEPTED = @json($ACCEPTED);
  const REJECTED = @json($REJECTED);
  const SHIPPED  = @json($SHIPPED);

  const tSelect = @json(__('adminlte::adminlte.select') ?? 'Select');

  const statusSel   = document.getElementById('status');
  const employeeBox = document.getElementById('employeeBox');
  const empSel      = document.getElementById('employee_id');

  const rejectBox   = document.getElementById('rejectBox');
  const rejectTxt   = document.getElementById('reject_reason');

  const shipmentBox = document.getElementById('shipmentBox');

  const fromCountry = document.getElementById('from_country_id');
  const fromCity    = document.getElementById('from_city_id');
  const toCountry   = document.getElementById('to_country_id');
  const toCity      = document.getElementById('to_city_id');

  // ✅ one way only
  const waySel     = document.getElementById('transportation_way_id');
  const daysH      = document.getElementById('days_count');
  const daysB      = document.getElementById('daysBadge');
  const typeB      = document.getElementById('typeBadge');

  const empCountryDefault  = document.getElementById('employee_country_id_default')?.value || '';
  const empCityDefault     = document.getElementById('employee_city_id_default')?.value || '';
  const userCountryDefault = document.getElementById('user_country_id_default')?.value || '';
  const userCityDefault    = document.getElementById('user_city_id_default')?.value || '';

  function show(el){ if(el) el.style.display=''; }
  function hide(el){ if(el) el.style.display='none'; }

  function resetSelect(sel){
    if(!sel) return;
    sel.innerHTML = `<option value="">${tSelect}</option>`;
  }

  function setRequiredForShipment(on){
    [fromCountry, fromCity, toCountry, toCity, waySel].forEach(el => { if(el) el.required = !!on; });
  }

  function lockShipmentAddresses(lock){
    [fromCountry, fromCity, toCountry, toCity].forEach(el => {
      if(!el) return;
      el.disabled = !!lock;
      el.classList.toggle('bg-light', !!lock);
    });
  }

  function getSelectedEmployeeCountryCity(){
    const opt = empSel?.options?.[empSel.selectedIndex];
    const cId  = String(opt?.dataset?.country || '');
    const ctId = String(opt?.dataset?.city || '');
    return { cId, ctId };
  }

  async function loadCities(countryId, citySelect){
    resetSelect(citySelect);
    if(!countryId) return;

    const url = @json(route('countries.cities', ['country' => '___ID___']))
      .replace('___ID___', encodeURIComponent(countryId));

    const selected = String(citySelect.dataset.selected || '');

    try{
      const res = await fetch(url, {
        headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
        cache:'no-store'
      });
      if(!res.ok) return;

      const json = await res.json();
      const list = Array.isArray(json?.data) ? json.data : [];

      list.forEach(c=>{
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = isAr ? (c.name_ar || c.name_en || '') : (c.name_en || c.name_ar || '');
        if(selected && String(c.id) === selected) opt.selected = true;
        citySelect.appendChild(opt);
      });
    }catch(e){ console.warn('loadCities', e); }
  }

  // ✅ Decide which (country_id, city_id) to search with:
  // - if different countries -> use FROM as source
  // - if same country -> still use FROM city (because endpoint accepts one city)
  function getSearchPair(){
    const fc = String(fromCountry?.value || '');
    const tc = String(toCountry?.value || '');
    const fct = String(fromCity?.value || '');
    const tct = String(toCity?.value || '');

    if(!fc) return { countryId:'', cityId:'' };

    // different countries => source side
    if(fc && tc && fc !== tc){
      return { countryId: fc, cityId: fct || '' };
    }

    // same country => "from city -> to city" (endpoint limitation)
    // so we query by FROM city; you can change to tct if your data is destination-based.
    return { countryId: fc, cityId: fct || tct || '' };
  }

  async function loadWaysOne(){
    resetSelect(waySel);

    const { countryId, cityId } = getSearchPair();
    if(!countryId || !cityId) return;

    const selected = String(waySel.dataset.selected || '');

    const url = new URL(@json(route('transportationWays.search')), window.location.origin);
    url.searchParams.set('country_id', countryId);
    url.searchParams.set('city_id', cityId);

    try{
      const res = await fetch(url.toString(), {
        headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
        cache:'no-store'
      });
      if(!res.ok) return;

      const json = await res.json();
      const list = Array.isArray(json?.data) ? json.data : [];

      list.forEach(w=>{
        const opt = document.createElement('option');
        opt.value = w.id;

        const name = isAr ? (w.name_ar || w.name_en || '') : (w.name_en || w.name_ar || '');
        opt.textContent = name;

        opt.dataset.days = w.days_count ?? 0;

        const typeName =
          (isAr ? (w.type?.name_ar || w.transpartation_type?.name_ar || w.transpartationType?.name_ar) :
                  (w.type?.name_en || w.transpartation_type?.name_en || w.transpartationType?.name_en))
          || '';

        opt.dataset.type = typeName;

        if(selected && String(w.id) === selected) opt.selected = true;
        waySel.appendChild(opt);
      });

    }catch(e){ console.warn('loadWaysOne', e); }
  }

  function updateWayBadges(){
    const opt = waySel?.options?.[waySel.selectedIndex];
    if(!opt || !opt.value){
      hide(daysB); hide(typeB);
      if(daysH) daysH.value = '';
      return;
    }

    const days = String(opt.dataset.days || '0');
    const type = String(opt.dataset.type || '');

    if(daysH) daysH.value = days;

    daysB.textContent = (isAr ? 'الأيام: ' : 'Days: ') + days;
    show(daysB);

    if(type){
      typeB.textContent = (isAr ? 'النوع: ' : 'Type: ') + type;
      show(typeB);
    }else{
      hide(typeB);
    }
  }

  async function applyShippedDefaults(){
    const emp = getSelectedEmployeeCountryCity();
    const fromC  = emp.cId  || empCountryDefault;
    const fromCt = emp.ctId || empCityDefault;

    const toC  = userCountryDefault;
    const toCt = userCityDefault;

    // from
    if(fromC){
      fromCountry.value = fromC;
      fromCity.dataset.selected = fromCt || '';
      await loadCities(fromC, fromCity);
    }else{
      resetSelect(fromCity);
    }

    // to
    if(toC){
      toCountry.value = toC;
      toCity.dataset.selected = toCt || '';
      await loadCities(toC, toCity);
    }else{
      resetSelect(toCity);
    }

    // load one way based on logic
    await loadWaysOne();
    updateWayBadges();

    lockShipmentAddresses(true);
  }

  async function initShipmentCurrentValues(){
    const fromC = String(fromCountry?.value || fromCountry?.dataset?.selected || '');
    const toC   = String(toCountry?.value   || toCountry?.dataset?.selected   || '');

    if(fromC) await loadCities(fromC, fromCity);
    if(toC)   await loadCities(toC, toCity);

    await loadWaysOne();
    updateWayBadges();
  }

  async function toggleBlocks(){
    const v = String(statusSel.value || '');

    const accepted = (v === ACCEPTED);
    employeeBox.style.display = accepted ? '' : 'none';
    if(empSel) empSel.required = accepted;

    const rejected = (v === REJECTED);
    rejectBox.style.display = rejected ? '' : 'none';
    if(rejectTxt) rejectTxt.required = rejected;

    const shipped = (v === SHIPPED);
    if(shipped){
      shipmentBox.style.display = '';
      setRequiredForShipment(true);
      await applyShippedDefaults();
    }else{
      shipmentBox.style.display = 'none';
      setRequiredForShipment(false);
      lockShipmentAddresses(false);
      await initShipmentCurrentValues();
    }
  }

  // change events
  fromCountry?.addEventListener('change', async () => {
    fromCity.dataset.selected = '';
    await loadCities(fromCountry.value, fromCity);
    waySel.dataset.selected = '';
    await loadWaysOne();
    updateWayBadges();
  });

  toCountry?.addEventListener('change', async () => {
    toCity.dataset.selected = '';
    await loadCities(toCountry.value, toCity);
    waySel.dataset.selected = '';
    await loadWaysOne();
    updateWayBadges();
  });

  fromCity?.addEventListener('change', async () => {
    waySel.dataset.selected = '';
    await loadWaysOne();
    updateWayBadges();
  });

  toCity?.addEventListener('change', async () => {
    waySel.dataset.selected = '';
    await loadWaysOne();
    updateWayBadges();
  });

  waySel?.addEventListener('change', updateWayBadges);

  statusSel?.addEventListener('change', () => toggleBlocks());

  empSel?.addEventListener('change', async () => {
    if(String(statusSel.value || '') !== SHIPPED) return;
    await applyShippedDefaults();
  });

  // init
  toggleBlocks();
});
</script>
@endpush
