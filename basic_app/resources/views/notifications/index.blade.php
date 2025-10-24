{{-- resources/views/notifications/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('Notifications'))

{{-- ensure csrf meta in your master layout head --}}
{{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">{{ __('Notifications') }}</h1>
    <form action="{{ route('notifications.markAll') }}" method="POST" class="m-0">
      @csrf
      <button class="btn btn-outline-secondary btn-sm">{{ __('Mark all read') }}</button>
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
            <option value="all"    @selected(request('filter')==='all')>{{ __('All') }}</option>
            <option value="unread" @selected(request('filter')==='unread')>{{ __('Unread') }}</option>
          </select>
        </div>
      </div>
    </form>

    <div id="notifList" class="list-group">
      @forelse($items as $n)
        <div class="list-group-item d-flex align-items-start {{ is_null($n->read_at) ? 'bg-light' : '' }}"
             style="margin:6px;border-radius:10px;box-shadow:0 2px 4px rgba(0,0,0,.08)">
          <i class="{{ $n->icon ?: 'fas fa-bell' }} me-3 mt-1" style="padding: 5px; margin:5px"></i>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <strong>{{ $n->title }}</strong>
              <small class="text-muted">{{ $n->created_at->format('Y-m-d H:i') }}</small>
            </div>
            @if($n->body)
              <div class="text-muted">{{ $n->body }}</div>
            @endif
            <div class="mt-2 d-flex flex-wrap gap-2" style="padding: 5px">
              @if($n->link)
                <a href="{{ $n->link }}" class="btn btn-sm btn-primary" style="padding: 5px">{{ __('Open') }}</a>
              @endif
              @if(is_null($n->read_at))
                <form action="{{ route('notifications.mark', $n) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-outline-secondary" style="padding:5px">{{ __('Mark read') }}</button>
                </form>
              @endif
              <form action="{{ route('notifications.destroy', $n) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('{{ __('Are you sure?') }}');"  style="padding: 5px">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger". style="padding:5px">{{ __('Delete') }}</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="text-center text-muted py-4">{{ __('No notifications') }}</div>
      @endforelse
    </div>

    <div class="mt-3">
      {{ $items->links() }}
    </div>

  </div>
</div>
@stop

@push('js')
  {{-- Pusher CDN (load once) --}}
  <script>
    (function ensurePusher(){
      if (window._pusherLoaderAdded) return;
      window._pusherLoaderAdded = true;
      const s = document.createElement('script');
      s.src = 'https://js.pusher.com/8.4/pusher.min.js';
      s.async = true;
      document.head.appendChild(s);
    })();
  </script>

  <script>
    (function() {
      const list = document.getElementById('notifList');
      if (!list) return;

      // Blade route templates for forms created in JS
      const ROUTES = {
        mark:    @json(route('notifications.mark',    ['notification' => '__ID__'])),
        destroy: @json(route('notifications.destroy', ['notification' => '__ID__'])),
      };

      const csrf = (document.querySelector('meta[name="csrf-token"]')||{}).content || '';
      const esc  = s => String(s ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;');
      const fmt  = (dt) => {
        try { const d=new Date(dt); const pad=n=>String(n).padStart(2,'0');
          return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
        } catch { return ''; }
      };

      function makeItem(n) {
        const wrap = document.createElement('div');
        wrap.className = 'list-group-item d-flex align-items-start bg-light';
        Object.assign(wrap.style,{margin:'6px',borderRadius:'10px',boxShadow:'0 2px 4px rgba(0,0,0,.08)'});

        const markUrl    = ROUTES.mark.replace('__ID__',    esc(n.id));
        const destroyUrl = ROUTES.destroy.replace('__ID__', esc(n.id));

        wrap.innerHTML = `
          <i class="${esc(n.icon || 'fas fa-bell')} me-3 mt-1"></i>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <strong>${esc(n.title || '{{ __("Notification") }}')}</strong>
              <small class="text-muted">${fmt(n.created_at)}</small>
            </div>
            ${n.body ? `<div class="text-muted">${esc(n.body)}</div>` : ''}
            <div class="mt-2 d-flex flex-wrap gap-2">
              ${n.link ? `<a href="${esc(n.link)}" class="btn btn-sm btn-primary">{{ __('Open') }}</a>` : ''}
              ${!n.read_at ? `
                <form action="${markUrl}" method="POST" class="d-inline">
                  <input type="hidden" name="_token" value="${csrf}">
                  <button class="btn btn-sm btn-outline-secondary">{{ __('Mark read') }}</button>
                </form>` : ''}
              <form action="${destroyUrl}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                <input type="hidden" name="_token" value="${csrf}">
                <input type="hidden" name="_method" value="DELETE">
                <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
              </form>
            </div>
          </div>
        `;
        return wrap;
      }

      const key     = @json(config('broadcasting.connections.pusher.key'));
      const cluster = @json(config('broadcasting.connections.pusher.options.cluster', 'mt1'));
      const userId  = @json((int)auth()->id());
      if (!key || !cluster || !userId) return;

      const ensurePusher = () => new Promise((res,rej)=>{
        if (window.Pusher) return res();
        const it=setInterval(()=>{ if(window.Pusher){clearInterval(it);res();}},50);
        setTimeout(()=>{clearInterval(it); if(!window.Pusher) rej(new Error('Pusher not loaded'));},6000);
      });

      ensurePusher().then(()=>{
        // eslint-disable-next-line no-undef
        const p = new Pusher(key, {
          cluster,
          forceTLS: true,
          authEndpoint: '{{ url('/broadcasting/auth') }}',
          auth: { headers: { 'X-CSRF-TOKEN': csrf } }
        });

        const channelName = 'private-notifications.user.' + userId;
        const ch = p.subscribe(channelName);

        function onEvent(e){
          const el = makeItem(e);
          list.insertBefore(el, list.firstChild);
          if (window.toastr) toastr.info(e.title || '{{ __("Notification") }}');
        }

        ch.bind('notification.created', onEvent);
        ch.bind('.notification.created', onEvent);

        console.info('[notifications] listening:', channelName, 'event: notification.created');
      }).catch(err=>console.error('[notifications] pusher init failed', err));
    })();
  </script>
@endpush
