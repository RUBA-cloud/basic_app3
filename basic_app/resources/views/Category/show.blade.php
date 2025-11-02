@extends('adminlte::page')

@section('title', __('adminlte::adminlte.category'))

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-dark fw-bold">
            <i class="fas fa-code-branch me-2 text-primary"></i>
            @if (app()->getLocale() === 'ar')
                {{ __('adminlte::adminlte.details') }} {{ __('adminlte::adminlte.category') }}
            @else
                {{ __('adminlte::adminlte.category') }} {{ __('adminlte::adminlte.details') }}
            @endif
        </h2>

        @if($category->is_main_branch)
            <span class="badge bg-purple text-white px-3 py-2">
                <i class="fas fa-star me-1"></i>
                {{ __('adminlte::adminlte.main_branch') }}
            </span>
        @endif
    </div>

    {{-- Card --}}
    <x-adminlte-card theme="light" theme-mode="outline" class="shadow-sm">
        <div class="row g-4">

            {{-- Image --}}
            <div class="col-lg-4 col-md-5">
                <div class="border rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center p-2 h-100">
                    <img
                        src="{{ $category->image ? asset($category->image) : 'https://placehold.co/500x300?text=Branch+Image' }}"
                        alt="Branch Image"
                        class="img-fluid rounded-3"
                        style="max-height: 280px; object-fit: cover;"
                    >
                </div>
            </div>

            {{-- Details --}}
            <div class="col-lg-8 col-md-7">
                <div class="row gy-3">

                    {{-- Branch Name EN --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_en') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $category->name_en }}</div>
                    </div>

                    {{-- Branch Name AR --}}
                    <div class="col-12">
                        <small class="text-muted">{{ __('adminlte::adminlte.name_ar') }}</small>
                        <div class="fs-5 fw-bold text-dark">{{ $category->name_ar }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        @if($category->is_active)
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i> {{ __('adminlte::adminlte.active') }}
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i> {{ __('adminlte::adminlte.inactive') }}
                            </span>
                        @endif
                    </div>

                    {{-- Addresses --}}
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_en') }}</small>
                        <div class="fw-semibold">{{ $category->address_en ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">{{ __('adminlte::adminlte.company_address_ar') }}</small>
                        <div class="fw-semibold">{{ $category->address_ar ?? '-' }}</div>
                    </div>

                    {{-- Branches --}}
                    <div class="col-12">
                        <h6 class="font-weight-bold text-secondary">{{ __('adminlte::menu.branches') }}</h6>
                        @if($category->branches->count())
                            <ul class="list-unstyled ps-2">
                                @foreach($category->branches as $branch)
                                    <li>
                                        <a href="{{ route('companyBranch.show', $branch->id) }}" class="text-primary fw-bold">
                                            @if(app()->getLocale()=="ar")
                                             <i class="fas fa-code-branch me-1"></i> {{ $branch->name_ar}}@else
                                            <i class="fas fa-code-branch me-1"></i> {{ $branch->name_en }}

                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">{{ __('adminlte::adminlte.no_branches') }}</p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 pt-3">
                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-edit me-2"></i> {{ __('adminlte::adminlte.edit') }}
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('adminlte::adminlte.go_back') }}
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </x-adminlte-card>
</div>
@endsection
@section('js')
<script>
(function(){
  'use strict';
  const esc = (s) => (window.CSS && CSS.escape) ? CSS.escape(s) : s;

  // Small helpers
  const setField = (name, value) => {
    if (value === undefined || value === null) return;
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('input',  { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setCheckbox = (name, isOn) => {
    const el = document.querySelector(`[name="${esc(name)}"]`);
    if (!el) return;
    el.checked = !!Number(isOn);
    el.dispatchEvent(new Event('change', { bubbles: true }));
  };

  const setMultiSelect = (name, values = []) => {
    const el = document.getElementById('branch_ids') || document.querySelector(`[name="${esc(name)}[]"]`);
    if (!el) return;
    const want = (values || []).map(v => String(v));
    Array.from(el.options).forEach(opt => { opt.selected = want.includes(String(opt.value)); });
    if (window.jQuery && jQuery(el).hasClass('select')) {
      jQuery(el).trigger('change.select2');
    } else {
      el.dispatchEvent(new Event('change', { bubbles: true }));
    }
  };

  const previewImage = (url) => {
    const img = document.querySelector('#image-preview, [data-role="image-preview"]');
    if (img && url) img.src = url;
  };

  // Apply incoming payload
  const applyPayload = (payload) => {
    const c = payload?.category ?? payload ?? {};
    setField('name_en',  c.name_en);
    setField('name_ar',  c.name_ar);
    setCheckbox('is_active', c.is_active);

    const ids = c.branch_ids || (Array.isArray(c.branches) ? c.branches.map(b => b.id) : []);
    setMultiSelect('branch_ids', ids);

    previewImage(c.image_url || c.image);

    if (window.toastr) { try { toastr.success(@json(__('adminlte::adminlte.saved_successfully'))); } catch(_){} }
    console.log('[categories] form updated', c);
  };

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('category-form');
    if (!form) return;

    const channel = form.dataset.channel || 'categories';
    let events;
    try { events = JSON.parse(form.dataset.events || '["category_updated"]'); }
    catch { events = ['category_updated']; }
    if (!Array.isArray(events) || events.length === 0) events = ['category_updated'];

    // ORDER OF FALLBACKS: data-* → <meta> → server-rendered config from PHP
    const dataKey     = form.dataset.pusherKey || '';
    const dataCluster = form.dataset.pusherCluster || '';
    const metaKey     = document.querySelector('meta[name="pusher-key"]')?.content || '';
    const metaCluster = document.querySelector('meta[name="pusher-cluster"]')?.content || '';
    const cfgKey      = @json($broadcast['pusher_key'] ?? '');
    const cfgCluster  = @json($broadcast['pusher_cluster'] ?? 'mt1');

    const key     = dataKey     || metaKey     || cfgKey;
    const cluster = dataCluster || metaCluster || cfgCluster;

    if (!key || !cluster) {
      console.warn('[categories] Missing Pusher key/cluster. Provide data-pusher-key/data-pusher-cluster or <meta> tags or config/broadcasting.php');
      return;
    }

    // Wait for Pusher to load if not yet present
    const ensurePusher = () => new Promise((resolve, reject) => {
      if (window.Pusher) return resolve();
      const check = setInterval(() => {
        if (window.Pusher) { clearInterval(check); resolve(); }
      }, 50);
      setTimeout(() => { clearInterval(check); if (!window.Pusher) reject(new Error('Pusher JS not loaded')); }, 5000);
    });

    ensurePusher()
      .then(() => {
        // eslint-disable-next-line no-undef
        const pusher = new Pusher(key, { cluster, forceTLS: true });
        const ch = pusher.subscribe(channel);

        events.forEach(ev => {
          ch.bind(ev,               e => applyPayload(e));
          ch.bind(ev.toLowerCase(), e => applyPayload(e));
          ch.bind('.' + ev,         e => applyPayload(e));
        });

        console.info(`[categories] Pusher listening on "${channel}" for`, events);
      })
      .catch((e) => console.error('[categories] Failed to init Pusher:', e));
  });
})();
</script>