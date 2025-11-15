{{-- resources/views/livewire/dashboard/custom-dashboard.blade.php --}}
@php
    $isRtl = app()->getLocale() === 'ar';

    $newOrdersCount       = $newOrders->count();
    $completedOrdersCount = $completedOrders->count();
    $newUsersCount        = $newUsers->count();
    $newProductsCount     = $newProducts->count();
@endphp

<div
    class="container-fluid {{ $isRtl ? 'text-right' : '' }}"
    {{ $autoRefresh ? 'wire:poll.30s' : '' }}
>
    {{-- Top header / controls --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div class="mb-2">
            <h4 class="mb-1 font-weight-bold">
                <i class="fas fa-tachometer-alt mr-2 text-primary"></i>
                {{ __('adminlte::adminlte.dashboard') }}
            </h4>
            <small class="text-muted">
                {{ __('adminlte::adminlte.live_snapshot') ?? __('adminlte::adminlte.latest_updates') }}
            </small>
        </div>

        <div class="d-flex flex-wrap align-items-center">
            {{-- Refresh button --}}
            <button
                wire:click="$dispatch('dashboard:refresh')"
                class="btn btn-outline-secondary btn-sm d-flex align-items-center mr-2 mb-2"
            >
                <i class="fas fa-sync-alt {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                <span>{{ __('adminlte::adminlte.refresh') }}</span>
            </button>

            {{-- Auto refresh toggle --}}
            <button
                wire:click="$toggle('autoRefresh')"
                class="btn btn-sm mb-2 d-flex align-items-center
                    {{ $autoRefresh ? 'btn-success' : 'btn-outline-secondary' }}"
            >
                <i class="far fa-clock {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                <span>
                    {{ $autoRefresh ? __('adminlte::adminlte.auto_refresh') : __('adminlte::adminlte.refresh_off') }}
                </span>
            </button>
        </div>
    </div>

    {{-- Quick stats strip --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-2">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h4 class="mb-1">{{ $newOrdersCount }}</h4>
                    <p class="mb-0">{{ __('adminlte::adminlte.newest_10_Orders') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3 mb-2">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h4 class="mb-1">{{ $completedOrdersCount }}</h4>
                    <p class="mb-0">{{ __('adminlte::adminlte.newest_10_completed_orders') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3 mb-2">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h4 class="mb-1">{{ $newUsersCount }}</h4>
                    <p class="mb-0">{{ __('adminlte::adminlte.newest_10_users') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3 mb-2">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h4 class="mb-1">{{ $newProductsCount }}</h4>
                    <p class="mb-0">{{ __('adminlte::adminlte.newest_10_products') ?? __('adminlte::adminlte.products') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders --}}
    <div class="row">
        {{-- Newest 10 Orders --}}
        <div class="col-12 col-xl-8">
            <x-adminlte-card
                title="{{ __('adminlte::adminlte.newest_10_Orders') }}"
                theme="primary"

                icon="fas fa-shopping-cart"
                removable
                collapsible
                class="lw-list-card"
            >
                <div class="table-responsive-md lw-table-wrapper">
                    <table class="table table-bordered table-hover text-nowrap align-middle lw-table {{ $isRtl ? 'text-right' : 'text-left' }}">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('adminlte::adminlte.user_name') }}</th>
                                <th>{{ __('adminlte::adminlte.status') }}</th>
                                <th class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    {{ __('adminlte::adminlte.Total') }}
                                </th>
                                <th>{{ __('adminlte::adminlte.created_at') }}</th>
                                <th style="width:1%;white-space:nowrap;" class="text-center">
                                    {{ __('adminlte::adminlte.actions') ?: 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($newOrders as $o)
                            @php
                                $total = $o->total_price ?? $o->total ?? null;
                                $badge = match ($o->status) {
                                    'completed','done','paid' => 'success',
                                    'cancelled','rejected'    => 'danger',
                                    'pending'                 => 'warning',
                                    default                   => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $o->id }}</td>
                                <td><strong>{{ $o->user->name ?? '-' }}</strong></td>
                                <td>
                                    <span class="badge lw-pill badge-{{ $badge }}">
                                        <i class="fas fa-circle small"></i>
                                        {{ $o->status_label ?? ucfirst($o->status) }}
                                    </span>
                                </td>
                                <td class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    {{ $money($total) }}
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional($o->created_at)->diffForHumans() }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="lw-actions">
                                        @if(Route::has('orders.show'))
                                            <a href="{{ route('orders.show', $o->id) }}"
                                               class="btn btn-info btn-sm lw-action-btn"
                                               title="{{ __('adminlte::adminlte.details') }}">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-md-inline">
                                                    {{ __('adminlte::adminlte.details') }}
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    {{ __('adminlte::adminlte.no_data_found') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Newest 10 Completed Orders --}}
        <div class="col-12 col-xl-6">
            <x-adminlte-card
                title="{{ __('adminlte::adminlte.newest_10_completed_orders') }}"
                theme="success"
                icon="fas fa-check-circle"
                removable
                collapsible
                class="lw-list-card"
            >
                <div class="table-responsive-md lw-table-wrapper">
                    <table class="table table-bordered table-hover text-nowrap align-middle lw-table {{ $isRtl ? 'text-right' : 'text-left' }}">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('adminlte::adminlte.user_name') }}</th>
                                <th>{{ __('adminlte::adminlte.status') }}</th>
                                <th class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    {{ __('adminlte::adminlte.Total') }}
                                </th>
                                <th>{{ __('adminlte::adminlte.updated_at') }}</th>
                                <th style="width:1%;white-space:nowrap;" class="text-center">
                                    {{ __('adminlte::adminlte.actions') ?: 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($completedOrders as $o)
                            @php $total = $o->total_price ?? $o->total ?? null; @endphp
                            <tr>
                                <td>{{ $o->id }}</td>
                                <td><strong>{{ $o->user->name ?? '-' }}</strong></td>
                                <td>
                                    <span class="badge lw-pill badge-success">
                                        <i class="fas fa-check small"></i>
                                        {{ $o->status_label ?? ucfirst($o->status) }}
                                    </span>
                                </td>
                                <td class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    {{ $money($total) }}
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional($o->updated_at)->diffForHumans() }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="lw-actions">
                                        @if(Route::has('orders.show'))
                                            <a href="{{ route('orders.show', $o->id) }}"
                                               class="btn btn-info btn-sm lw-action-btn"
                                               title="{{ __('adminlte::adminlte.details') }}">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-md-inline">
                                                    {{ __('adminlte::adminlte.details') }}
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    {{ __('adminlte::adminlte.no_data_found') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    {{-- Users & Products --}}
    <div class="row">
        {{-- Newest 10 Users --}}
        <div class="col-12 col-xl-6">
            <x-adminlte-card
                title="{{ __('adminlte::adminlte.newest_10_users') }}"
                theme="info"
                icon="fas fa-users"
                removable
                collapsible
                class="lw-list-card"
            >
                <div class="table-responsive-md lw-table-wrapper">
                    <table class="table table-bordered table-hover text-nowrap align-middle lw-table {{ $isRtl ? 'text-right' : 'text-left' }}">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('adminlte::adminlte.user_name') }}</th>
                                <th>{{ __('adminlte::adminlte.email') }}</th>
                                <th>{{ __('adminlte::adminlte.joined_at') }}</th>
                                <th style="width:1%;white-space:nowrap;" class="text-center">
                                    {{ __('adminlte::adminlte.actions') ?: 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($newUsers as $u)
                            <tr>
                                <td>{{ $u->id }}</td>
                                <td><strong>{{ $u->name }}</strong></td>
                                <td><small>{{ $u->email }}</small></td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional($u->created_at)->diffForHumans() }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="lw-actions">
                                        @if(Route::has('users.show'))
                                            <a href="{{ route('users.show', $u->id) }}"
                                               class="btn btn-info btn-sm lw-action-btn"
                                               title="{{ __('adminlte::adminlte.details') }}">
                                                <i class="fas fa-user"></i>
                                                <span class="d-none d-md-inline">
                                                    {{ __('adminlte::adminlte.details') }}
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    {{ __('adminlte::adminlte.no_data_found') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Newest 10 Products --}}
        <div class="col-12 col-xl-6">
            <x-adminlte-card
                title="{{ __('adminlte::adminlte.newest_10_products') ?? __('adminlte::adminlte.products') }}"
                theme="warning"
                icon="fas fa-box"
                removable
                collapsible
                class="lw-list-card"
            >
                <div class="table-responsive-md lw-table-wrapper">
                    <table class="table table-bordered table-hover text-nowrap align-middle lw-table {{ $isRtl ? 'text-right' : 'text-left' }}">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('adminlte::adminlte.name_en') }}</th>
                                <th>{{ __('adminlte::adminlte.name_ar') }}</th>
                                <th class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    {{ __('adminlte::adminlte.price') }}
                                </th>
                                <th>{{ __('adminlte::adminlte.created_at') }}</th>
                                <th style="width:1%;white-space:nowrap;" class="text-center">
                                    {{ __('adminlte::adminlte.actions') ?: 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($newProducts as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td><strong>{{ $p->name_en }}</strong></td>
                                <td>{{ $p->name_ar }}</td>
                                <td class="{{ $isRtl ? 'text-left' : 'text-end' }}">
                                    {{ $money($p->price ?? null) }}
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional($p->created_at)->diffForHumans() }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="lw-actions">
                                        @if(Route::has('products.show'))
                                            <a href="{{ route('products.show', $p->id) }}"
                                               class="btn btn-info btn-sm lw-action-btn"
                                               title="{{ __('adminlte::adminlte.details') }}">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-md-inline">
                                                    {{ __('adminlte::adminlte.details') }}
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    {{ __('adminlte::adminlte.no_data_found') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    {{-- Optional: lightweight JS hooks from your generic list table (kept for reuse) --}}
    <script wire:ignore>
        window.addEventListener('show-details-modal', () => {
            const el = document.getElementById('detailsModal');
            if (!el) return;
            const modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();
        });

        window.addEventListener('toast', (e) => {
            const { type = 'info', message = '' } = e.detail || {};
            if (message) { alert(message); }
        });
    </script>
</div>
