@extends('adminlte::page')

@section('title', __('adminlte::adminlte.employee_module'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        {{ __('adminlte::adminlte.employee') }} #{{ $employee->id }}
    </h1>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>{{ __('adminlte::adminlte.go_back') }}
    </a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @php
            $isAr = app()->getLocale() === 'ar';

            // Determine which image to show
            $avatar = null;

            if (!empty($employee->avatar_url)) {
                $avatar = $employee->avatar_url;
            } elseif (!empty($employee->image)) {
                $avatar = asset('storage/' . $employee->image);
            } else {
                $avatar = asset('images/logo_image.png');
            }

            // Country/City display (safe)
            $countryName = null;
            if (!empty($employee->country)) {
                $countryName = $isAr
                    ? ($employee->country->name_ar ?? $employee->country->name_en ?? $employee->country->name ?? null)
                    : ($employee->country->name_en ?? $employee->country->name_ar ?? $employee->country->name ?? null);
            } elseif (!empty($employee->country_name)) {
                $countryName = $employee->country_name;
            }

            $cityName = null;
            if (!empty($employee->city)) {
                $cityName = $isAr
                    ? ($employee->city->name_ar ?? $employee->city->name_en ?? $employee->city->name ?? null)
                    : ($employee->city->name_en ?? $employee->city->name_ar ?? $employee->city->name ?? null);
            } elseif (!empty($employee->city_name)) {
                $cityName = $employee->city_name;
            }
        @endphp

        <div class="d-flex gap-3 align-items-center mb-3" style="padding: 5px">
            <img src="{{ $avatar }}" alt="avatar"
                 id="employee-avatar"
                 class="rounded-circle border"
                 style="width:70px;height:70px;object-fit:cover;margin:5px;"
                 data-placeholder="{{ $avatar }}">
            <div class="w-100">
                <div id="employee-name" class="h5 mb-1">{{ $employee->name }}</div>
                <div id="employee-email" class="text-muted">{{ $employee->email }}</div>

                {{-- ✅ Country + City --}}
                <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-light text-dark border">
                        <i class="fas fa-flag me-1"></i>
                        <span class="text-muted me-1">{{ __('adminlte::adminlte.country') }}:</span>
                        <span id="employee-country">
                            {{ $countryName ?: '—' }}
                        </span>
                    </span>

                    <span class="badge bg-light text-dark border">
                        <i class="fas fa-city me-1"></i>
                        <span class="text-muted me-1">{{ __('adminlte::adminlte.city') }}:</span>
                        <span id="employee-city">
                            {{ $cityName ?: '—' }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <h6 class="fw-bold">{{ __('adminlte::adminlte.permissions') }}</h6>

        <div id="employee-permissions">
            @if($employee->permissions->isEmpty())
                <div class="text-muted">{{ __('adminlte::menu.permissions') }}</div>
            @else
                <ul class="mb-0">
                    @foreach($employee->permissions as $p)
                        <li>{{ $p->display_name ?? ($p->name_en ?: $p->name_ar) }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

{{-- Listener anchor for broadcasting --}}
<div id="employee-listener"
     data-channel="employees"
     data-events='["employee_updated","EmployeeUpdated"]'
     data-employee-id="{{ $employee->id }}">
</div>
@endsection

@push('js')
<script>
(function () {
    'use strict';

    const IS_AR = @json(app()->getLocale() === 'ar');

    function norm(v) {
        if (v === undefined || v === null) return '';
        return String(v);
    }

    function pickLocalizedName(obj) {
        if (!obj) return '';
        // supports {name_en,name_ar} or {name}
        if (IS_AR) return norm(obj.name_ar || obj.name_en || obj.name || '');
        return norm(obj.name_en || obj.name_ar || obj.name || '');
    }

    function resolveCountryName(e) {
        // supports many shapes:
        // e.country (object), e.country_name (string), e.countryName, e.country_label
        if (e.country && typeof e.country === 'object') return pickLocalizedName(e.country);
        return norm(e.country_name || e.countryName || e.country_label || '');
    }

    function resolveCityName(e) {
        if (e.city && typeof e.city === 'object') return pickLocalizedName(e.city);
        return norm(e.city_name || e.cityName || e.city_label || '');
    }

    function renderPermissions(container, permissions) {
        if (!container) return;

        if (!Array.isArray(permissions) || permissions.length === 0) {
            container.innerHTML = '<div class="text-muted">{{ __('adminlte::menu.permissions') }}</div>';
            return;
        }

        const ul = document.createElement('ul');
        ul.className = 'mb-0';

        permissions.forEach(p => {
            const li = document.createElement('li');
            const display = p.display_name || p.name_en || p.name_ar || p.name || '';
            li.textContent = display;
            ul.appendChild(li);
        });

        container.innerHTML = '';
        container.appendChild(ul);
    }

    function updateDomFromPayload(payload) {
        if (!payload) return;

        const e = payload.employee ?? payload ?? {};

        // Ensure it's the same employee
        const anchor = document.getElementById('employee-listener');
        const currentId = anchor ? anchor.dataset.employeeId : null;
        if (currentId && e.id && String(e.id) !== String(currentId)) {
            return; // different employee, ignore
        }

        // Name
        const nameEl = document.getElementById('employee-name');
        if (nameEl && e.name !== undefined) nameEl.textContent = norm(e.name);

        // Email
        const emailEl = document.getElementById('employee-email');
        if (emailEl && e.email !== undefined) emailEl.textContent = norm(e.email);

        // ✅ Country
        const countryEl = document.getElementById('employee-country');
        if (countryEl) {
            const cn = resolveCountryName(e);
            if (cn !== '') countryEl.textContent = cn;
            // لو رجع فاضي وما بدك يمسح القيمة القديمة، اتركها زي ما هي
        }

        // ✅ City
        const cityEl = document.getElementById('employee-city');
        if (cityEl) {
            const ctn = resolveCityName(e);
            if (ctn !== '') cityEl.textContent = ctn;
        }

        // Avatar
        const avatarEl = document.getElementById('employee-avatar');
        if (avatarEl) {
            const newSrc = e.avatar_url || e.image_url || e.image || avatarEl.dataset.placeholder;
            if (newSrc) avatarEl.src = newSrc;
        }

        // Permissions
        const permsContainer = document.getElementById('employee-permissions');
        if (permsContainer && (Array.isArray(e.permissions) || e.permissions === null || e.permissions === undefined)) {
            renderPermissions(permsContainer, e.permissions || []);
        }

        if (window.toastr) {
            toastr.success(@json(__('adminlte::adminlte.saved_successfully')));
        }

        console.log('[employee show] updated from broadcast payload', e);
    }

    // Optional: expose globally
    window.updateEmployeeShow = updateDomFromPayload;

    document.addEventListener('DOMContentLoaded', function () {
        const anchor = document.getElementById('employee-listener');
        if (!anchor) {
            console.warn('[employee show] listener anchor not found');
            return;
        }

        window.__pageBroadcasts = window.__pageBroadcasts || [];

        let events;
        try {
            events = JSON.parse(anchor.dataset.events || '["employee_updated"]');
        } catch (_) {
            events = ['employee_updated'];
        }
        if (!Array.isArray(events) || !events.length) {
            events = ['employee_updated'];
        }

        const handler = function (eventPayload) {
            // بعض المشاريع تبعث payload كامل أو payload.employee
            updateDomFromPayload(eventPayload && (eventPayload.employee ?? eventPayload));
        };

        window.__pageBroadcasts.push({
            channel: 'employees',
            event:   'employee_updated',
            handler: handler
        });

        if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
            window.AppBroadcast.subscribe('employees', 'employee_updated', handler);
            console.info('[employee show] subscribed via AppBroadcast → employees / employee_updated');
        } else {
            console.info('[employee show] registered in __pageBroadcasts; layout will subscribe later.');
        }
    });
})();
</script>
@endpush
