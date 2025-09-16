resources/views/livewire/notifications/bell.blade.php
<li class="nav-item dropdown" wire:poll.20s>
    {{-- Toggle --}}
    <a id="notifDropdownToggle"
       class="nav-link"
       href="#"
       wire:click.prevent="toggle"
       role="button"
       aria-haspopup="true"
       aria-expanded="{{ $show ? 'true':'false' }}"
       aria-controls="notifDropdownMenu">
        <i class="far fa-bell"></i>

        @if($this->count > 0)
            <span class="badge navbar-badge badge-danger bg-danger">{{ $this->count }}</span>
        @endif
    </a>

    {{-- Menu --}}
    <div
        id="notifDropdownMenu"
        class="dropdown-menu dropdown-menu-lg {{ app()->getLocale()==='ar' ? 'dropdown-menu-start' : 'dropdown-menu-end dropdown-menu-right' }} {{ $show ? 'show' : '' }}"
        style="max-width: 360px;"
        role="menu"
        aria-labelledby="notifDropdownToggle"
        {{-- close on outside click --}}
        x-data
        x-init="
            // close on escape
            document.addEventListener('keydown', e => { if(e.key==='Escape'){ $wire.set('show', false) }});
            // close on outside click
            document.addEventListener('click', e => {
                const item = e.target.closest('li.nav-item.dropdown');
                if(!item){ $wire.set('show', false); }
            });
        "
    >
        <div class="px-3 py-2 d-flex align-items-center justify-content-between">
            <span class="fw-bold">{{ __('Notifications') }}</span>

            <button class="btn btn-link btn-sm p-0"
                    wire:click="markAllAsRead"
                    wire:loading.attr="disabled">
                {{ __('Mark all as read') }}
            </button>
        </div>

        <div class="list-group list-group-flush" style="max-height: 380px; overflow:auto;">
            @forelse($items as $n)
                <a
                    href="{{ $n->link ?: '#' }}"
                    wire:key="notification-{{ $n->id }}"
                    @class([
                        'list-group-item',
                        'list-group-item-action',
                        'bg-light' => is_null($n->read_at),
                    ])
                    {{-- If you want to navigate after marking as read,
                         change to wire:click.prevent="open({{ $n->id }})"
                         and implement open($id) in the component to mark + redirect. --}}
                    wire:click.prevent="markAsRead({{ $n->id }})"
                >
                    <div class="d-flex align-items-start">
                        <i class="{{ $n->icon ?: 'fas fa-bell' }} me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $n->title }}</strong>
                                <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                            </div>
                            @if($n->body)
                                <div class="small text-muted">{{ Str::limit($n->body, 120) }}</div>
                            @endif>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center text-muted py-3">{{ __('No notifications') }}</div>
            @endforelse
        </div>

        <div class="dropdown-divider m-0"></div>

        <div class="px-3 py-2 d-flex justify-content-between align-items-center">
            <a href="{{ route('notifications.index') }}" class="small">{{ __('View all') }}</a>

            @if($items->hasMorePages())
                <button class="btn btn-sm btn-outline-secondary"
                        wire:click="$set('perPage', {{ $perPage + 8 }})"
                        wire:loading.attr="disabled">
                    {{ __('Load more') }}
                </button>
            @endif
        </div>
    </div>
</li>
