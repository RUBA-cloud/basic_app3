{{-- resources/views/admin/chat/index.blade.php (example path) --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.chat'))

@php
    $isRtl      = app()->isLocale('ar');
    /** @var \App\Models\User $currentUser */
    $currentUser = $currentUser ?? Auth::user();
@endphp

@section('content')
<div class="container-fluid py-4 chat-page {{ $isRtl ? 'rtl-mode' : 'ltr-mode' }}">
  <div class="chat-shell shadow-sm">
    <div class="chat-grid">

      {{-- LEFT / RIGHT: USERS LIST (position depends on RTL/LTR) --}}
      <aside class="users-pane">
        <div class="users-head">
          <div class="users-head-title d-flex align-items-center gap-2">
            <i class="fas fa-users text-primary"></i>
            <span class="fw-bold">{{ __('adminlte::adminlte.users') }}</span>
          </div>
          <div class="users-head-actions d-flex gap-2 flex-grow-1">
            <input type="text" id="userSearch" class="form-control form-control-sm search"
                   placeholder="{{ __('adminlte::adminlte.search_users') }}">
            <a href="{{ route('chat.index') }}" class="btn btn-sm btn-light border">
              <i class="fas fa-sync"></i>
            </a>
          </div>
        </div>

        <div class="users-list" id="usersList">
          @php
              $activeId = request('user_id');
          @endphp
          @foreach ($users as $u)
            @php
              $initials = collect(explode(' ', trim($u->name)))
                  ->take(2)
                  ->map(fn($p)=>mb_substr($p,0,1))
                  ->implode('');
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
                <div class="user-last text-muted small">&nbsp;</div>
              </div>
              <div class="user-meta">
                <span class="badge-unread" data-unread-for="{{ $u->id }}">0</span>
              </div>
            </a>
          @endforeach
        </div>
      </aside>

      {{-- CONVERSATION --}}
      <section class="conv-pane">
        <div class="conv-head">
          @if($activeId)
            @php $peer = $users->firstWhere('id', (int)$activeId); @endphp
            <div class="conv-head-main d-flex align-items-center gap-2">
              <div class="user-avatar peer-avatar">
                @if($peer?->avatar_path)
                  <img src="{{ asset($peer->avatar_path) }}" alt="avatar">
                @else
                  {{ mb_strtoupper(
                      collect(explode(' ', trim($peer?->name ?? 'U')))
                          ->take(2)
                          ->map(fn($p)=>mb_substr($p,0,1))
                          ->implode('')
                  ) }}
                @endif
              </div>
              <div class="conv-title">{{ $peer?->name ?? __('adminlte::adminlte.conversation') }}</div>
            </div>
          @else
            <div class="conv-head-main">
              <div class="conv-title">{{ __('adminlte::adminlte.conversation') }}</div>
            </div>
          @endif
        </div>

        <div id="chatBody"
             class="conv-body"
             data-current-user-id="{{ $currentUser->id }}"
             data-peer-user-id="{{ $activeId ?: '' }}"
             data-channel="chat.user.{{ $currentUser->id }}"
             data-events='["message.sent"]'>
          @php $lastDay = null; @endphp
          @forelse($messages as $m)
            @php
              $isMe   = $m->sender_id == $currentUser->id;
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
                  {{ mb_strtoupper(
                      collect(explode(' ', trim($sender?->name ?? ($isMe ? ($currentUser->name ?? '') : 'U'))))
                          ->take(2)
                          ->map(fn($p)=>mb_substr($p,0,1))
                          ->implode('')
                  ) }}
                @endif
              </div>
              <div class="bubble {{ $who }}">
                <div class="text">{{ e($m->message) }}</div>
                <div class="meta">
                  <span class="time">{{ optional($m->created_at)->format('H:i') }}</span>
                  <span class="from ms-2 text-muted small">
                    {{ $isMe ? __('adminlte::adminlte.you') : ($sender?->name ?? __('adminlte::adminlte.user')) }}
                  </span>
                </div>
              </div>
            </div>
          @empty
            <div class="text-center text-muted my-3">{{ __('adminlte::adminlte.no_messages') }}</div>
          @endforelse
        </div>

        <div class="conv-input border-top">
          <form id="sendForm"
                action="{{ route('chat.store') }}"
                method="POST"
                class="conv-input-form d-flex align-items-center gap-2"
                autocomplete="off">
            @csrf
            @if($activeId)
              <input type="hidden" name="receiver_id" value="{{ (int)$activeId }}">
            @else
              <select name="receiver_id" class="form-select form-select-sm conv-recipient" required>
                <option value="">{{ __('adminlte::adminlte.choose_recipient') }}</option>
                @foreach($users as $u)
                  @continue($currentUser->id == $u->id)
                  <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->name }}</option>
                @endforeach
              </select>
            @endif
            <input type="text" name="message" class="form-control flex-fill conv-message"
                   placeholder="{{ __('adminlte::adminlte.type_message') }}" required maxlength="2000">
            <button type="submit" class="btn btn-primary px-3 conv-send-btn">
              <i class="fas fa-paper-plane"></i>
            </button>
          </form>
        </div>
      </section>

    </div>
  </div>
</div>
@endsection



@section('adminlte_js')
@parent
<script>
document.addEventListener('DOMContentLoaded', function () {
  const $ = (sel) => document.querySelector(sel);
  const chatBody   = $('#chatBody');
  const sendForm   = $('#sendForm');
  const usersList  = $('#usersList');
  const userSearch = $('#userSearch');

  const I18N = {
    now:  @json(__('adminlte::adminlte.now') ?? 'Now'),
    you:  @json(__('adminlte::adminlte.you') ?? 'You'),
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
  smartScrollToBottom(chatBody, true);
  window.addEventListener('load', ()=>smartScrollToBottom(chatBody, true));
  let rzTimer = null;
  window.addEventListener('resize', ()=>{
    clearTimeout(rzTimer);
    rzTimer = setTimeout(()=>smartScrollToBottom(chatBody), 120);
  });

  /* ---------- Users search ---------- */
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
      <div class="avatar" title="${meName || ''}">
        ${avatar
          ? `<img src="{{ asset('') }}${avatar}" alt="avatar">`
          : (meName ? meName.trim().split(' ').slice(0,2).map(s=>s[0]).join('').toUpperCase() : 'U')}
      </div>
      <div class="bubble me">
        <div class="text"></div>
        <div class="meta">
          <span class="time">${I18N.now}</span>
          <span class="from ms-2 text-muted small">${I18N.you}</span>
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
          method: 'POST',
          body: fd,
          credentials: 'same-origin',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content || ''
          }
        });
      } catch(err){
        console.error('[chat] send failed', err);
      }

      sendForm.message.value = '';
      sendForm.message.focus();
    });
  }

  /* ---------- Incoming messages (message.sent) ---------- */
  function appendIncoming(payload){
    let m = payload;

    if (m && typeof m === 'object') {
      if (m.message) m = m.message;
      else if (m.data && m.data.message) m = m.data.message;
    }
    if (!m || !chatBody) return;

    // Avoid duplicates by id
    if (m.id && chatBody.querySelector(`.msg[data-id="${m.id}"]`)) return;

    const currentUserId = Number(chatBody.dataset.currentUserId || 0);
    const peerUserId    = chatBody.dataset.peerUserId
      ? Number(chatBody.dataset.peerUserId)
      : null;

    const sender   = m.sender || {};
    const senderId = Number(m.sender_id ?? sender.id ?? 0);
    const recvId   = Number(m.receiver_id ?? m.receiver?.id ?? 0);
    const isMine   = senderId === currentUserId;
    if (isMine) return;

    const counterpart = (senderId === currentUserId) ? recvId : senderId;

    // If message is from/to another user, bump unread badge
    if (peerUserId && counterpart !== peerUserId) {
      const badge = document.querySelector(`.badge-unread[data-unread-for="${senderId}"]`);
      if (badge) {
        const curr = Number(badge.textContent) || 0;
        badge.textContent = String(curr + 1);
        badge.style.display = '';
      }
      return;
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
        ${avatar
          ? `<img src="{{ asset('') }}${avatar}" alt="avatar">`
          : (name ? name.trim().split(' ').slice(0,2).map(s=>s[0]).join('').toUpperCase() : 'U')}
      </div>
      <div class="bubble them">
        <div class="text"></div>
        <div class="meta">
          <span class="time">${hhmm}</span>
          <span class="from ms-2 text-muted small">${name}</span>
        </div>
      </div>`;
    wrap.querySelector('.text').textContent = (m.message ?? '');

    const stick = nearBottom(chatBody);
    chatBody.appendChild(wrap);
    smartScrollToBottom(chatBody, stick);
  }

  /* ---------- Real-time subscription via Echo ---------- */
  if (chatBody) {
    const currentUserId = Number(chatBody.dataset.currentUserId || 0);
    const channelName   = chatBody.dataset.channel || ('chat.user.' + currentUserId);

    let events;
    try {
      events = JSON.parse(chatBody.dataset.events || '["message.sent"]');
    } catch (_) {
      console.warn('[chat] failed to parse data-events, fallback to ["message.sent"]');
      events = ['message.sent'];
    }
    if (!Array.isArray(events) || !events.length) {
      events = ['message.sent'];
    }

    const eventName = events[0] || 'message.sent';

    if (typeof Echo === 'undefined') {
      console.warn('[chat] Echo is not defined. Check your bootstrap.js config.');
    } else {
      Echo.private(channelName)
          .listen('.' + eventName, (payload) => {
            appendIncoming(payload);
          });

      console.info('[chat] listening on', channelName, 'event', '.' + eventName);
    }
  }
});
</script>
@endsection
