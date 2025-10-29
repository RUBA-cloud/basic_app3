{{-- resources/views/chat/chat-message.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.chat'))

@section('adminlte_css')
<style>
/* ===== Layout shell (page can scroll) ===== */
.chat-shell {
  min-height: 60vh;
  background: #f7f7fb;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: visible;
}
.chat-grid  {
  display: grid;
  grid-template-columns: 320px 1fr;
}
@media (max-width: 992px) {
  .chat-grid { grid-template-columns: 1fr; }
}

/* ===== Left: users list ===== */
.users-pane { background: #fff; border-right: 1px solid #e5e7eb; display: grid; grid-template-rows: auto 1fr; }
.users-head { padding: .75rem; border-bottom: 1px solid #eef0f4; display:flex; gap:.5rem; align-items:center; }
.users-head .search { flex:1; }
.users-list { overflow-y: auto; padding: .5rem; }
.user-row { display: grid; grid-template-columns: 44px 1fr auto; gap: .5rem; padding: .55rem; border-radius: .75rem; align-items: center; cursor: pointer; text-decoration:none; color: inherit; }
.user-row:hover { background: #f3f6ff; }
.user-row.active { background: #e9f2ff; border: 1px solid #cfe0ff; }
.user-avatar { width: 44px; height: 44px; border-radius: 50%; overflow: hidden; display:grid; place-items:center; background:#eef2f7; font-weight:700; color:#475569; }
.user-avatar img{ width:100%; height:100%; object-fit:cover; }
.user-lines { min-width: 0; }
.user-name  { font-weight: 600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.user-last  { font-size:.82rem; color:#6b7280; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.user-meta  { text-align:right; }
.badge-unread { background:#2563eb; color:#fff; border-radius:999px; padding:.15rem .45rem; font-size:.72rem; display:none; }

/* ===== Right: conversation ===== */
.conv-pane   { display: grid; grid-template-rows: auto 1fr auto; min-height: 60vh; }
.conv-head   { background:#fff; border-bottom:1px solid #e5e7eb; padding:.75rem 1rem; display:flex; align-items:center; gap:.5rem; }
.conv-title  { font-weight:600; }
.conv-body   {
  padding: .9rem;
  background:#f7f9fc;
  overflow-y: auto;
  max-height: calc(100vh - 260px); /* adjust if your navbar/footer change */
}
@media (max-width: 992px) { .conv-body { max-height: calc(100vh - 220px); } }
.conv-input  { background:#fff; border-top:1px solid #e5e7eb; padding:.6rem; }

/* ===== Bubbles ===== */
.msg { display:flex; gap:.5rem; margin:.4rem 0; align-items:flex-end; }
.msg.me { flex-direction: row-reverse; }
.msg .avatar { width:32px; height:32px; border-radius:50%; overflow:hidden; background:#e5e7eb; display:grid; place-items:center; font-weight:700; color:#374151; }
.msg .bubble { max-width:72%; padding:.55rem .8rem; border-radius:1rem; position:relative; word-wrap:break-word; }
.msg.me   .bubble { background:#cfe9ff; border-bottom-right-radius:.25rem; }
.msg.them .bubble { background:#fff;    border-bottom-left-radius:.25rem; box-shadow:0 1px 2px rgba(0,0,0,.06); }
.meta { font-size:.72rem; color:#6b7280; margin-top:.22rem; }

/* Day divider */
.day-divider { text-align:center; margin:.9rem 0; color:#9ca3af; font-size:.76rem; }
</style>
@endsection

@section('content')
<div class="container-fluid">
  <div class="chat-shell">
    <div class="chat-grid">

      {{-- LEFT: USERS LIST --}}
      <aside class="users-pane">
        <div class="users-head">
          <i class="fas fa-users text-primary"></i>
          <input type="text" id="userSearch" class="form-control form-control-sm search" placeholder="{{ __('adminlte::adminlte.search_users') }}">
          <a href="{{ route('chat.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync"></i></a>
        </div>

        <div class="users-list" id="usersList">
          @php $activeId = request('user_id'); @endphp
          @foreach ($users as $u)
            @php
              $initials = collect(explode(' ', trim($u->name)))->take(2)->map(fn($p)=>mb_substr($p,0,1))->implode('');
              $isActive = (string)$activeId === (string)$u->id;
            @endphp
            <a href="{{ route('chat.index', ['user_id' => $u->id] + request()->except('page')) }}"
               class="user-row {{ $isActive ? 'active':'' }}"
               data-user-id="{{ $u->id }}">
              <div class="user-avatar">
                @if($u->avatar_path)
                  <img src="{{ asset($u->avatar_path) }}" alt="avatar">
                @else
                  {{ mb_strtoupper($initials ?: 'U') }}
                @endif
              </div>
              <div class="user-lines">
                <div class="user-name">{{ $u->name }}</div>
                <div class="user-last">&nbsp;</div>
              </div>
              <div class="user-meta">
                <div class="text-muted" style="font-size:.7rem">&nbsp;</div>
                <div class="badge-unread" data-unread-for="{{ $u->id }}">0</div>
              </div>
            </a>
          @endforeach
        </div>
      </aside>

      {{-- RIGHT: CONVERSATION --}}
      <section class="conv-pane">
        <div class="conv-head">
          @if($activeId)
            @php $peer = $users->firstWhere('id', (int)$activeId); @endphp
            <div class="user-avatar" style="width:36px;height:36px;">
              @if($peer?->avatar_path)
                <img src="{{ asset($peer->avatar_path) }}" alt="avatar">
              @else
                {{ mb_strtoupper(collect(explode(' ', trim($peer?->name ?? 'U')))->take(2)->map(fn($p)=>mb_substr($p,0,1))->implode('')) }}
              @endif
            </div>
            <div class="conv-title">{{ $peer?->name ?? __('adminlte::adminlte.conversation') }}</div>
          @else
            <div class="conv-title">{{ __('adminlte::adminlte.conversation') }}</div>
          @endif
        </div>

        <div id="chatBody"
             class="conv-body"
             data-current-user-id="{{ $currentUser->id ?? Auth::id() }}"
             data-peer-user-id="{{ $activeId ?: '' }}"
             data-channel="chat.user.{{ $currentUser->id ?? Auth::id() }}"
             data-events='@json(["message.sent"])'
             data-pusher-key="{{ config('broadcasting.connections.pusher.key') }}"
             data-pusher-cluster="{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}">
          @php $lastDay = null; @endphp
          @forelse($messages as $m)
            @php
              $isMe   = $m->sender_id == ($currentUser->id ?? Auth::id());
              $who    = $isMe ? 'me' : 'them';
              $day    = optional($m->created_at)->toDateString();
              $sender = $m->sender ?? null;
              $avatar = $isMe ? ($currentUser->avatar_path ?? null) : ($sender->avatar_path ?? null);
            @endphp

            @if($day !== $lastDay)
              <div class="day-divider">{{ \Carbon\Carbon::parse($day)->isoFormat('LL') }}</div>
              @php $lastDay = $day; @endphp
            @endif

            <div class="msg {{ $who }}" data-id="{{ $m->id }}">
              <div class="avatar" title="{{ $sender?->name ?? ($isMe ? $currentUser->name ?? '' : '') }}">
                @if($avatar)
                  <img src="{{ asset($avatar) }}" alt="avatar">
                @else
                  {{ mb_strtoupper(collect(explode(' ', trim($sender?->name ?? ($isMe ? ($currentUser->name ?? '') : 'U'))))->take(2)->map(fn($p)=>mb_substr($p,0,1))->implode('')) }}
                @endif
              </div>
              <div class="bubble {{ $who }}">
                <div class="text">{{ e($m->message) }}</div>
                <div class="meta">
                  <span class="time">{{ optional($m->created_at)->format('H:i') }}</span>
                  <span class="from ml-2 text-muted">{{ $isMe ? __('adminlte::adminlte.you') : ($sender?->name ?? __('adminlte::adminlte.user')) }}</span>
                </div>
              </div>
            </div>
          @empty
            <div class="text-center text-muted my-3">{{ __('adminlte::adminlte.no_messages') }}</div>
          @endforelse
        </div>

        <div class="conv-input">
          <form id="sendForm" action="{{ route('chat.store') }}" method="POST" class="d-flex align-items-center" autocomplete="off">
            @csrf
            @if($activeId)
              <input type="hidden" name="receiver_id" value="{{ (int)$activeId }}">
            @else
              <select name="receiver_id" class="form-control form-control-sm mr-2" required style="max-width:260px">
                <option value="">{{ __('adminlte::adminlte.choose_recipient') }}</option>
                @foreach($users as $u) @continue(($currentUser->id ?? Auth::id()) == $u->id)
                  <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->name }}</option>
                @endforeach
              </select>
            @endif
            <input type="text" name="message" class="form-control mr-2" placeholder="{{ __('adminlte::adminlte.type_message') }}" required maxlength="2000">
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
          </form>
        </div>
      </section>

    </div>
  </div>
</div>
@endsection

@once
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
@endonce

@section('adminlte_js')
<script>
(function () {
  const $ = (sel)=>document.querySelector(sel);
  const chatBody  = $('#chatBody');
  const sendForm  = $('#sendForm');
  const usersList = $('#usersList');
  const userSearch= $('#userSearch');

  const I18N = {
    now: @json(__('adminlte::adminlte.now') ?? 'Now'),
    you: @json(__('adminlte::adminlte.you') ?? 'You'),
    user: @json(__('adminlte::adminlte.user') ?? 'User'),
  };

  /* ---------- SMART SCROLL ---------- */
  function nearBottom(el, threshold = 80) {
    if (!el) return true;
    return (el.scrollHeight - el.scrollTop - el.clientHeight) < threshold;
  }
  function smartScrollToBottom(el, force = false) {
    if (!el) return;
    if (force || nearBottom(el)) el.scrollTop = el.scrollHeight;
  }
  // initial + load + resize (debounced)
  smartScrollToBottom(chatBody, true);
  window.addEventListener('load', ()=>smartScrollToBottom(chatBody, true));
  let rzTimer = null;
  window.addEventListener('resize', ()=>{ clearTimeout(rzTimer); rzTimer=setTimeout(()=>smartScrollToBottom(chatBody), 120); });

  /* ---------- Users search (client-filter) ---------- */
  if (userSearch && usersList) {
    userSearch.addEventListener('input', function(){
      const q = this.value.toLowerCase().trim();
      usersList.querySelectorAll('.user-row').forEach(row=>{
        const name = (row.querySelector('.user-name')?.textContent || '').toLowerCase();
        row.style.display = name.includes(q) ? '' : 'none';
      });
    });
  }

  /* ---------- Optimistic send ---------- */
  function appendMine(text){
    const meName = @json($currentUser->name ?? Auth::user()->name);
    const avatar = @json($currentUser->avatar_path ?? null);
    const wrap = document.createElement('div');
    wrap.className = 'msg me';
    wrap.innerHTML = `
      <div class="avatar" title="${meName ?? ''}">
        ${avatar ? `<img src="{{ asset('') }}${avatar}" alt="avatar">`
                  : (meName ? meName.trim().split(' ').slice(0,2).map(s=>s[0]).join('').toUpperCase() : 'U')}
      </div>
      <div class="bubble me">
        <div class="text"></div>
        <div class="meta">
          <span class="time">${I18N.now}</span>
          <span class="from ml-2 text-muted">${I18N.you}</span>
        </div>
      </div>`;
    wrap.querySelector('.text').textContent = text;
    const stick = nearBottom(chatBody);
    chatBody.appendChild(wrap);
    smartScrollToBottom(chatBody, stick);
  }

  if (sendForm) {
    sendForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const fd  = new FormData(sendForm);
      const msg = (fd.get('message')||'').toString().trim();
      if (!msg) return;
      appendMine(msg);
      try {
        await fetch(sendForm.action, {
          method:'POST', body: fd, credentials:'same-origin',
          headers:{ 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content }
        });
      } catch(err){ console.error(err); }
      sendForm.message.value=''; sendForm.message.focus();
    });
  }

  /* ---------- Pusher: private-chat.user.{id} ---------- */
  (function initPusher(){
    if (!chatBody) return;

    const ds = chatBody.dataset;
    const currentUserId = Number(ds.currentUserId || 0);
    const peerUserId    = ds.peerUserId ? Number(ds.peerUserId) : null;

    const dataKey      = ds.pusherKey || '';
    const dataCluster  = ds.pusherCluster || '';
    const metaKey      = document.querySelector('meta[name="pusher-key"]')?.content || '';
    const metaCluster  = document.querySelector('meta[name="pusher-cluster"]')?.content || '';
    const key          = dataKey || metaKey;
    const cluster      = dataCluster || metaCluster;

    if (!key || !cluster) { console.warn('[chat] Missing Pusher key/cluster.'); return; }

    // CSS.escape polyfill (old browsers)
    if (typeof window.CSS === 'undefined') window.CSS = {};
    if (typeof window.CSS.escape !== 'function') window.CSS.escape = s => String(s).replace(/[^a-zA-Z0-9_\u00A0-\u10FFFF-]/g, '\\$&');

    const ensurePusher = () => new Promise((resolve, reject)=>{
      if (window.Pusher) return resolve();
      const it = setInterval(()=>{ if (window.Pusher){ clearInterval(it); resolve(); } }, 50);
      setTimeout(()=>{ clearInterval(it); if(!window.Pusher) reject(new Error('Pusher not loaded')); }, 6000);
    });

    const existsById = (id)=> !!(id && chatBody.querySelector(`.msg[data-id="${CSS.escape(String(id))}"]`));

    function appendIncoming(payload){
      const m = (payload && typeof payload.message === 'object') ? payload.message : payload;
      if (!m) return;
      if (existsById(m.id)) return;

      const sender   = m.sender || {};
      const isMine   = Number(m.sender_id ?? sender.id ?? 0) === currentUserId;
      if (isMine) return; // mine already optimistically added

      // Only show messages for selected peer (if any); otherwise bump badge
      if (peerUserId) {
        const counterpart = (Number(m.sender_id ?? sender.id ?? 0) === currentUserId)
          ? Number(m.receiver_id ?? m.receiver?.id ?? 0)
          : Number(m.sender_id ?? sender.id ?? 0);
        if (counterpart !== peerUserId) {
          const badge = document.querySelector(`.badge-unread[data-unread-for="${counterpart}"]`);
          if (badge) {
            const curr = Number(badge.textContent) || 0;
            badge.textContent = String(curr + 1);
            badge.style.display = '';
          }
          return;
        }
      }

      const name   = sender.name || I18N.user;
      const avatar = sender.avatar_path || null;
      const time   = m.created_at ? new Date(m.created_at) : new Date();
      const hhmm   = String(time.getHours()).padStart(2,'0') + ':' + String(time.getMinutes()).padStart(2,'0');

      const wrap = document.createElement('div');
      wrap.className = 'msg them';
      if (m.id) wrap.dataset.id = String(m.id);
      wrap.innerHTML = `
        <div class="avatar" title="${name}">
          ${avatar ? `<img src="{{ asset('') }}${avatar}" alt="avatar">`
                   : (name ? name.trim().split(' ').slice(0,2).map(s=>s[0]).join('').toUpperCase() : 'U')}
        </div>
        <div class="bubble them">
          <div class="text"></div>
          <div class="meta">
            <span class="time">${hhmm}</span>
            <span class="from ml-2 text-muted">${name}</span>
          </div>
        </div>`;
      wrap.querySelector('.text').textContent = (m.message ?? '');

      const stick = nearBottom(chatBody);
      chatBody.appendChild(wrap);
      smartScrollToBottom(chatBody, stick);
    }

    ensurePusher().then(()=>{
      // eslint-disable-next-line no-undef
      const p = new Pusher(key, {
        cluster, forceTLS: true,
        authEndpoint: '{{ url('/broadcasting/auth') }}',
        headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content || '' }
      });

      const channelName = 'private-chat.user.' + currentUserId;
      const ch = p.subscribe(channelName);

      ch.bind('message.sent',  e => appendIncoming(e));
      ch.bind('.message.sent', e => appendIncoming(e)); // some libs prefix with dot

      console.info('[chat] Subscribed', channelName, 'listening: message.sent');
    }).catch(err=>console.error('[chat] Pusher init failed', err));
  })();
})();
</script>
@endsection
