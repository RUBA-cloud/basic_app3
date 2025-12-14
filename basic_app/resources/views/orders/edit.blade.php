{{-- resources/views/orders/edit.blade.php --}}
@extends('adminlte::page')

@section('title', "Edit Order #{$order->id}")

@section('adminlte_css')
@parent
<style>
  .order-edit-wrap{
    max-width: 980px;
    margin: 0 auto;
  }
  .order-hero{
    border-radius: 18px;
    padding: 18px 18px;
    margin-bottom: 14px;
    background: linear-gradient(135deg, rgba(0,0,0,.06), rgba(0,0,0,.02));
    border: 1px solid rgba(0,0,0,.06);
  }
  .order-hero .title{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
  }
  .order-hero h2{
    margin:0;
    font-size: 1.35rem;
    font-weight: 800;
    letter-spacing:.2px;
  }
  .order-hero .meta{
    font-size: .9rem;
    opacity: .85;
    margin-top: 6px;
    display:flex; flex-wrap:wrap; gap:10px;
  }
  .pill{
    display:inline-flex; align-items:center; gap:8px;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(0,0,0,.05);
    border: 1px solid rgba(0,0,0,.06);
    font-size: .85rem;
    font-weight: 600;
  }
  .glass-card{
    border-radius: 18px;
    overflow:hidden;
    border: 1px solid rgba(0,0,0,.06);
    box-shadow: 0 12px 28px rgba(0,0,0,.08);
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(10px);
  }
  .glass-card .card-header{
    background: transparent;
    border-bottom: 1px solid rgba(0,0,0,.06);
    padding: 14px 16px;
    font-weight: 800;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
  }
  .hint{
    font-size:.82rem;
    opacity:.75;
    margin:0;
  }
  .form-label{
    font-weight: 700;
    margin-bottom: 6px;
  }
  .soft-field{
    border-radius: 14px !important;
    border: 1px solid rgba(0,0,0,.10) !important;
    box-shadow: none !important;
    padding: .7rem .9rem;
  }
  .soft-field:focus{
    border-color: rgba(0,0,0,.22) !important;
    box-shadow: 0 0 0 .18rem rgba(0,0,0,.06) !important;
  }
  .section{
    padding: 16px;
  }
  .section + .section{
    border-top: 1px dashed rgba(0,0,0,.12);
  }
  .grid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 12px;
  }
  @media (min-width: 992px){
    .grid.two{
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }
    .grid.three{
      grid-template-columns: 1fr 1fr 1fr;
      gap: 14px;
    }
  }
  .alert{
    border-radius: 14px;
  }
  .actions{
    display:flex;
    gap:10px;
    justify-content:flex-end;
    flex-wrap:wrap;
    padding: 14px 16px;
    background: rgba(0,0,0,.02);
    border-top: 1px solid rgba(0,0,0,.06);
  }
  .btn-round{
    border-radius: 14px;
    padding: .7rem 1rem;
    font-weight: 800;
  }
  .fade-in{
    animation: fadeIn .18s ease;
  }
  @keyframes fadeIn{
    from{opacity:0; transform: translateY(-4px);}
    to{opacity:1; transform: translateY(0);}
  }
</style>
@endsection

@section('content')
<div class="order-edit-wrap">

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- HERO --}}
  <div class="order-hero">
    <div class="title">
      <div>
        <h2>{{ __('adminlte::adminlte.edit') ?? 'Edit' }} #{{ $order->id }}</h2>
        <div class="meta">
          <span class="pill">
            <i class="fas fa-user"></i>
            {{ $order->user->name ?? __('adminlte::adminlte.user') ?? 'User' }}
          </span>
          <span class="pill">
            <i class="fas fa-calendar-alt"></i>
            {{ optional($order->created_at)->format('Y-m-d H:i') }}
          </span>
          @if($order->offer)
            <span class="pill">
              <i class="fas fa-tag"></i>
              {{ app()->isLocale('ar') ? ($order->offer->name_ar ?? $order->offer->name_en) : ($order->offer->name_en ?? $order->offer->name_ar) }}
            </span>
          @endif
        </div>
      </div>

      <a href="{{ route('orders.show',$order) }}" class="btn btn-outline-secondary btn-round">
        <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') ?? 'Back' }}
      </a>
    </div>
  </div>

  <form method="POST" action="{{ route('orders.update',$order) }}">
    @csrf
    @method('PUT')

    <div class="card glass-card mb-3">
      <div class="card-header">
        <span><i class="fas fa-receipt mr-2"></i>{{ __('adminlte::adminlte.order') ?? 'Order' }}</span>
        <p class="hint">
          {{ __('adminlte::adminlte.update_information') ?? 'Update the order status and notes.' }}
        </p>
      </div>

      {{-- SECTION: NOTES --}}
      <div class="section">
        <div class="grid">
          <div>
            <label class="form-label">{{ __('adminlte::adminlte.notes') ?? 'Notes' }}</label>
            <textarea
              name="notes"
              class="form-control soft-field"
              rows="4"
              placeholder="{{ __('adminlte::adminlte.optional') ?? 'Optional' }}"
            >{{ old('notes',$order->notes) }}</textarea>
          </div>
        </div>
      </div>

      {{-- SECTION: STATUS + EMPLOYEE --}}
      <div class="section">
        <div class="grid two">
          <div>
            <label class="form-label">{{ __('adminlte::adminlte.status') ?? 'Status' }}</label>
            <label>{{ $orderStatus }}</label>
            <select name="status" id="status" class="form-control soft-field" required>
              <option value="">{{ __('adminlte::adminlte.select') ?? 'choose' }}</option>

              @foreach($orderStatus as $st)
                @php
                  $label = app()->isLocale('ar') ? ($st->name_ar ?? $st->name_en) : ($st->name_en ?? $st->name_ar);
                  $code  = strtolower($st->code ?? $st->slug ?? $st->name_en ?? '');
                @endphp
                <option
                  value="{{ $st->id }}"
                  data-code="{{ $code }}"
                  @selected((string)old('status',$order->status) === (string)$st->id)
                >
                  {{ $label }}
                </option>
              @endforeach
            </select>
            <small class="text-muted d-block mt-2">
              {{ __('adminlte::adminlte.choose_status') ?? 'Choose order status from the list.' }}
            </small>
          </div>

          <div id="employeeContainer" style="display:none;">
            <label class="form-label">
              {{ __('adminlte::adminlte.assign_employee') ?? 'Assign Employee' }}
            </label>
            <select name="employee_id" id="employee_id" class="form-control soft-field">
              <option value="">{{ __('adminlte::adminlte.select') ?? '-- choose --' }}</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}" @selected((string)old('employee_id',$order->employee_id) === (string)$emp->id)>
                  {{ $emp->name }}
                </option>
              @endforeach
            </select>
            <small class="text-muted d-block mt-2">
              {{ __('adminlte::adminlte.required_when_accepting') ?? 'Required when status is Accepted.' }}
            </small>
          </div>
        </div>

        {{-- REJECT REASON --}}
        <div class="grid mt-3">
          <div id="rejectReasonContainer" style="display:none;">
            <label class="form-label">{{ __('adminlte::adminlte.reject_reason') ?? 'Reject reason' }}</label>
            <textarea
              name="reject_reason"
              id="reject_reason"
              class="form-control soft-field"
              rows="4"
              placeholder="{{ __('adminlte::adminlte.enter_reject_reason') ?? 'Please enter reason for rejection' }}"
            >{{ old('reject_reason', $order->reject_reason ?? '') }}</textarea>
            <small class="text-muted d-block mt-2">
              {{ __('adminlte::adminlte.required_when_rejected') ?? 'Required when status is Rejected.' }}
            </small>
          </div>
        </div>
      </div>

      {{-- ACTIONS --}}
      <div class="actions">
        <button class="btn btn-primary btn-round" type="submit">
          <i class="fas fa-save mr-1"></i> {{ __('adminlte::adminlte.save') ?? 'Save' }}
        </button>

        <a class="btn btn-outline-secondary btn-round" href="{{ route('orders.show',$order) }}">
          {{ __('adminlte::adminlte.cancel') ?? 'Cancel' }}
        </a>
      </div>
    </div>
  </form>

</div>

@php
  // Prefer using code column: accepted / rejected
  $acceptedId = $orderStatus->firstWhere('code','accepted')?->id ?? null;
  $rejectedId = $orderStatus->firstWhere('code','rejected')?->id ?? null;
@endphp

<script>
(function(){
  const statusSel = document.getElementById('status');

  const empBox = document.getElementById('employeeContainer');
  const empSel = document.getElementById('employee_id');

  const rejectBox = document.getElementById('rejectReasonContainer');
  const rejectTxt = document.getElementById('reject_reason');

  const ACCEPTED_ID = @json($acceptedId);
  const REJECTED_ID = @json($rejectedId);

  function getSelectedCode(){
    const opt = statusSel.options[statusSel.selectedIndex];
    return (opt && opt.dataset && opt.dataset.code) ? String(opt.dataset.code).trim() : '';
  }
  function isAccepted(){
    if (ACCEPTED_ID) return String(statusSel.value) === String(ACCEPTED_ID);
    return getSelectedCode().includes('accept');
  }
  function isRejected(){
    if (REJECTED_ID) return String(statusSel.value) === String(REJECTED_ID);
    return getSelectedCode().includes('reject');
  }

  function show(el){
    el.style.display = '';
    el.classList.add('fade-in');
    setTimeout(()=>el.classList.remove('fade-in'), 200);
  }
  function hide(el){
    el.style.display = 'none';
  }

  function toggleFields(){
    const showEmp = isAccepted();
    if (showEmp) show(empBox); else { hide(empBox); empSel.value=''; }
    empSel.required = showEmp;

    const showReject = isRejected();
    if (showReject) show(rejectBox); else { hide(rejectBox); rejectTxt.value=''; }
    rejectTxt.required = showReject;
  }

  statusSel.addEventListener('change', toggleFields);
  toggleFields();
})();
</script>
@endsection
