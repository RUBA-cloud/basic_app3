@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit_order') . " #{$order->id}")

@php
  $isAr = app()->isLocale('ar');

  $PENDING   = '0';
  $ACCEPTED  = '1';
  $REJECTED  = '2';
  $SHIPPED   = '3';
  $canceled  = '4';
  $delivered = '5';

  $employeeCountryId = (string)(optional($order->employee)->country_id ?? '');
  $employeeCityId    = (string)(optional($order->employee)->city_id    ?? '');
  $userCountryId     = (string)(optional($order->user)->country_id     ?? '');
  $userCityId        = (string)(optional($order->user)->city_id        ?? '');

  // FROM = company country/city (fallback to saved order value on re-edit)
  $fromCountry  = (string) old('from_country_id',   $order->from_country_id   ?? $companyCountryId ?? '');
  $fromCity     = (string) old('from_city_id',      $order->from_city_id      ?? $companyCityId    ?? '');

  // TO = user country/city (fallback to saved order value on re-edit)
  $toCountry    = (string) old('to_country_id',     $order->to_country_id     ?? $userCountryId);
  $toCity       = (string) old('to_city_id',        $order->to_city_id        ?? $userCityId);

  $oldStatus    = (string) old('status',            $order->status            ?? $PENDING);
  $oldEmployee  = (string) old('employee_id',       $order->employee_id       ?? '');
  $wayId        = (string) old('transpartation_id', $order->transpartation_id ?? '');
  $daysCount    = (string) old('days_count',        $order->days_count        ?? '');
  $rejectReason = (string) old('reject_reason',     $order->reject_reason     ?? '');

  $companyCountryId = $companyCountryId ?? '';
  $companyCityId    = $companyCityId    ?? '';
@endphp

@push('css')
<style>
/* ── original design kept exactly ─────────────────────────── */
.glass-card  { border:none; border-radius:18px; box-shadow:0 4px 24px rgba(0,0,0,.07); overflow:hidden; }
.mini-card   { background:rgba(0,0,0,.03); border-radius:12px; padding:1rem 1.25rem; }
.section-title { font-size:1.1rem; font-weight:700; display:flex; align-items:center; gap:.5rem; color:var(--brand-main,#c0392b); }
.section-title i { opacity:.75; }
.pill { display:inline-flex; align-items:center; gap:.35rem; background:rgba(0,0,0,.06); border-radius:30px; padding:3px 12px; font-size:.8rem; font-weight:500; }
.soft-field  { border:1.5px solid #e0e0e0!important; border-radius:10px!important; background:#fff!important; transition:border-color .2s,box-shadow .2s; padding:.5rem .85rem; }
.soft-field:focus { border-color:var(--brand-main,#c0392b)!important; box-shadow:0 0 0 3px rgba(192,57,43,.1)!important; outline:none; }
.subtle      { font-size:.78rem; color:#888; line-height:1.4; }
.badge-soft  { font-size:.78rem; font-weight:600; border-radius:20px; padding:4px 12px; }
.badge-soft.badge-info    { background:rgba(23,162,184,.12);  color:#117a8b; }
.badge-soft.badge-primary { background:rgba(0,123,255,.12);   color:#0056b3; }
.badge-soft.badge-success { background:rgba(39,174,96,.12);   color:#1e8449; }
.badge-soft.badge-warning { background:rgba(243,156,18,.12);  color:#b7770d; }
.card-footer.bg { background:rgba(0,0,0,.02)!important; border-top:1px solid rgba(0,0,0,.06); }
select:disabled, input:disabled { background:#f5f5f5!important; cursor:not-allowed; opacity:.7; }
body.rtl .section-title, body.rtl .pill { flex-direction:row-reverse; }

/* scope badge */
#scopeBadge { display:none; font-size:.75rem; font-weight:600; border-radius:20px; padding:3px 14px; }
#scopeBadge.local    { background:rgba(39,174,96,.15);  color:#1e8449; }
#scopeBadge.internal { background:rgba(41,128,185,.15); color:#1a5276; }

/* stage timeline */
#stageTimeline { display:none; margin-top:1rem; }
.stage-row  { display:flex; align-items:flex-start; gap:.75rem; padding:.55rem 0; position:relative; }
.stage-dot  { width:26px; height:26px; flex-shrink:0; border-radius:50%; background:var(--brand-main,#c0392b); color:#fff; display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; z-index:1; }
.stage-line { position:absolute; left:12px; top:30px; width:2px; bottom:0; background:rgba(0,0,0,.1); z-index:0; }
.stage-row:last-child .stage-line { display:none; }
.stage-body { flex:1; }
.stage-loc  { font-weight:600; font-size:.87rem; }
.stage-meta { font-size:.77rem; color:#888; margin-top:2px; }
body.rtl .stage-row  { flex-direction:row-reverse; }
body.rtl .stage-line { left:auto; right:12px; }

/* local cap warning */
#localCapWarning { display:none; padding:8px 14px; background:rgba(243,156,18,.1); border-radius:8px; border-left:3px solid #f39c12; margin-top:.6rem; font-size:.82rem; color:#7d6608; }
body.rtl #localCapWarning { border-left:none; border-right:3px solid #f39c12; }

/* type filter bar */
#typeFilterWrap { display:none; margin-bottom:.75rem; }
</style>
@endpush

@section('content')
<div class="container-fluid">

  @if($errors->any())
    <div class="alert alert-danger border-0" style="border-radius:12px">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  {{-- ── Header ───────────────────────────────────────────────── --}}
  <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
      <div class="section-title">
        <i class="fas fa-edit"></i>
        <span>{{ __('adminlte::adminlte.edit_order') ?? 'Edit Order' }} #{{ $order->id }}</span>
      </div>
      <div class="mt-2 d-flex flex-wrap gap-2">
        <span class="pill"><i class="fas fa-user"></i> {{ $order->user->name ?? '—' }}</span>
        <span class="pill"><i class="fas fa-calendar-alt"></i> {{ optional($order->created_at)->format('Y-m-d H:i') }}</span>
        @if($order->offer)
          <span class="pill"><i class="fas fa-tag"></i>
            {{ $isAr ? ($order->offer->name_ar ?? $order->offer->name_en) : ($order->offer->name_en ?? $order->offer->name_ar) }}
          </span>
        @endif
      </div>
    </div>
    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary" style="border-radius:14px">
      <i class="fas fa-arrow-{{ $isAr ? 'right' : 'left' }} mr-1"></i>
      {{ __('adminlte::adminlte.back') ?? 'Back' }}
    </a>
  </div>

  {{-- ── Hidden JS helpers ────────────────────────────────────── --}}
  <input type="hidden" id="js_cities_url"      value="{{ route('countries.cities', ['country' => '__COUNTRY_ID__']) }}">
  <input type="hidden" id="js_ways_url"        value="{{ route('transportationWays.search') }}">
  {{-- Company geo (FROM defaults) --}}
  <input type="hidden" id="d_comp_country"     value="{{ $companyCountryId }}">
  <input type="hidden" id="d_comp_city"        value="{{ $companyCityId }}">
  {{-- User geo (TO defaults) --}}
  <input type="hidden" id="d_user_country"     value="{{ $userCountryId }}">
  <input type="hidden" id="d_user_city"        value="{{ $userCityId }}">
  {{-- Saved order geo (pre-fill on existing shipped orders) --}}
  <input type="hidden" id="d_from_country"     value="{{ $fromCountry }}">
  <input type="hidden" id="d_from_city"        value="{{ $fromCity }}">
  <input type="hidden" id="d_to_country"       value="{{ $toCountry }}">
  <input type="hidden" id="d_to_city"          value="{{ $toCity }}">
  {{-- Saved way --}}
  <input type="hidden" id="d_way_id"           value="{{ $wayId }}">
  {{-- Company country (for scope calculation) --}}
  <input type="hidden" id="js_company_country" value="{{ $companyCountryId }}">

  <form method="POST" action="{{ route('orders.update', $order) }}" id="order-edit-form">
    @csrf @method('PUT')

    <div class="card glass-card mb-3">
      <div class="card-body">

        {{-- Notes --}}
        <div class="mb-3">
          <label class="font-weight-bold">{{ __('adminlte::adminlte.notes') ?? 'Notes' }}</label>
          <textarea name="notes" class="form-control soft-field" rows="3">{{ old('notes', $order->notes) }}</textarea>
        </div>

        <div class="row">
          {{-- Status --}}
          <div class="col-lg-6 mb-3">
            <label class="font-weight-bold">{{ __('adminlte::adminlte.status') ?? 'Status' }}</label>
            <select name="status" id="orderStatus" class="form-control soft-field" required>
              <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
              @foreach($orderStatus as $st)
                @php $label = $isAr ? ($st->name_ar ?? $st->name_en) : ($st->name_en ?? $st->name_ar); @endphp
                <option value="{{ $st->status }}" @selected((string)$oldStatus===(string)$st->status)>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          {{-- Employee (ACCEPTED) --}}
          <div class="col-lg-6 mb-3" id="employeeBox" style="display:none;">
            <label class="font-weight-bold">{{ __('adminlte::adminlte.assign_employee') ?? 'Assign Employee' }}</label>
            <select name="employee_id" id="employee_id" class="form-control soft-field">
              <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}"
                        data-country="{{ $emp->country_id ?? '' }}"
                        data-city="{{ $emp->city_id ?? '' }}"
                        @selected((string)$oldEmployee===(string)$emp->id)>
                  {{ $emp->name }}
                </option>
              @endforeach
            </select>
            <div class="subtle mt-2">{{ __('adminlte::adminlte.required_when_accepted') ?? 'Required when accepted' }}</div>
          </div>
        </div>

        {{-- Reject reason (REJECTED) --}}
        <div class="mb-3" id="rejectBox" style="display:none;">
          <label class="font-weight-bold">{{ __('adminlte::adminlte.reject_reason') ?? 'Reject reason' }}</label>
          <textarea name="reject_reason" id="reject_reason" class="form-control soft-field" rows="3">{{ $rejectReason }}</textarea>
          <div class="subtle mt-2">{{ __('adminlte::adminlte.required_when_rejected') ?? 'Required when rejected' }}</div>
        </div>

        {{-- ═══════════════════════════════════════════════════
             SHIPMENT  (SHIPPED only)
        ═══════════════════════════════════════════════════ --}}
        <div id="shipmentBox" style="display:none;">
          <hr class="my-3">

          {{-- Title + scope badge --}}
          <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
            <div class="section-title mb-0">
              <i class="fas fa-truck"></i>
              {{ __('adminlte::adminlte.shipment') ?? 'Shipment' }}
            </div>
            <span id="scopeBadge"></span>
          </div>

          <div class="row">

            {{-- FROM (company) --}}
            <div class="col-lg-6 mb-3">
              <div class="mini-card">
                <div class="subtle font-weight-bold text-uppercase mb-2" style="font-size:.7rem;letter-spacing:.05em;">
                  <i class="fas fa-building mr-1" style="color:var(--brand-main)"></i>
                  {{ $isAr ? 'من · الشركة' : 'From · Company' }}
                </div>
                <div class="mb-2">
                  <label class="subtle">{{ __('adminlte::adminlte.country') ?? 'Country' }}</label>
                  {{-- FIX: value pre-set by PHP ($fromCountry) so JS can read it on load --}}
                  <select id="from_country_id" name="from_country_id" class="form-control soft-field">
                    <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
                    @foreach($countries as $c)
                      <option value="{{ $c->id }}" @selected((string)$fromCountry===(string)$c->id)>
                        {{ $isAr ? ($c->name_ar ?? $c->name_en) : ($c->name_en ?? $c->name_ar) }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <label class="subtle">{{ __('adminlte::adminlte.city') ?? 'City' }}</label>
                  {{-- FIX: data-selected carries the PHP city id; JS reads this after fetching cities --}}
                  <select id="from_city_id" name="from_city_id"
                          class="form-control soft-field"
                          data-selected="{{ $fromCity }}">
                    <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
                  </select>
                </div>
              </div>
            </div>

            {{-- TO (user) --}}
            <div class="col-lg-6 mb-3">
              <div class="mini-card">
                <div class="subtle font-weight-bold text-uppercase mb-2" style="font-size:.7rem;letter-spacing:.05em;">
                  <i class="fas fa-user mr-1" style="color:var(--brand-main)"></i>
                  {{ $isAr ? 'إلى · العميل' : 'To · Customer' }}
                </div>
                <div class="mb-2">
                  <label class="subtle">{{ __('adminlte::adminlte.country') ?? 'Country' }}</label>
                  {{-- FIX: value pre-set by PHP ($toCountry) --}}
                  <select id="to_country_id" name="to_country_id" class="form-control soft-field">
                    <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
                    @foreach($countries as $c)
                      <option value="{{ $c->id }}" @selected((string)$toCountry===(string)$c->id)>
                        {{ $isAr ? ($c->name_ar ?? $c->name_en) : ($c->name_en ?? $c->name_ar) }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <label class="subtle">{{ __('adminlte::adminlte.city') ?? 'City' }}</label>
                  {{-- FIX: data-selected carries the PHP city id --}}
                  <select id="to_city_id" name="to_city_id"
                          class="form-control soft-field"
                          data-selected="{{ $toCity }}">
                    <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          {{-- Transportation section --}}
          <div class="mini-card mt-1">

            {{-- Type filter (built client-side from fetched ways) --}}
            <div id="typeFilterWrap">
              <label class="font-weight-bold">
                <i class="fas fa-layer-group mr-1" style="opacity:.65"></i>
                {{ $isAr ? 'نوع وسيلة النقل' : 'Transportation Type' }}
              </label>
              <select id="typeFilter" class="form-control soft-field mb-2">
                <option value="">{{ $isAr ? 'كل الأنواع' : 'All types' }}</option>
              </select>
              <div class="subtle mb-3">
                {{ $isAr
                  ? 'اختر النوع لتصفية الطرق — محلي (بحد أقصى مرحلتان) · دولي (مراحل متعددة).'
                  : 'Filter ways by type — Local (max 2 stages) · International (unlimited stages).' }}
              </div>
            </div>

            {{-- Way --}}
            <label class="font-weight-bold">
              <i class="fas fa-route mr-1" style="opacity:.65"></i>
              {{ __('adminlte::adminlte.transportation_way') ?? 'Transportation Way' }}
            </label>
            {{-- FIX: data-selected carries saved way id; JS selects it after ways load --}}
            <select name="transpartation_id" id="transportation_way_id"
                    class="form-control soft-field"
                    data-selected="{{ $wayId }}">
              <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>
            </select>

            {{-- Local cap warning --}}
            <div id="localCapWarning">
              <i class="fas fa-exclamation-triangle mr-1"></i>
              {{ $isAr ? 'الشحن المحلي يسمح بمرحلتين كحد أقصى.' : 'Local transportation allows a maximum of 2 stages.' }}
            </div>

            {{-- Info badges --}}
            <div class="d-flex flex-wrap gap-2 mt-2">
              <span class="badge badge-info    badge-soft" id="daysBadge"  style="display:none;"></span>
              <span class="badge badge-primary badge-soft" id="typeBadge"  style="display:none;"></span>
              <span class="badge badge-success badge-soft" id="priceBadge" style="display:none;"></span>
            </div>

            {{-- Stage timeline --}}
            <div id="stageTimeline"></div>

            <input type="hidden" name="days_count" id="days_count" value="{{ $daysCount }}">
          </div>

          <div class="subtle mt-2">
            {{ __('adminlte::adminlte.required_when_shipped') ?? 'Required when shipped' }}
          </div>
        </div>{{-- /shipmentBox --}}

      </div>{{-- /card-body --}}

      <div class="card-footer bg d-flex flex-wrap gap-3">
        <button class="btn btn-primary" style="border-radius:14px" type="submit">
          <i class="fas fa-save mr-1"></i> {{ __('adminlte::adminlte.save') ?? 'Save' }}
        </button>
        <a class="btn btn-outline-secondary" style="border-radius:14px" href="{{ route('orders.show', $order) }}">
          {{ __('adminlte::adminlte.cancel') ?? 'Cancel' }}
        </a>
      </div>
    </div>

  </form>
</div>
@endsection

@push('js')
<script>
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {

    /* ── Constants ──────────────────────────────────────────── */
    var PENDING  = '{{ $PENDING }}';
    var ACCEPTED = '{{ $ACCEPTED }}';
    var REJECTED = '{{ $REJECTED }}';
    var SHIPPED  = '{{ $SHIPPED }}';
    var isAr     = document.body.classList.contains('rtl') || document.documentElement.lang === 'ar';
    var T_SEL    = '{{ __('adminlte::adminlte.select') ?? 'Select' }}';
    var CSRF     = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var LOCAL_MAX = 2;

    /* ── URLs / blade data ──────────────────────────────────── */
    var CITIES_TPL  = (document.getElementById('js_cities_url')      || {}).value || '';
    var WAYS_URL    = (document.getElementById('js_ways_url')        || {}).value || '';
    var COMPANY_CID = (document.getElementById('js_company_country') || {}).value || '';

    /* ── Pre-set PHP values passed via hidden inputs ─────────── */
    // FIX: read all geo values from dedicated hidden inputs
    var D_COMP_C   = (document.getElementById('d_comp_country') || {}).value || '';
    var D_COMP_CT  = (document.getElementById('d_comp_city')    || {}).value || '';
    var D_USER_C   = (document.getElementById('d_user_country') || {}).value || '';
    var D_USER_CT  = (document.getElementById('d_user_city')    || {}).value || '';
    var D_FROM_C   = (document.getElementById('d_from_country') || {}).value || '';
    var D_FROM_CT  = (document.getElementById('d_from_city')    || {}).value || '';
    var D_TO_C     = (document.getElementById('d_to_country')   || {}).value || '';
    var D_TO_CT    = (document.getElementById('d_to_city')      || {}).value || '';
    var D_WAY_ID   = (document.getElementById('d_way_id')       || {}).value || '';

    /* ── DOM refs ────────────────────────────────────────────── */
    var statusSel  = document.getElementById('orderStatus');
    var empBox     = document.getElementById('employeeBox');
    var empSel     = document.getElementById('employee_id');
    var rejBox     = document.getElementById('rejectBox');
    var rejTxt     = document.getElementById('reject_reason');
    var shipBox    = document.getElementById('shipmentBox');
    var fromC      = document.getElementById('from_country_id');
    var fromCt     = document.getElementById('from_city_id');
    var toC        = document.getElementById('to_country_id');
    var toCt       = document.getElementById('to_city_id');
    var typeFilter = document.getElementById('typeFilter');
    var typeWrap   = document.getElementById('typeFilterWrap');
    var waySel     = document.getElementById('transportation_way_id');
    var daysHid    = document.getElementById('days_count');
    var daysB      = document.getElementById('daysBadge');
    var typeB      = document.getElementById('typeBadge');
    var priceB     = document.getElementById('priceBadge');
    var scopeBadge = document.getElementById('scopeBadge');
    var stageTL    = document.getElementById('stageTimeline');
    var localWarn  = document.getElementById('localCapWarning');

    /* ── Caches ─────────────────────────────────────────────── */
    var allWays  = [];
    var wayCache = {};

    /* ── Helpers ────────────────────────────────────────────── */
    function show(el) { if (el) el.style.display = ''; }
    function hide(el) { if (el) el.style.display = 'none'; }
    function setReq(el, on) { if (el) el.required = !!on; }
    function reset(sel, placeholder) {
      if (!sel) return;
      sel.innerHTML = '<option value="">' + (placeholder || T_SEL) + '</option>';
    }
    function lockGeo(lock) {
      [fromC, fromCt, toC, toCt].forEach(function (el) { if (el) el.disabled = !!lock; });
    }

    /* ── Scope ──────────────────────────────────────────────── */
    function calcScope(cid) {
      if (!cid || !COMPANY_CID) return 'internal';
      return String(cid) === String(COMPANY_CID) ? 'local' : 'internal';
    }

    function showScope(s) {
      if (!scopeBadge) return;
      scopeBadge.className = s;
      scopeBadge.textContent = s === 'local'
        ? (isAr ? '🏠 محلي (نفس البلد)' : '🏠 Local (same country)')
        : (isAr ? '✈️ دولي'              : '✈️ International');
      show(scopeBadge);
    }

    /* ── Fetch: cities ──────────────────────────────────────── */
    // FIX: after building options, explicitly set .value then dispatch change
    // so the select shows the right option even before the user touches it.
    function loadCities(cid, sel, preId) {
      return new Promise(function (res) {
        reset(sel);
        if (!cid || !sel) { res(); return; }
        fetch(CITIES_TPL.replace('__COUNTRY_ID__', encodeURIComponent(cid)), {
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(function (r) { return r.ok ? r.json() : { data: [] }; })
        .then(function (j) {
          (j.data || []).forEach(function (c) {
            var o = document.createElement('option');
            o.value       = c.id;
            o.textContent = isAr ? (c.name_ar || c.name_en || '') : (c.name_en || c.name_ar || '');
            sel.appendChild(o);
          });
          // FIX: set value after all options are appended
          if (preId) sel.value = String(preId);
          res();
        }).catch(function (e) { console.warn('[cities]', e); res(); });
      });
    }

    /* ── Fetch: all ways ────────────────────────────────────── */
    // FIX: after rendering options, set waySel.value to preWay
    // then call updateMeta() so badges and timeline populate immediately.
    function loadWays(cid, ctid, sc, preWay) {
      return new Promise(function (res) {
        reset(waySel);
        reset(typeFilter, isAr ? 'كل الأنواع' : 'All types');
        clearMeta();
        hide(typeWrap);
        if (!cid) { res(); return; }

        var url = new URL(WAYS_URL, window.location.origin);
        url.searchParams.set('country_id', cid);
        if (ctid) url.searchParams.set('city_id', ctid);
        url.searchParams.set('scope', sc || calcScope(cid));

        fetch(url.toString(), {
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(function (r) { return r.ok ? r.json() : { data: [] }; })
        .then(function (j) {
          allWays  = j.data || [];
          wayCache = {};
          allWays.forEach(function (w) { wayCache[String(w.id)] = w; });

          /* Build type filter from unique types */
          var seenTypes = {};
          allWays.forEach(function (w) {
            var t = w.type || {};
            var tid = String(t.id || '');
            if (tid && !seenTypes[tid]) {
              seenTypes[tid] = true;
              var o = document.createElement('option');
              o.value       = tid;
              o.dataset.scope = t.scope || w.scope || '';
              o.textContent = isAr ? (t.name_ar || t.name_en || '') : (t.name_en || t.name_ar || '');
              typeFilter.appendChild(o);
            }
          });
          if (Object.keys(seenTypes).length > 1) show(typeWrap);

          // FIX: pass preWay so way gets selected immediately
          renderWayOptions(allWays, preWay);
          res();
        }).catch(function (e) { console.warn('[ways]', e); res(); });
      });
    }

    /* ── Render way options ─────────────────────────────────── */
    // FIX: set waySel.value after appending all options, then run updateMeta
    function renderWayOptions(ways, preWay) {
      var saved = (preWay !== undefined && preWay !== null) ? String(preWay) : '';
      reset(waySel);
      ways.forEach(function (w) {
        var o         = document.createElement('option');
        o.value       = w.id;
        o.textContent = isAr ? (w.name_ar || w.name_en || '') : (w.name_en || w.name_ar || '');
        o.dataset.days    = w.days_count || 0;
        o.dataset.price   = w.price      || 0;
        o.dataset.scope   = w.scope      || '';
        var t = w.type || {};
        o.dataset.type    = isAr ? (t.name_ar || t.name_en || '') : (t.name_en || t.name_ar || '');
        o.dataset.type_id = String(t.id || '');
        waySel.appendChild(o);
      });
      // FIX: explicitly set value so the saved way appears selected
      if (saved) waySel.value = saved;
      updateMeta();
    }

    function reloadWays(preWay) {
      var cid  = fromC  ? fromC.value  : '';
      var ctid = fromCt ? fromCt.value : '';
      var sc   = calcScope(cid);
      showScope(sc);
      return loadWays(cid, ctid, sc, preWay);
    }

    /* ── Meta badges ────────────────────────────────────────── */
    function clearMeta() {
      hide(daysB); hide(typeB); hide(priceB);
      if (daysHid) daysHid.value = '';
      if (stageTL) { stageTL.innerHTML = ''; hide(stageTL); }
      hide(localWarn);
    }

    function updateMeta() {
      var opt = waySel && waySel.selectedIndex > 0 ? waySel.options[waySel.selectedIndex] : null;
      if (!opt || !opt.value) { clearMeta(); return; }

      var days  = opt.dataset.days  || '0';
      var type  = opt.dataset.type  || '';
      var price = opt.dataset.price || '0';
      var sc    = opt.dataset.scope || calcScope(fromC ? fromC.value : '');

      if (daysHid) daysHid.value = days;
      if (daysB)  { daysB.textContent  = (isAr ? 'الأيام: '  : 'Days: ')  + days;  show(daysB);  }
      if (priceB) { priceB.textContent = (isAr ? 'السعر: '   : 'Price: ') + price; show(priceB); }
      if (typeB && type) { typeB.textContent = (isAr ? 'النوع: ' : 'Type: ') + type; show(typeB); }
      else { hide(typeB); }

      var way    = wayCache[String(opt.value)];
      var stages = (way && way.stages) ? way.stages : [];

      if (stages.length) {
        var isLocal  = sc === 'local';
        var visible  = isLocal ? stages.slice(0, LOCAL_MAX) : stages;
        renderStages(visible, sc, stages.length);
        if (isLocal && stages.length > LOCAL_MAX) show(localWarn);
        else hide(localWarn);
      } else {
        if (stageTL) { stageTL.innerHTML = ''; hide(stageTL); }
        hide(localWarn);
      }
    }

    /* ── Stage timeline ─────────────────────────────────────── */
    function renderStages(stages, sc, total) {
      if (!stageTL) return;
      stageTL.innerHTML = '';
      var isLocal = sc === 'local';

      var h = document.createElement('div');
      h.className  = 'subtle font-weight-bold mt-3 mb-1';
      h.style.fontSize = '.77rem';
      h.textContent = isLocal
        ? (isAr ? 'مراحل الشحن المحلي (بحد أقصى ' + LOCAL_MAX + ' مراحل):' : 'Local stages (max ' + LOCAL_MAX + '):')
        : (isAr ? 'مراحل الشحن الدولي (' + stages.length + ' مراحل):'       : 'International stages (' + stages.length + '):');
      stageTL.appendChild(h);

      stages.forEach(function (s, i) {
        var row = document.createElement('div');
        row.className = 'stage-row';

        if (i < stages.length - 1) {
          var line = document.createElement('div');
          line.className = 'stage-line';
          row.appendChild(line);
        }

        var dot = document.createElement('div');
        dot.className   = 'stage-dot';
        dot.textContent = s.stage_order || (i + 1);
        row.appendChild(dot);

        var body = document.createElement('div');
        body.className  = 'stage-body';

        var loc = document.createElement('div');
        loc.className   = 'stage-loc';
        var lp = isAr
          ? [s.country_name_ar || s.country_name_en || '', s.city_name_ar || s.city_name_en || '']
          : [s.country_name_en || s.country_name_ar || '', s.city_name_en || s.city_name_ar || ''];
        loc.textContent = lp.filter(Boolean).join(' / ') || '—';
        body.appendChild(loc);

        var meta = document.createElement('div');
        meta.className  = 'stage-meta';
        var parts = [];
        if (s.transport_mode) parts.push(s.transport_mode);
        if (s.days_count)     parts.push((isAr ? 'أيام: ' : 'days: ') + s.days_count);
        if (s.price > 0)      parts.push((isAr ? 'سعر: '  : 'price: ') + s.price);
        meta.textContent = parts.join(' · ');
        body.appendChild(meta);

        row.appendChild(body);
        stageTL.appendChild(row);
      });

      if (isLocal && total > LOCAL_MAX) {
        var note = document.createElement('div');
        note.className   = 'subtle mt-1';
        note.style.color = '#b7770d';
        note.textContent = isAr
          ? '⚠ يُعرض ' + LOCAL_MAX + ' من أصل ' + total + ' مراحل (الحد الأقصى للشحن المحلي).'
          : '⚠ Showing ' + LOCAL_MAX + ' of ' + total + ' stages (local cap applied).';
        stageTL.appendChild(note);
      }
      show(stageTL);
    }
function initShipment() {

  if (fromC && D_FROM_C) fromC.value = D_FROM_C;
  if (toC   && D_TO_C)   toC.value   = D_TO_C;

  var sc = calcScope(D_FROM_C);
  showScope(sc);

  return loadCities(D_FROM_C, fromCt, D_FROM_CT)
    .then(function () {
      return loadCities(D_TO_C, toCt, D_TO_CT);
    })
    .then(function () {
      var ctid = fromCt ? fromCt.value : '';
      return loadWays(D_FROM_C, ctid, sc, D_WAY_ID);
    });
}
    /* ── Default: fresh status change → company → user ────────── */
    // FIX: use company country/city for FROM, user country/city for TO
    function defaultShipment() {
      if (fromC) fromC.value = D_COMP_C;
      if (toC)   toC.value   = D_USER_C;

      var sc = calcScope(D_COMP_C);
      showScope(sc);

      return loadCities(D_COMP_C, fromCt, D_COMP_CT)
        .then(function () { return loadCities(D_USER_C, toCt, D_USER_CT); })
        .then(function () { return loadWays(D_COMP_C, D_COMP_CT, sc, ''); });
    }

    /* ── Apply status ───────────────────────────────────────── */
    function applyStatus(v, init) {
      v = String(v || '');
      var acc  = v === ACCEPTED;
      var rej  = v === REJECTED;
      var ship = v === SHIPPED;

      if (acc) show(empBox); else hide(empBox);
      setReq(empSel, acc);

      if (rej) show(rejBox); else hide(rejBox);
      setReq(rejTxt, rej);

      if (ship) show(shipBox); else hide(shipBox);
      setReq(fromC, ship); setReq(fromCt, ship);
      setReq(toC,   ship); setReq(toCt,   ship);
      setReq(waySel, ship);

      if (ship) {
        // init=true  → editing existing order, restore saved values
        // init=false → user just changed status, use company→user defaults
        if (init) initShipment(); else defaultShipment();
      } else {
        hide(scopeBadge); hide(typeWrap);
        lockGeo(false);
      }
    }

    /* ── Events ─────────────────────────────────────────────── */
    if (statusSel) {
      statusSel.addEventListener('change', function () { applyStatus(this.value, false); });
    }

    if (empSel) {
      empSel.addEventListener('change', function () {
        if (statusSel && String(statusSel.value) === SHIPPED) defaultShipment();
      });
    }

    if (fromC) {
      fromC.addEventListener('change', function () {
        var sc = calcScope(this.value);
        showScope(sc);
        loadCities(this.value, fromCt, '').then(function () {
          return loadWays(fromC.value, fromCt ? fromCt.value : '', sc, '');
        });
      });
    }

    if (toC) {
      toC.addEventListener('change', function () {
        loadCities(this.value, toCt, '');
      });
    }

    if (fromCt) {
      fromCt.addEventListener('change', function () {
        reloadWays('');
      });
    }

    /* Type filter: filter allWays in memory — no extra request */
    if (typeFilter) {
      typeFilter.addEventListener('change', function () {
        var tid      = this.value;
        var filtered = tid
          ? allWays.filter(function (w) { return String((w.type || {}).id || '') === String(tid); })
          : allWays;
        renderWayOptions(filtered, '');
      });
    }

    if (waySel) {
      waySel.addEventListener('change', updateMeta);
    }

    /* ── Boot ───────────────────────────────────────────────── */
    applyStatus(statusSel ? statusSel.value : PENDING, true);

  });
}());
</script>
@endpush