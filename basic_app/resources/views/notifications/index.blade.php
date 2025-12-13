@extends('adminlte::page')

@section('title', __('Notifications'))
@php
    $pusher_key     = config('broadcasting.connections.pusher.key'); // kept if needed elsewhere
    $pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');
@endphp

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
  <h1 class="m-0">{{ __('adminlte::adminlte.Notifications') }}</h1>
  <form action="{{ route('notifications.markAll') }}" method="POST" class="m-0">
    @csrf
    <button class="btn btn-outline-secondary btn-sm">{{ __('adminlte::adminlte.mark_all_as_read') }}</button>
  </form>
</div>
@stop

@section('content')
<div class="card">
  <div class="card-body">

    <form class="mb-3" method="GET" style="margin: 5px; padding: 5px;">
      <div class="row g-2">
        <div class="col-auto">
          <select name="filter" class="form-control" onchange="this.form.submit()">
            <option value="all"    @selected(request('filter')==='all')>{{ __('adminlte::adminlte.all') }}</option>
            <option value="unread" @selected(request('filter')==='unread')>{{ __('adminlte::adminlte.unread') }}</option>
          </select>
        </div>
      </div>
    </form>

    <div id="notifList" class="list-group">
      @forelse($items as $n)
        <div class="list-group-item d-flex align-items-start {{ is_null($n->read_at) ? 'bg-light' : '' }}"
             style="margin:6px;border-radius:10px;box-shadow:0 2px 4px rgba(0,0,0,.08)">
          <i class="{{ $n->icon ?: 'fas fa-bell' }} me-3 mt-1"></i>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <strong>{{ $n->title }}</strong>
              <small class="text-muted">{{ $n->created_at->format('Y-m-d H:i') }}</small>
            </div>
            @if($n->body)
              <div class="text-muted">{{ $n->body }}</div>
            @endif
            <div class="mt-2 d-flex flex-wrap gap-2">
              @if($n->link)
                <a href="{{ $n->link }}" class="btn btn-sm btn-primary" style="margin: 5px">{{ __('adminlte::adminlte.open') }}</a>
              @endif
              @if(is_null($n->read_at))
                <form action="{{ route('notifications.mark', $n) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-outline-secondary" style="margin: 5px">{{ __('adminlte::adminlte.mark_as_read') }}</button>
                </form>
              @endif
              <form action="{{ route('notifications.destroy', $n) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('{{ __('Are you sure?') }}');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" style="margin: 5px">{{__('adminlte::adminlte.delete') }}</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="text-center text-muted py-4">{{ __('adminlte::adminlte.Notifications') }}</div>
      @endforelse
    </div>

    <div class="mt-3">
      {{ $items->links() }}
    </div>
  </div>
</div>

{{-- Listener anchor --}}
<div id="notifications-listener"
     data-channel="notifications"
     data-events='["notification.created","NotificationCreated"]'
     data-user-id="{{ auth()->id() }}">
</div>
@stop

@push('js')
@once
<script>
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  const userId = @json(auth()->id());
  if (!userId) return;

  const channelName = `notifications.user.${userId}`;
  const eventName   = 'notification.created';

  const escapeHtml = (s) => String(s || '').replace(/[&<>"']/g, (c) =>
    ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]
  );

  const refreshBell = (payload) => {
    const OPEN_LABEL = @json(__('adminlte::adminlte.open'));
    const list = document.getElementById('notifList');
    if (!list) return;

    // If payload provided (some broadcasters pass the notification payload), build and prepend an item
    if (payload && (payload.notification || payload.data)) {
      const n = payload.notification || payload.data;
      const item = document.createElement('div');
      item.className = 'list-group-item d-flex align-items-start ' + (n.read_at ? '' : 'bg-light');
      item.style.margin = '6px';
      item.style.borderRadius = '10px';
      item.style.boxShadow = '0 2px 4px rgba(0,0,0,.08)';

      const icon = escapeHtml(n.icon || 'fas fa-bell');
      const title = escapeHtml(n.title || '');
      const created = escapeHtml(n.created_at || (new Date()).toISOString().slice(0,16).replace('T',' '));
      const body = n.body ? `<div class="text-muted">${escapeHtml(n.body)}</div>` : '';
      const linkBtn = n.link ? `<a href="${escapeHtml(n.link)}" class="btn btn-sm btn-primary" style="margin: 5px">${OPEN_LABEL}</a>` : '';

      item.innerHTML = `
            <i class="${icon} me-3 mt-1"></i>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                    <strong>${title}</strong>
                    <small class="text-muted">${created}</small>
                </div>
                ${body}
                <div class="mt-2 d-flex flex-wrap gap-2">
                    ${linkBtn}
                </div>
            </div>
      `;

      list.insertBefore(item, list.firstChild);
      return;
    }

    // Fallback: reload notification list from current page (AJAX). If that fails, reload the page.
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => res.text())
      .then(html => {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        const newList = tmp.querySelector('#notifList');
        if (newList) {
          list.innerHTML = newList.innerHTML;
        } else {
          // if server didn't return fragment, reload whole page
          window.location.reload();
        }
      })
      .catch(err => {
        console.warn('[notifications] refresh failed, reloading page', err);
        window.location.reload();
      });
  };

  // Register in-page broadcast hooks (if your app uses them)
  window.__pageBroadcasts = window.__pageBroadcasts || [];
  window.__pageBroadcasts.push({
    channel: channelName,
    event: eventName,
    handler: function (payload) {
      console.log('[notifications] received', payload);
      refreshBell(payload);
    }
  });

  // If your global AppBroadcast helper exists, subscribe too
  if (window.AppBroadcast && typeof window.AppBroadcast.subscribe === 'function') {
    window.AppBroadcast.subscribe(channelName, eventName, function(payload){
      console.log('[notifications] received', payload);
      refreshBell(payload);
    });
  }
});
</script>
@endonce
@endpush
