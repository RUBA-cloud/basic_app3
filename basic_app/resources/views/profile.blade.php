{{-- resources/views/profile/edit.blade.php --}}
@extends('adminlte::page')

@php
    $isRtl     = session('locale', app()->getLocale()) === 'ar';
    $dir       = $isRtl ? 'rtl' : 'ltr';
    $avatarUrl = !empty($user?->avatar)
        ? asset('storage/' . $user->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'U')
          . '&size=128&background=random&color=fff';
@endphp

@section('title', __('adminlte::adminlte.profile'))

@push('css')
<style>
    .profile-wrap *           { box-sizing: border-box; }
    .profile-wrap label       { display: block; }
    .profile-wrap .form-group { margin-bottom: 1.2rem; }

    [dir="rtl"] .profile-wrap .custom-file-label::after {
        right: auto; left: 0;
        border-radius: .25rem 0 0 .25rem;
    }
    [dir="ltr"] .profile-wrap .custom-file-label::after {
        left: auto; right: 0;
        border-radius: 0 .25rem .25rem 0;
    }

    /* ── Profile hero banner ── */
    .profile-hero {
        background: linear-gradient(135deg, var(--brand-main, #c0392b), var(--brand-sub, #922b21));
        padding: 36px 32px;
        display: flex;
        align-items: center;
        gap: 24px;
        border-radius: 0;
    }
    [dir="rtl"] .profile-hero { flex-direction: row-reverse; }

    .profile-hero__avatar {
        width: 90px; height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,.8);
        box-shadow: 0 4px 16px rgba(0,0,0,.25);
        cursor: pointer;
        flex-shrink: 0;
        transition: opacity .2s;
    }
    .profile-hero__avatar:hover { opacity: .85; }

    .profile-hero__info { flex: 1; }
    .profile-hero__name  { color:#fff; font-size:20px; font-weight:700; margin:0 0 4px; }
    .profile-hero__email { color:rgba(255,255,255,.75); font-size:13px; margin:0; }
    .profile-hero__badge {
        display: inline-flex; align-items: center; gap: 5px;
        margin-top: 8px;
        background: rgba(255,255,255,.15);
        color: #fff;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        cursor: pointer;
        transition: background .2s;
    }
    .profile-hero__badge:hover { background: rgba(255,255,255,.28); }

    /* ── Section divider ── */
    .profile-section {
        padding: 24px 28px 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #aaa;
        border-bottom: 1px solid #f2f2f2;
        margin-bottom: 20px;
    }

    /* ── Fields ── */
    .profile-wrap .form-control {
        border: 1.5px solid #e8e8e8 !important;
        border-radius: 8px !important;
        background: #fafafa !important;
        transition: border-color .2s, box-shadow .2s;
    }
    .profile-wrap .form-control:focus {
        border-color: var(--brand-main, #c0392b) !important;
        box-shadow: 0 0 0 3px rgba(192,57,43,.1) !important;
        background: #fff !important;
    }
    .profile-wrap .form-control.is-invalid {
        border-color: #e74c3c !important;
    }
    .profile-wrap label {
        font-size: 12px;
        font-weight: 600;
        color: #666;
        margin-bottom: 5px;
    }

    /* ── Two-column grid for fields ── */
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 28px;
        padding: 0 28px;
    }
    .profile-grid-full {
        grid-column: 1 / -1;
    }
    @media (max-width: 640px) {
        .profile-grid { grid-template-columns: 1fr; }
        .profile-hero { flex-direction: column; text-align: center; }
        [dir="rtl"] .profile-hero { flex-direction: column; }
    }

    /* ── Password strength ── */
    .pwd-bar-wrap {
        height: 4px; border-radius: 2px;
        background: #eee; margin-top: 6px; overflow: hidden;
    }
    .pwd-bar-fill {
        height: 100%; width: 0; border-radius: 2px;
        transition: width .3s, background .3s;
    }

    /* ── Footer ── */
    .profile-footer {
        display: flex;
        gap: .6rem;
        padding: 18px 28px;
        border-top: 1px solid #f0f0f0;
        background: #fafafa;
    }
    [dir="rtl"] .profile-footer { flex-direction: row-reverse; }

    .btn-save {
        padding: 9px 28px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        background: var(--brand-main, #c0392b);
        color: #fff !important;
        border: none;
        transition: opacity .2s;
    }
    .btn-save:hover { opacity: .88; }

    .btn-back {
        padding: 9px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        background: #efefef;
        color: #555 !important;
        border: none;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background .2s;
    }
    .btn-back:hover { background: #e2e2e2; }
</style>
@endpush

@section('content_header')
    <h1 class="m-0">{{ __('adminlte::adminlte.profile') }}</h1>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row no-gutters">
        <div class="col-12">

            {{-- Flash --}}
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                    <i class="fas fa-check-circle {{ $isRtl ? 'ml-2' : 'mr-2' }}"></i>
                    {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            <div class="card mb-0 profile-wrap border-0 shadow-sm" dir="{{ $dir }}">

                {{-- ══ HERO ══ --}}
                <div class="profile-hero">
                    <img id="avatarPreview"
                         src="{{ $avatarUrl }}"
                         alt="avatar"
                         class="profile-hero__avatar"
                         onclick="document.getElementById('avatarFile').click()">

                    <div class="profile-hero__info">
                        <p class="profile-hero__name" id="heroName">
                            {{ $user->name ?? '' }}
                        </p>
                        <p class="profile-hero__email" id="heroEmail">
                            {{ $user->email ?? '' }}
                        </p>
                        <span class="profile-hero__badge"
                              onclick="document.getElementById('avatarFile').click()">
                            <i class="fas fa-camera"></i>
                            {{ $isRtl ? 'تغيير الصورة' : 'Change photo' }}
                        </span>
                    </div>
                </div>

                <form method="POST"
                      action="{{ route('profile.update') }}"
                      enctype="multipart/form-data"
                      novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Hidden file input --}}
                    <input type="file" id="avatarFile" name="avatar"
                           accept="image/*" style="display:none;">
                    @error('avatar')
                        <div class="text-danger px-4 pt-2" style="font-size:12px;
                             text-align:{{ $isRtl ? 'right' : 'left' }};">
                            <i class="fas fa-exclamation-circle {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                            {{ $message }}
                        </div>
                    @enderror

                    {{-- ══ SECTION: Personal Info ══ --}}
                    <div class="profile-section">
                        <i class="fas fa-id-card {{ $isRtl ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('adminlte::adminlte.your_information') }}
                    </div>

                    <div class="profile-grid">

                        {{-- Name --}}
                        <div class="form-group">
                            <label style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                <i class="fas fa-user {{ $isRtl ? 'ml-1' : 'mr-1' }}"
                                   style="color:var(--brand-main,#c0392b);"></i>
                                {{ __('adminlte::adminlte.name') }}
                            </label>
                            <input type="text"
                                   name="name"
                                   id="nameInput"
                                   dir="{{ $dir }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   style="text-align:{{ $isRtl ? 'right' : 'left' }};"
                                   value="{{ old('name', $user->name ?? '') }}"
                                   required autocomplete="off">
                            @error('name')
                                <div class="invalid-feedback d-block"
                                     style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="form-group">
                            <label style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                <i class="fas fa-envelope {{ $isRtl ? 'ml-1' : 'mr-1' }}"
                                   style="color:var(--brand-main,#c0392b);"></i>
                                {{ __('adminlte::adminlte.email') }}
                            </label>
                            <input type="email"
                                   name="email"
                                   id="emailInput"
                                   dir="ltr"
                                   class="form-control @error('email') is-invalid @enderror"
                                   style="text-align:{{ $isRtl ? 'right' : 'left' }};"
                                   value="{{ old('email', $user->email ?? '') }}"
                                   required autocomplete="off">
                            @error('email')
                                <div class="invalid-feedback d-block"
                                     style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Language --}}
                        <div class="form-group">
                            <label style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                <i class="fas fa-language {{ $isRtl ? 'ml-1' : 'mr-1' }}"
                                   style="color:var(--brand-main,#c0392b);"></i>
                                {{ __('adminlte::adminlte.language') }}
                            </label>
                            <select name="locale"
                                    id="localeSelect"
                                    class="form-control @error('locale') is-invalid @enderror"
                                    style="text-align:{{ $isRtl ? 'right' : 'left' }};
                                           text-align-last:{{ $isRtl ? 'right' : 'left' }};">
                                <option value="en"
                                    @selected(old('locale', $user->locale ?? app()->getLocale()) === 'en')>
                                    🇬🇧 {{ __('adminlte::adminlte.english') }}
                                </option>
                                <option value="ar"
                                    @selected(old('locale', $user->locale ?? app()->getLocale()) === 'ar')>
                                    🇸🇦 {{ __('adminlte::adminlte.arabic') }}
                                </option>
                            </select>
                            @error('locale')
                                <div class="invalid-feedback d-block"
                                     style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted d-block mt-1"
                                   style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                {{ __('adminlte::adminlte.direction_notice') }}
                            </small>
                        </div>

                        {{-- Photo upload button --}}
                        <div class="form-group">
                            <label style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                <i class="fas fa-image {{ $isRtl ? 'ml-1' : 'mr-1' }}"
                                   style="color:var(--brand-main,#c0392b);"></i>
                                {{ __('adminlte::adminlte.avatar') }}
                            </label>
                            <div class="d-flex align-items-center"
                                 style="{{ $isRtl ? 'flex-direction:row-reverse;' : '' }} gap:10px;">
                                <button type="button"
                                        onclick="document.getElementById('avatarFile').click()"
                                        class="btn btn-outline-secondary btn-sm"
                                        style="border-radius:7px; white-space:nowrap;">
                                    <i class="fas fa-upload {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                                    {{ __('adminlte::adminlte.choose_file') }}
                                </button>
                                <small id="avatarFileName" class="text-muted">
                                    {{ __('adminlte::adminlte.png_jpg') }}
                                </small>
                            </div>
                        </div>

                    </div>{{-- /.profile-grid --}}

                    {{-- ══ SECTION: Security ══ --}}
                    <div class="profile-section">
                        <i class="fas fa-shield-alt {{ $isRtl ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('adminlte::adminlte.change_password_optional') }}
                    </div>

                    <div class="profile-grid">

                        {{-- Current password --}}
                        <div class="form-group">
                            <label style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                <i class="fas fa-key {{ $isRtl ? 'ml-1' : 'mr-1' }}"
                                   style="color:var(--brand-main,#c0392b);"></i>
                                {{ __('adminlte::adminlte.current_password') }}
                            </label>
                            <input type="password"
                                   name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('current_password')
                                <div class="invalid-feedback d-block"
                                     style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- New password --}}
                        <div class="form-group">
                            <label style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                <i class="fas fa-lock-open {{ $isRtl ? 'ml-1' : 'mr-1' }}"
                                   style="color:var(--brand-main,#c0392b);"></i>
                                {{ __('adminlte::adminlte.new_password') }}
                            </label>
                            <input type="password"
                                   name="password"
                                   id="newPassword"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback d-block"
                                     style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="pwd-bar-wrap">
                                <div class="pwd-bar-fill" id="pwdBar"></div>
                            </div>
                            <small id="pwdLabel" class="text-muted" style="font-size:11px;"></small>
                        </div>

                        {{-- Confirm password --}}
                        <div class="form-group">
                            <label style="text-align:{{ $isRtl ? 'right' : 'left' }};">
                                <i class="fas fa-check-double {{ $isRtl ? 'ml-1' : 'mr-1' }}"
                                   style="color:var(--brand-main,#c0392b);"></i>
                                {{ __('adminlte::adminlte.confirm_new_password') }}
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   autocomplete="new-password">
                        </div>

                    </div>{{-- /.profile-grid --}}

                    {{-- ══ FOOTER ══ --}}
                    <div class="profile-footer">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save {{ $isRtl ? 'ml-2' : 'mr-2' }}"></i>
                            {{ __('adminlte::adminlte.save_changes') }}
                        </button>
                        <a href="{{ url()->previous() }}" class="btn-back">
                            <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}"></i>
                            {{ __('adminlte::adminlte.back') }}
                        </a>
                    </div>

                </form>
            </div>{{-- /.card --}}
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Avatar pick ── */
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

    /* ── Live hero name / email ── */
    var nameInput  = document.getElementById('nameInput');
    var emailInput = document.getElementById('emailInput');
    var heroName   = document.getElementById('heroName');
    var heroEmail  = document.getElementById('heroEmail');

    if (nameInput  && heroName)  nameInput.addEventListener('input',  function () { heroName.textContent  = this.value; });
    if (emailInput && heroEmail) emailInput.addEventListener('input', function () { heroEmail.textContent = this.value; });

    /* ── Language switcher → live dir flip ── */
    var localeSelect = document.getElementById('localeSelect');
    if (localeSelect) {
        localeSelect.addEventListener('change', function () {
            var isAr   = this.value === 'ar';
            var newDir = isAr ? 'rtl' : 'ltr';

            document.documentElement.setAttribute('dir',  newDir);
            document.documentElement.setAttribute('lang', this.value);

            document.querySelectorAll('[dir]').forEach(function (el) {
                el.setAttribute('dir', newDir);
            });

            var align = isAr ? 'right' : 'left';
            document.querySelectorAll('.profile-wrap input, .profile-wrap label, .profile-wrap small, .profile-wrap select')
                .forEach(function (el) { el.style.textAlign = align; });

            // flip hero row
            var hero = document.querySelector('.profile-hero');
            if (hero) hero.style.flexDirection = isAr ? 'row-reverse' : 'row';

            // flip footer
            var footer = document.querySelector('.profile-footer');
            if (footer) footer.style.flexDirection = isAr ? 'row-reverse' : 'row';
        });
    }

    /* ── Password strength ── */
    var pwdInput = document.getElementById('newPassword');
    var pwdBar   = document.getElementById('pwdBar');
    var pwdLabel = document.getElementById('pwdLabel');
    var levels   = [
        { pct:'20%',  color:'#e74c3c', en:'Weak',   ar:'ضعيفة'  },
        { pct:'45%',  color:'#e67e22', en:'Fair',   ar:'مقبولة' },
        { pct:'72%',  color:'#f1c40f', en:'Good',   ar:'جيدة'   },
        { pct:'100%', color:'#27ae60', en:'Strong', ar:'قوية'   },
    ];

    if (pwdInput) {
        pwdInput.addEventListener('input', function () {
            var val  = this.value;
            var isAr = document.documentElement.getAttribute('dir') === 'rtl';
            if (!val) { pwdBar.style.width = '0'; pwdLabel.textContent = ''; return; }
            var score = 0;
            if (val.length >= 8)          score++;
            if (/[A-Z]/.test(val))        score++;
            if (/[0-9]/.test(val))        score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            var lvl = levels[Math.max(0, score - 1)];
            pwdBar.style.width      = lvl.pct;
            pwdBar.style.background = lvl.color;
            pwdLabel.textContent    = isAr ? lvl.ar : lvl.en;
            pwdLabel.style.color    = lvl.color;
        });
    }

});
</script>
@endpush