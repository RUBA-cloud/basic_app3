{{-- resources/views/chat/chat-message.blade.php --}}
@extends('adminlte::page')

@php
    $isRtl = app()->isLocale('ar');
@endphp

@section('title', __('adminlte::adminlte.chat'))
@section('content')
<div class="container-fluid">
                <div class="chat-shell">
                    <div class="chat-grid">

                        {{-- USERS LIST --}}
                        <aside class="users-pane">
                            <div class="users-head">
                                <i class="fas fa-users" style="color:var(--brand-main)"></i>
                                <input type="text"
                                       class="form-control form-control-sm search"
                                       placeholder="{{ $isRtl ? 'بحث عن مستخدم' : 'Search users' }}">
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync"></i></button>
                            </div>

                            <div class="users-list">
                                {{-- demo users , replace with @foreach --}}
                                <a href="#" class="user-row active">
                                    <div class="user-avatar">RH</div>
                                    <div class="user-lines">
                                        <div class="user-name">{{ $isRtl ? 'روبا حمّاد' : 'Ruba Hammad' }}</div>
                                        <div class="user-last">{{ $isRtl ? 'آخر رسالة...' : 'Last message...' }}</div>
                                    </div>
                                    <div class="user-meta">
                                        <span class="text-muted" style="font-size:.7rem">10:30</span>
                                        <span class="badge-unread" style="display:inline-block">2</span>
                                    </div>
                                </a>
                                <a href="#" class="user-row">
                                    <div class="user-avatar">AD</div>
                                    <div class="user-lines">
                                        <div class="user-name">Admin</div>
                                        <div class="user-last">{{ $isRtl ? 'مرحبا بك 👋' : 'Welcome 👋' }}</div>
                                    </div>
                                    <div class="user-meta">
                                        <span class="text-muted" style="font-size:.7rem">&nbsp;</span>
                                    </div>
                                </a>
                            </div>
                        </aside>

                        {{-- CONVERSATION --}}
                        <section class="conv-pane">
                            <div class="conv-head">
                                <div class="user-avatar" style="width:36px;height:36px;">RH</div>
                                <div class="conv-title">{{ $isRtl ? 'المحادثة' : 'Conversation' }}</div>
                            </div>

                            <div class="conv-body">
                                <div class="day-divider">{{ $isRtl ? 'اليوم' : 'Today' }}</div>

                                <div class="msg them">
                                    <div class="avatar">AD</div>
                                    <div class="bubble them">
                                        <div class="text">{{ $isRtl ? 'أهلاً روبا، هذه نسخة الداشبورد بتصميم المحادثة ✨' : 'Hi Ruba, this is the dashboard with chat styling ✨' }}</div>
                                        <div class="meta"><span class="time">10:20</span> <span class="from ml-2 text-muted">Admin</span></div>
                                    </div>
                                </div>

                                <div class="msg me">
                                    <div class="avatar">RH</div>
                                    <div class="bubble me">
                                        <div class="text">{{ $isRtl ? 'جميل! الألوان مأخوذة من إعدادات الشركة ✅' : 'Nice! Colors are from company settings ✅' }}</div>
                                        <div class="meta"><span class="time">10:21</span> <span class="from ml-2 text-muted">{{ $isRtl ? 'أنتِ' : 'You' }}</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="conv-input">
                                <form class="d-flex align-items-center">
                                    <input type="text" class="form-control" placeholder="{{ $isRtl ? 'اكتب رسالة...' : 'Type a message...' }}">
                                    <button type="button" class="btn btn-primary ml-2"><i class="fas fa-paper-plane"></i></button>
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
  window.addEventListener('resize', ()=>{ clearTimeout(rzTimer); rzTimer=setTimeout(()=>smartScrollToBottom(chatBody), 120); });

  if (userSearch && usersList) {
    userSearch.addEventListener('input', function(){
      const q = this.value.toLowerCase().trim();
      usersList.querySelectorAll('.user-row').forEach(row=>{
        const name = (row.querySelector('.user-name')?.textContent || '').toLowerCase();
        row.style.display = name.includes(q) ? '' : 'none';
      });
    });
  }

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
          method:'POST',
          body: fd,
          credentials:'same-origin',
          headers:{
            'X-Requested-With':'XMLHttpRequest',
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content
          }
        });
      } catch(err){ console.error(err); }
      sendForm.message.value=''; sendForm.message.focus();
    });
  }

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

      const senderId = Number(m.sender_id ?? m.sender?.id ?? 0);

      // my own message → already added
      if (senderId === currentUserId) return;

      // if a specific chat open, show only that user's messages; else bump unread
      if (peerUserId && senderId !== peerUserId) {
        const badge = document.querySelector(`.badge-unread[data-unread-for="${senderId}"]`);
        if (badge) {
          const curr = Number(badge.textContent) || 0;
          badge.textContent = String(curr + 1);
          badge.style.display = '';
        }
        return;
      }

      const sender   = m.sender || {};
      const name     = sender.name || I18N.user;
      const avatar   = sender.avatar_path || null;
      const time     = m.created_at ? new Date(m.created_at) : new Date();
      const hhmm     = String(time.getHours()).padStart(2,'0') + ':' + String(time.getMinutes()).padStart(2,'0');

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
        cluster,
        forceTLS: true,
        authEndpoint: '{{ url('/broadcasting/auth') }}',
        headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content || '' }
      });

      const channelName = 'private-chat.user.' + currentUserId;
      const ch = p.subscribe(channelName);

      ch.bind('message.sent',  e => appendIncoming(e));
      ch.bind('.message.sent', e => appendIncoming(e));
    }).catch(err=>console.error('[chat] Pusher init failed', err));
  })();
})();
</script>
@endsection
