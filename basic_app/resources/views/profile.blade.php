{{-- resources/views/profile/edit.blade.php --}}
@extends('adminlte::page')

@php
    $isRtl     = session('locale', app()->getLocale()) === 'ar';
    $dir       = $isRtl ? 'rtl' : 'ltr';
    $avatarUrl = !empty($user?->avatar)
        ? asset('storage/' . $user->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'U')
          . '&size=192&background=2a1f16&color=c9855a&bold=true&format=png';
@endphp

@section('title', __('adminlte::adminlte.profile'))



@section('content_header')
    <h1 class="m-0">{{ __('adminlte::adminlte.profile') }}</h1>
@endsection

@section('content')
<div class="pg-root" dir="{{ $dir }}">

    {{-- ══ FLASH MESSAGE ══ --}}
    @if(session('status'))
        <div class="pg-alert" role="alert">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="8" r="7" stroke="#5dcc8a" stroke-width="1.5"/>
                <path d="M5 8l2 2 4-4" stroke="#5dcc8a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- ══ HERO ══ --}}
    <div class="profile-hero">
        <div class="hero-avatar-wrap" onclick="document.getElementById('avatarFile').click()" title="{{ $isRtl ? 'تغيير الصورة' : 'Change photo' }}">
            <img id="avatarPreview"
                 src="{{ $avatarUrl }}"
                 alt="avatar"
                 class="hero-avatar">
            <div class="hero-cam">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="none">
                    <rect x="1" y="5" width="18" height="13" rx="3" stroke="#fff" stroke-width="1.6"/>
                    <circle cx="10" cy="11.5" r="3.2" stroke="#fff" stroke-width="1.6"/>
                    <path d="M7 5l1.5-2h3L13 5" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <div class="hero-text">
            <div class="hero-name" id="heroName">{{ $user->name ?? '' }}</div>
            <div class="hero-email" id="heroEmail">{{ $user->email ?? '' }}</div>
            <div class="hero-pills">
                <div class="hero-pill" onclick="document.getElementById('avatarFile').click()">
                    <svg width="10" height="10" viewBox="0 0 20 20" fill="none">
                        <path d="M10 3v14M3 10h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    {{ $isRtl ? 'تغيير الصورة' : 'Change photo' }}
                </div>
                @if($user->roles?->first())
                    <div class="hero-pill hero-pill-ghost">{{ $user->roles->first()->name }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ FORM ══ --}}
    <form method="POST"
          action="{{ route('profile.update') }}"
          enctype="multipart/form-data"
          novalidate>
        @csrf
        @method('PUT')

        {{-- Hidden file input --}}
        <input type="file" id="avatarFile" name="avatar" accept="image/*" style="display:none;">

        @error('avatar')
            <div class="pg-error" style="max-width:900px; margin:12px auto 0; padding:0 32px;">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="none">
                    <circle cx="10" cy="10" r="8" stroke="#e74c3c" stroke-width="1.5"/>
                    <path d="M10 6v5M10 13v1" stroke="#e74c3c" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                {{ $message }}
            </div>
        @enderror

        <div class="profile-body">

            {{-- ── SECTION: Personal Info ── --}}
            <div class="pg-section">
                <div class="pg-section__icon">
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="7" r="3.5" stroke="#c9855a" stroke-width="1.5"/>
                        <path d="M3 18c0-3.866 3.134-7 7-7s7 3.134 7 7" stroke="#c9855a" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="pg-section__text">{{ __('adminlte::adminlte.your_information') }}</div>
                <div class="pg-section__line"></div>
            </div>

            {{-- Name --}}
            <div class="pg-field">
                <label class="pg-label" for="nameInput">
                    <span class="pg-label-dot"></span>
                    {{ __('adminlte::adminlte.name') }}
                </label>
                <input type="text"
                       id="nameInput"
                       name="name"
                       dir="{{ $dir }}"
                       class="pg-input @error('name') is-invalid @enderror"
                       style="text-align:{{ $isRtl ? 'right' : 'left' }};"
                       value="{{ old('name', $user->name ?? '') }}"
                       placeholder="{{ $isRtl ? 'اسمك الكامل' : 'Your full name' }}"
                       required autocomplete="off">
                @error('name')
                    <div class="pg-error">
                        <svg width="11" height="11" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M10 6v5M10 13v1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="pg-field">
                <label class="pg-label" for="emailInput">
                    <span class="pg-label-dot"></span>
                    {{ __('adminlte::adminlte.email') }}
                </label>
                <input type="email"
                       id="emailInput"
                       name="email"
                       dir="ltr"
                       class="pg-input @error('email') is-invalid @enderror"
                       style="text-align:{{ $isRtl ? 'right' : 'left' }};"
                       value="{{ old('email', $user->email ?? '') }}"
                       placeholder="you@example.com"
                       required autocomplete="off">
                @error('email')
                    <div class="pg-error">
                        <svg width="11" height="11" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M10 6v5M10 13v1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Language --}}
            <div class="pg-field">
                <label class="pg-label" for="localeSelect">
                    <span class="pg-label-dot"></span>
                    {{ __('adminlte::adminlte.language') }}
                </label>
                <select id="localeSelect"
                        name="locale"
                        class="pg-input @error('locale') is-invalid @enderror"
                        style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                    <option value="en" @selected(old('locale', $user->locale ?? app()->getLocale()) === 'en')>
                        🇬🇧 {{ __('adminlte::adminlte.english') }}
                    </option>
                    <option value="ar" @selected(old('locale', $user->locale ?? app()->getLocale()) === 'ar')>
                        🇸🇦 {{ __('adminlte::adminlte.arabic') }}
                    </option>
                </select>
                @error('locale')
                    <div class="pg-error">{{ $message }}</div>
                @enderror
                <div class="pg-hint">{{ __('adminlte::adminlte.direction_notice') }}</div>
            </div>

            {{-- Avatar upload --}}
            <div class="pg-field">
                <label class="pg-label">
                    <span class="pg-label-dot"></span>
                    {{ __('adminlte::adminlte.avatar') }}
                </label>
                <div>
                    <button type="button"
                            class="upload-btn"
                            onclick="document.getElementById('avatarFile').click()">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="none">
                            <path d="M10 14V4M6 8l4-4 4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 16h14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                        </svg>
                        {{ __('adminlte::adminlte.choose_file') }}
                    </button>
                </div>
                <div class="pg-hint" id="avatarFileName">{{ __('adminlte::adminlte.png_jpg') }}</div>
            </div>

            {{-- ── SECTION: Security ── --}}
            <div class="pg-section">
                <div class="pg-section__icon">
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="none">
                        <path d="M10 2l7 3v5c0 4.4-3 8.1-7 9-4-.9-7-4.6-7-9V5l7-3z" stroke="#c9855a" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M7.5 10l2 2 3-3" stroke="#c9855a" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="pg-section__text">{{ __('adminlte::adminlte.change_password_optional') }}</div>
                <div class="pg-section__line"></div>
            </div>

            {{-- Current password --}}
            <div class="pg-field">
                <label class="pg-label" for="currentPassword">
                    <span class="pg-label-dot"></span>
                    {{ __('adminlte::adminlte.current_password') }}
                </label>
                <input type="password"
                       id="currentPassword"
                       name="current_password"
                       class="pg-input @error('current_password') is-invalid @enderror"
                       placeholder="••••••••"
                       autocomplete="new-password">
                @error('current_password')
                    <div class="pg-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- New password --}}
            <div class="pg-field">
                <label class="pg-label" for="newPassword">
                    <span class="pg-label-dot"></span>
                    {{ __('adminlte::adminlte.new_password') }}
                </label>
                <input type="password"
                       id="newPassword"
                       name="password"
                       class="pg-input @error('password') is-invalid @enderror"
                       placeholder="{{ $isRtl ? '٨ أحرف على الأقل' : 'Min. 8 characters' }}"
                       autocomplete="new-password">
                @error('password')
                    <div class="pg-error">{{ $message }}</div>
                @enderror
                <div class="pwd-bar-wrap">
                    <div class="pwd-bar-fill" id="pwdBar"></div>
                </div>
                <div class="pwd-hint" id="pwdLabel"></div>
            </div>

            {{-- Confirm password --}}
            <div class="pg-field">
                <label class="pg-label" for="confirmPassword">
                    <span class="pg-label-dot"></span>
                    {{ __('adminlte::adminlte.confirm_new_password') }}
                </label>
                <input type="password"
                       id="confirmPassword"
                       name="password_confirmation"
                       class="pg-input"
                       placeholder="{{ $isRtl ? 'أعد كتابة كلمة المرور' : 'Repeat password' }}"
                       autocomplete="new-password">
                <div class="pwd-hint" id="matchLabel"></div>
            </div>

            {{-- empty grid cell for alignment --}}
            <div></div>

            {{-- ── ACTIONS ── --}}
            <div class="pg-actions">
                <button type="submit" class="btn-pg-save">
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="none">
                        <path d="M15 17H5a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v7a2 2 0 01-2 2z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        <path d="M13 17v-5H7v5M7 3v4h6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ __('adminlte::adminlte.save_changes') }}
                </button>

                <a href="{{ url()->previous() }}" class="btn-pg-back">
                    @if($isRtl)
                        <svg width="13" height="13" viewBox="0 0 20 20" fill="none">
                            <path d="M8 5l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @else
                        <svg width="13" height="13" viewBox="0 0 20 20" fill="none">
                            <path d="M12 5L7 10l5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @endif
                    {{ __('adminlte::adminlte.back') }}
                </a>
            </div>

        </div>{{-- /.profile-body --}}
    </form>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Avatar preview ── */
    var fileInput  = document.getElementById('avatarFile');
    var preview    = document.getElementById('avatarPreview');
    var fileNameEl = document.getElementById('avatarFileName');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                if (fileNameEl) fileNameEl.textContent = this.files[0].name;
                var reader = new FileReader();
                reader.onload = function (e) {
                    if (preview) preview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    /* ── Live hero name / email sync ── */
    var nameInput  = document.getElementById('nameInput');
    var emailInput = document.getElementById('emailInput');
    var heroName   = document.getElementById('heroName');
    var heroEmail  = document.getElementById('heroEmail');

    if (nameInput  && heroName)  nameInput.addEventListener('input',  function () { heroName.textContent  = this.value || ''; });
    if (emailInput && heroEmail) emailInput.addEventListener('input', function () { heroEmail.textContent = this.value || ''; });

    /* ── Language switcher → live dir flip ── */
    var localeSelect = document.getElementById('localeSelect');
    if (localeSelect) {
        localeSelect.addEventListener('change', function () {
            var isAr   = this.value === 'ar';
            var newDir = isAr ? 'rtl' : 'ltr';
            var align  = isAr ? 'right' : 'left';

            document.documentElement.setAttribute('dir',  newDir);
            document.documentElement.setAttribute('lang', this.value);

            // flip root
            var root = document.querySelector('.pg-root');
            if (root) root.setAttribute('dir', newDir);

            // flip hero
            var hero = document.querySelector('.profile-hero');
            if (hero) hero.style.flexDirection = isAr ? 'row-reverse' : 'row';

            // flip actions
            var actions = document.querySelector('.pg-actions');
            if (actions) actions.style.flexDirection = isAr ? 'row-reverse' : 'row';

            // text align on inputs
            document.querySelectorAll('.pg-input').forEach(function (el) {
                el.style.textAlign = align;
            });
        });
    }

    /* ── Password strength ── */
    var pwdInput     = document.getElementById('newPassword');
    var pwdBar       = document.getElementById('pwdBar');
    var pwdLabel     = document.getElementById('pwdLabel');
    var confirmInput = document.getElementById('confirmPassword');
    var matchLabel   = document.getElementById('matchLabel');

    var levels = [
        { pct: '22%',  color: '#e74c3c', en: 'Weak',   ar: 'ضعيفة'  },
        { pct: '48%',  color: '#e67e22', en: 'Fair',   ar: 'مقبولة' },
        { pct: '74%',  color: '#d4ac0d', en: 'Good',   ar: 'جيدة'   },
        { pct: '100%', color: '#27ae60', en: 'Strong', ar: 'قوية'   },
    ];

    function isArabic() {
        return document.documentElement.getAttribute('dir') === 'rtl';
    }

    if (pwdInput) {
        pwdInput.addEventListener('input', function () {
            var val = this.value;
            if (!val) {
                pwdBar.style.width  = '0';
                pwdLabel.textContent = '';
                return;
            }
            var score = 0;
            if (val.length >= 8)          score++;
            if (/[A-Z]/.test(val))        score++;
            if (/[0-9]/.test(val))        score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            var lvl = levels[Math.max(0, score - 1)];
            pwdBar.style.width      = lvl.pct;
            pwdBar.style.background = lvl.color;
            pwdLabel.textContent    = isArabic() ? lvl.ar : lvl.en;
            pwdLabel.style.color    = lvl.color;
            checkMatch();
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', checkMatch);
    }

    function checkMatch() {
        if (!confirmInput || !confirmInput.value) {
            if (matchLabel) matchLabel.textContent = '';
            return;
        }
        var matches = confirmInput.value === (pwdInput ? pwdInput.value : '');
        matchLabel.textContent = matches
            ? (isArabic() ? 'كلمتا المرور متطابقتان' : 'Passwords match')
            : (isArabic() ? 'كلمتا المرور غير متطابقتين' : 'Does not match');
        matchLabel.style.color = matches ? '#27ae60' : '#e74c3c';
    }

});
</script>
@endpush