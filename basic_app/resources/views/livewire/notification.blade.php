@php
  $isRtl = app()->isLocale('ar');
  $user  = Auth::user();

  $avatar = $user?->avatar_path
      ? asset($user->avatar_path)
      : asset('images/logo_image.png');

  $notifMenuAlign = $isRtl ? 'dropdown-menu-left' : 'dropdown-menu-right';
  $textAlign      = $isRtl ? 'text-right' : 'text-left';
  $iconSpace      = $isRtl ? 'ml-2' : 'mr-2';
  $dir            = $isRtl ? 'rtl' : 'ltr';

  $unreadChats         = $unreadChats ?? 0;
  $unreadNotifications = $unreadNotifications ?? 0;
@endphp

<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="padding:.6rem 1rem;">

  {{-- LEFT --}}
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
  </ul>

  {{-- CENTER SEARCH --}}
  <form class="form-inline {{ $isRtl ? 'mr-3' : 'ml-3' }}">
    <div class="input-group input-group-sm">
      <input class="form-control form-control-navbar"
             type="search"
             placeholder="{{ __('adminlte::adminlte.search') }}"
             aria-label="Search">
      <div class="input-group-append">
        <button class="btn btn-navbar" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>
  </form>

  {{-- RIGHT --}}
  <ul class="navbar-nav {{ $isRtl ? 'mr-auto' : 'ml-auto' }}">

    {{-- Messages --}}
    <li class="nav-item">
      <a class="nav-link" href="{{ route('chat.index') }}">
        <i class="far fa-comments"></i>

        <span id="chat-badge"
              class="badge badge-danger navbar-badge"
              style="{{ $unreadChats > 0 ? '' : 'display:none' }}">
          {{ $unreadChats }}
        </span>
      </a>
    </li>

    {{-- Notifications --}}
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>

        <span id="notification-badge"
              class="badge badge-warning navbar-badge"
              style="{{ $unreadNotifications > 0 ? '' : 'display:none' }}">
          {{ $unreadNotifications }}
        </span>
      </a>

      <div class="dropdown-menu dropdown-menu-lg {{ $notifMenuAlign }}" dir="{{ $dir }}">
        <span class="dropdown-header {{ $textAlign }}">
          {{ __('adminlte::adminlte.notifications') }}
        </span>

        <div class="dropdown-divider"></div>

        <a href="{{ route('notifications.index') }}" class="dropdown-item {{ $textAlign }}">
          <i class="fas fa-list {{ $iconSpace }}"></i>
          {{ __('adminlte::adminlte.view_all') }}
        </a>

        <div class="dropdown-divider"></div>

        <form action="{{ route('notifications.markAll') }}" method="POST" class="px-3 py-2">
          @csrf
          <button class="btn btn-sm btn-outline-primary btn-block">
            {{ __('adminlte::adminlte.mark_all_as_read') }}
          </button>
        </form>
      </div>
    </li>

    {{-- PROFILE MENU --}}
    <li class="nav-item dropdown user-menu">
      <a href="{{ route('profile.edit') }}"
         class="nav-link dropdown-toggle"
         data-toggle="dropdown"
         aria-expanded="false">
        <img src="{{ $avatar }}" class="user-image img-circle elevation-2" alt="User Image">
        <span class="d-none d-md-inline">{{ $user->name }}</span>
      </a>

      <ul class="dropdown-menu dropdown-menu-lg {{ $notifMenuAlign }}" dir="{{ $dir }}">

        {{-- HEADER --}}
        <li class="user-header bg-dark" style="background:#374151 !important;">
          <img src="{{ $avatar }}" class="img-circle elevation-2" alt="User Image">
          <p class="mt-2 mb-0">{{ $user->name }}</p>
          <small class="text-gray-300">{{ $user->email }}</small>
        </li>

        {{-- BODY --}}
        <li class="user-body">
          <div class="row text-center">
            <div class="col-6">
              <a href="{{ route('profile.edit') }}" class="btn btn-default btn-sm w-100">
                <i class="fas fa-user-cog {{ $iconSpace }}"></i>
                {{ __('adminlte::adminlte.profile') }}
              </a>
            </div>

            <div class="col-6">
              <a href="{{ route('notifications.index') }}" class="btn btn-default btn-sm w-100">
                <i class="far fa-bell {{ $iconSpace }}"></i>
                {{ __('adminlte::adminlte.notifications') }}
              </a>
            </div>
          </div>
        </li>

        {{-- FOOTER --}}
        <li class="user-footer d-flex flex-column">

          {{-- Language Switch --}}
          <div class="btn-group mb-2">
            <form action="{{ route('change.language') }}" method="GET" class="d-inline m-0 p-0">
              <input type="hidden" name="lang" value="en">
              <button type="submit" class="btn btn-outline-secondary btn-sm">EN</button>
            </form>

            <form action="{{ route('change.language') }}" method="GET" class="d-inline m-0 p-0">
              <input type="hidden" name="lang" value="ar">
              <button type="submit" class="btn btn-outline-secondary btn-sm">AR</button>
            </form>
          </div>

          {{-- Theme Toggle --}}
          <button type="button" class="btn btn-outline-secondary btn-sm mb-2" id="theme-toggle-btn">
            <i class="fas fa-adjust {{ $iconSpace }}" id="theme-toggle-icon"></i>
            <span id="theme-toggle-text"></span>
          </button>

          {{-- LOGOUT --}}
          <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm btn-block">
              <i class="fas fa-sign-out-alt {{ $iconSpace }}"></i>
              {{ __('adminlte::adminlte.log_out') }}
            </button>
          </form>

        </li>
      </ul>
    </li>

  </ul>
</nav>

@push('js')
<script>
(function () {
  // =======================
  // Theme toggle
  // =======================
  const btn  = document.getElementById('theme-toggle-btn');
  const text = document.getElementById('theme-toggle-text');
  const icon = document.getElementById('theme-toggle-icon');
  const body = document.body;

  const DARK = 'dark';
  const LIGHT = 'light';

  function applyTheme(theme) {
    if (theme === DARK) {
      body.classList.add('dark-mode');
      text.textContent = @json(__('adminlte::adminlte.light_mode'));
      icon.classList.remove('fa-adjust', 'fa-sun');
      icon.classList.add('fa-moon');
    } else {
      body.classList.remove('dark-mode');
      text.textContent = @json(__('adminlte::adminlte.dark_mode'));
      icon.classList.remove('fa-adjust', 'fa-moon');
      icon.classList.add('fa-sun');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('theme') || (body.classList.contains('dark-mode') ? DARK : LIGHT);
    applyTheme(saved);
  });

  if (btn) {
    btn.addEventListener('click', () => {
      const isDark = body.classList.contains('dark-mode');
      const newTheme = isDark ? LIGHT : DARK;
      applyTheme(newTheme);
      localStorage.setItem('theme', newTheme);
    });
  }

  // =======================
  // Realtime unread counts
  // =======================
  const notifBadge = document.getElementById('notification-badge');
  const chatBadge  = document.getElementById('chat-badge');

  function setBadge(el, count) {
    if (!el) return;
    const n = Math.max(0, parseInt(count || 0, 10) || 0);
    el.textContent = n;
    el.style.display = (n > 0) ? 'inline-block' : 'none';
  }

  function bumpBadge(el) {
    if (!el) return;
    const cur = parseInt(el.textContent || '0', 10) || 0;
    setBadge(el, cur + 1);
  }

  const userId = @json(auth()->id());

  // needs Laravel Echo loaded globally (window.Echo)
  if (!window.Echo || !userId) return;

  // ✅ Laravel Notifications default channel
  Echo.private(`App.Models.User.${userId}`)
    .notification((payload) => {
      // If your notification includes unread_count (recommended), use it
      if (payload && payload.unread_count !== undefined) {
        setBadge(notifBadge, payload.unread_count);
      } else {
        bumpBadge(notifBadge);
      }
    });

  // ✅ Chat channel (change event name if yours differs)
  Echo.private(`chat.user.${userId}`)
    .listen('.MessageSent', (e) => {
      if (e && e.unread_count !== undefined) {
        setBadge(chatBadge, e.unread_count);
      } else {
        bumpBadge(chatBadge);
      }
    });
})();
</script>
@endpush
