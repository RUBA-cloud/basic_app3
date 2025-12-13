@php use Illuminate\Support\Str; @endphp

<li class="nav-item dropdown"
    wire:key="notifications-bell"
    style="position: relative;"
>
    {{-- Bell --}}
    <a class="nav-link" href="#" onclick="return false;">
        <i class="far fa-bell"></i>

        @if($count > 0)
            <span class="badge navbar-badge badge-danger bg-danger">
                {{ $count }}
            </span>
        @endif
    </a>

    {{-- Dropdown --}}
    <div
        style="
            max-width:360px;
            max-height:480px;
            overflow:hidden;
            display:block;
            top:100%;
            {{ app()->getLocale()==='ar' ? 'left:0;' : 'right:0;' }}
            z-index:1035;
        "
    >
        <div class="px-3 py-2 d-flex justify-content-between">
            <strong>{{ __('Notifications') }}</strong>

            <button class="btn btn-link btn-sm"
                    wire:click="markAllAsRead"
                    wire:loading.attr="disabled">
                {{ __('Mark all as read') }}
            </button>
        </div>

        <div class="list-group list-group-flush" style="max-height:380px; overflow:auto;">
            @forelse($items as $n)
                <a href="{{ $n->data['link'] ?? '#' }}"
                   wire:key="notif-{{ $n->id }}"
                   class="list-group-item list-group-item-action {{ is_null($n->read_at) ? 'bg-light' : '' }}"
                   wire:click.prevent="markAsRead('{{ $n->id }}')"
                >
                    <div class="d-flex align-items-start">
                        <i class="fas fa-bell mr-2 mt-1"></i>

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $n->data['title'] ?? 'Notification' }}</strong>
                                <small class="text-muted">
                                    {{ $n->created_at->diffForHumans() }}
                                </small>
                            </div>

                            @if(!empty($n->data['body']))
                                <div class="small text-muted">
                                    {{ Str::limit($n->data['body'], 120) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center text-muted py-3">
                    {{ __('No notifications') }}
                </div>
            @endforelse
        </div>

        <div class="dropdown-divider m-0"></div>

        <div class="px-3 py-2 d-flex justify-content-between">
            <a href="{{ route('notifications.index') }}" class="small">
                {{ __('View all') }}
            </a>

            @if($items->hasMorePages())
                <button class="btn btn-sm btn-outline-secondary"
                        wire:click="loadMore"
                        wire:loading.attr="disabled">
                    {{ __('Load more') }}
                </button>
            @endif
        </div>
    </div>
</li>

@push('js')
@once
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (!window.Echo) return;

  const userId = @json(auth()->id());
  if (!userId) return;

  const channel = `notifications.user.${userId}`;

  const refresh = () => {
    if (window.Livewire?.dispatch) {
      window.Livewire.dispatch('notifications:refresh'); // Livewire v3
    } else if (window.Livewire?.emit) {
      window.Livewire.emit('notifications:refresh');     // Livewire v2
    }
  };

  window.private(channel)
    .listen('.notification.created', () => {
      refresh();
    });

  console.info('[notifications] listening on', channel);
});
</script>
@endonce
@endpush
