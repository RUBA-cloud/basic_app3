{{-- resources/views/livewire/dashboard/custom-dashboard.blade.php --}}
<div class="container-fluid {{ app()->getLocale()==='ar' ? 'text-right' : '' }}" {{ $autoRefresh ? 'wire:poll.30s' : '' }}>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">

        <div class="d-flex gap-2">
            <button wire:click="$dispatch('dashboard:refresh')" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-sync-alt me-1"></i> {{ __('adminlte::adminlte.refresh') }}
            </button>
            <button wire:click="$toggle('autoRefresh')" class="btn btn-outline-{{ $autoRefresh ? 'success' : 'secondary' }} btn-sm">
                <i class="far fa-clock me-1"></i>

                {{ $autoRefresh ? __('adminlte::adminlte.auto_refresh') : __('adminlte::adminlte.auto_refresh_off') }}
            </button>
        </div>
    </div>

    <div class="row">
        {{-- Newest 10 Orders --}}
        <div class="col-12 col-xl-6">
            <x-adminlte-card title="{{ __('adminlte::adminlte.newest_10_Orders') }}"
                             theme="primary" icon="fas fa-shopping-cart" removable collapsible>
                <div class="table-responsive">
                    <table class="table table-sm table-hover text-nowrap mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('adminlte::adminlte.user_name') }}</th>
                            <th>{{ __('adminlte::adminlte.status') }}</th>
                            <th class="{{ app()->getLocale()==='ar' ? 'text-left' : 'text-end' }}">{{ __('adminlte::adminlte.Total') }}</th>
                            <th>{{ __('adminlte::adminlte.created_at') }}</th>
                            <th>{{ __('adminlte::adminlte.action') }}</th>
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
                                <td>{{ $o->user->name ?? '-' }}</td>
                                <td><span class="badge badge-{{ $badge }}">{{ $o->status_label ?? ucfirst($o->status) }}</span></td>
                                <td class="{{ app()->getLocale()==='ar' ? 'text-left' : 'text-end' }}">{{ $money($total) }}</td>
                                <td><small class="text-muted">{{ optional($o->created_at)->diffForHumans() }}</small></td>
                                <td>
                                    @if(Route::has('orders.show'))
                                        <a href="{{ route('orders.show', $o->id) }}" class="btn btn-xs btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">{{ __('adminlte::adminlte.no_data_found') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Newest 10 Completed Orders --}}
        <div class="col-12 col-xl-6">
            <x-adminlte-card title="{{ __('adminlte::adminlte.newest_10_completed_orders') }}"
                             theme="success" icon="fas fa-check-circle" removable collapsible>
                <div class="table-responsive">
                    <table class="table table-sm table-hover text-nowrap mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('adminlte::adminlte.user_name') }}</th>
                            <th>{{ __('adminlte::adminlte.status') }}</th>
                            <th class="{{ app()->getLocale()==='ar' ? 'text-left' : 'text-end' }}">{{ __('adminlte::adminlte.Total') }}</th>
                            <th>{{ __('adminlte::adminlte.updated_at') }}</th>
                            <th>{{ __('adminlte::adminlte.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($completedOrders as $o)
                            @php $total = $o->total_price ?? $o->total ?? null; @endphp
                            <tr>
                                <td>{{ $o->id }}</td>
                                <td>{{ $o->user->name ?? '-' }}</td>
                                <td><span class="badge badge-success">{{ $o->status_label ?? ucfirst($o->status) }}</span></td>
                                <td class="{{ app()->getLocale()==='ar' ? 'text-left' : 'text-end' }}">{{ $money($total) }}</td>
                                <td><small class="text-muted">{{ optional($o->updated_at)->diffForHumans() }}</small></td>
                                <td>
                                    @if(Route::has('orders.show'))
                                        <a href="{{ route('orders.show', $o->id) }}" class="btn btn-xs btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">{{ __('adminlte::adminlte.no_data_found') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    <div class="row">
        {{-- Newest 10 Users --}}
        <div class="col-12 col-xl-6">
            <x-adminlte-card title="{{ __('adminlte::adminlte.newest_10_users') }}"
                             theme="info" icon="fas fa-users" removable collapsible>
                <div class="table-responsive">
                    <table class="table table-sm table-hover text-nowrap mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('adminlte::adminlte.user_name') }}</th>
                            <th>{{ __('adminlte::adminlte.email') }}</th>
                            <th>{{ __('adminlte::adminlte.joined_at') }}</th>
                            <th>{{ __('adminlte::adminlte.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($newUsers as $u)
                            <tr>
                                <td>{{ $u->id }}</td>
                                <td>{{ $u->name }}</td>
                                <td><small>{{ $u->email }}</small></td>
                                <td><small class="text-muted">{{ optional($u->created_at)->diffForHumans() }}</small></td>
                                <td>
                                    @if(Route::has('users.show'))
                                        <a href="{{ route('users.show', $u->id) }}" class="btn btn-xs btn-primary">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">{{ __('adminlte::adminlte.no_data_found') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Newest 10 Products --}}
        <div class="col-12 col-xl-6">
            <x-adminlte-card title="{{ __('adminlte::adminlte.newest_10_Orders') }}"
                             theme="warning" icon="fas fa-box" removable collapsible>
                <div class="table-responsive">
                    <table class="table table-sm table-hover text-nowrap mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('adminlte::adminlte.name_en') }}</th>
                                  <th>{{ __('adminlte::adminlte.name_ar') }}</th>
                            <th class="{{ app()->getLocale()==='ar' ? 'text-left' : 'text-end' }}">{{ __('adminlte::adminlte.price') }}</th>
                            <th>{{ __('adminlte::adminlte.created_at') }}</th>
                            <th>{{ __('adminlte::adminlte.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($newProducts as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td>{{ $p->name_en }}</td>
                                <td class="{{ app()->getLocale()==='ar' ? 'text-left' : 'text-end' }}">{{ $money($p->price ?? null) }}</td>
                                <td><small class="text-muted">{{ optional($p->created_at)->diffForHumans() }}</small></td>
                                <td>
                                    @if(Route::has('products.show'))
                                        <a href="{{ route('products.show', $p->id) }}" class="btn btn-xs btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">{{ __('adminlte::adminlte.no_data_found') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>
    </div>
</div>
