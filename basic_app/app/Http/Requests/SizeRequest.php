
<style>
/* ====== Responsive navbar that never hides icons ====== */
.main-header.navbar {
    min-height: 56px; /* stable height */
}

/* Keep each nav-bar list in a single line */
.main-header .navbar-nav { flex-wrap: nowrap; }

/* Right-side icons can scroll horizontally on small screens */
.nav-icons-scroll {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    max-width: 70vw; /* space for brand + left controls */
}
@media (max-width: 768px) {
    .nav-icons-scroll { max-width: 65vw; }
}
@media (max-width: 576px) {
    .nav-icons-scroll { max-width: 60vw; }
}

/* Hide scrollbar but keep scrollability */
.nav-icons-scroll::-webkit-scrollbar { display: none; }
.nav-icons-scroll { scrollbar-width: none; }

/* Make items non-wrapping and tidy */
.nav-icons-scroll > li.nav-item { flex: 0 0 auto; }

/* Tighten icon spacing a bit on small screens */
@media (max-width: 576px) {
    .main-header .nav-link { padding-left: .5rem; padding-right: .5rem; }
}

/* Optional: subtle fade at the scrollable container edge */
.nav-scroll-fade {
    position: relative;
}
.nav-scroll-fade::after {
    content: "";
    position: absolute;
    top: 0; bottom: 0;
    width: 24px;
    pointer-events: none;
    /* LTR fade on the left edge of right navbar */
    @if(!$isAr)
    left: -1px;
    background: linear-gradient(to right, rgba(255,255,255,0), rgba(0,0,0,.04));
    @else
    right: -1px;
    background: linear-gradient(to left, rgba(255,255,255,0), rgba(0,0,0,.04));
    @endif
}

/* Keep the globe label compact on phones */
.lang-label { white-space: nowrap; }
</style>

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links (no wrap) --}}
    <ul class="navbar-nav">
        {{-- Sidebar hamburger --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left slot --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links (scrollable to always show icons) --}}
    <ul class="navbar-nav ml-auto nav-icons-scroll nav-scroll-fade" id="navbarRightIcons">
        {{-- Custom right slot --}}
        @yield('content_top_nav_right')

        {{-- Language switcher --}}
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" data-toggle="dropdown" role="button" aria-expanded="false" title="{{ __('Language') }}">
                <i class="fas fa-globe"></i>
                <span class="d-none d-sm-inline ml-1 lang-label">
                    {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                </span>
                <i class="fas fa-chevron-down ml-1 d-none d-sm-inline"></i>
            </a>

            <div class="dropdown-menu {{ $isAr ? '' : 'dropdown-menu-right' }}">
                <form action="{{ route('change.language') }}" method="GET" class="px-2 py-1">
                    <input type="hidden" name="redirect" value="{{ url()->full() }}">

                    <button type="submit" name="locale" value="en"
                            class="dropdown-item {{ app()->getLocale()==='en' ? 'active' : '' }}">
                        🇬🇧 English
                    </button>

                    <button type="submit" name="locale" value="ar"
                            class="dropdown-item {{ app()->getLocale()==='ar' ? 'active' : '' }}">
                        🇸🇦 العربية
                    </button>
                </form>
            </div>
        </li>

        {{-- Configured right links (notifications, messages, etc.) --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- User menu / logout --}}
        @auth
            @if(config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endauth

        {{-- Right sidebar toggler (Control Sidebar) --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>
</nav>

{{-- Tiny helpers: tooltips + horizontal wheel scroll --}}
@push('js')
<script>
(function () {
    // Enable BS4 tooltips if any 'title' is present
    if (window.jQuery && $.fn.tooltip) {
        $('[data-toggle="tooltip"], .nav-link[title]').tooltip({container: 'body'});
    }

    // Smooth horizontal scroll for right icon rail (mouse wheel & trackpads)
    var rail = document.getElementById('navbarRightIcons');
    if (!rail) return;

    // On small screens, allow vertical wheel to scroll the rail horizontally.
    rail.addEventListener('wheel', function (e) {
        // Only when overflow is actually scrollable
        if (rail.scrollWidth <= rail.clientWidth) return;

        // Shift horizontally instead of vertically
        // RTL browsers can use negative scrollLeft; normalize with scrollBy({left})
        var delta = (e.deltaY || e.deltaX || 0);
        // Flip direction for RTL so "down" still moves content left visually
        var isRTL = {{ $isAr ? 'true' : 'false' }};
        rail.scrollBy({ left: (isRTL ? -delta : delta), behavior: 'smooth' });
        e.preventDefault();
    }, { passive: false });
})();
</script>
@endpush
