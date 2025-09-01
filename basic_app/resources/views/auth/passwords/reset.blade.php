@extends('layouts.app')

{{-- Meta Tags --}}
{{-- Page Title --}}

@section('title', 'Reset Password')

@section('content')
<div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div style="background: #fff; border-radius: 24px; box-shadow: 0 8px 32px 0 rgba(31,38,135,0.12); padding: 48px 32px; max-width: 420px; width: 100%;">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('assets/Images/logo.png') }}" alt="Logo" style="height: 48px; margin-bottom: 12px;">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 8px;">{{ __('adminlte::adminlte.password') }}</h2>
            <p style="color: #888; font-size: 1rem;">{{ __('asminlte::adminlte.enter_new_password') }}/p>
        </div>
        @if (session('status'))
            <div class="alert alert-success" style="margin-bottom: 18px;">
                {{ session('status') }}
            </div>
        @endif
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div style="margin-bottom: 18px;">
                <label for="email" style="display:block; color:#22223B; font-weight:500; margin-bottom:6px;">{{ __('adminlte::adminlte.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    style="width:100%; padding:14px 18px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:16px; background:#f8f8ff;">
                @error('email')
                    <span style="color:#e3342f; font-size:0.95rem;">{{ $message }}</span>
                @enderror
            </div>
            <div style="margin-bottom: 18px;">
                <label for="password" style="display:block; color:#22223B; font-weight:500; margin-bottom:6px;">{{ __('adminlte::adminlte.password') }}</label>
                <input id="password" type="password" name="password" required
                    style="width:100%; padding:14px 18px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:16px; background:#f8f8ff;">
                @error('password')
                    <span style="color:#e3342f; font-size:0.95rem;">{{ $message }}</span>
                @enderror
            </div>
            <div style="margin-bottom: 18px;">
                <label for="password-confirm" style="display:block; color:#22223B; font-weight:500; margin-bottom:6px;">{{ __('adminlte::adminlte.password') }}</</label>
                <input id="password-confirm" type="password" name="password_confirmation" required
                    style="width:100%; padding:14px 18px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:16px; background:#f8f8ff;">
            </div>
            <button type="submit" style="width:100%; background:#6C63FF; color:#fff; font-size:1.1rem; font-weight:600; border:none; border-radius:24px; padding:14px 0; margin-bottom:12px; cursor:pointer; box-shadow:0 4px 16px 0 rgba(108,99,255,0.15); transition:background 0.2s;">
                Reset Password
            </button>
        </form>
        <div style="text-align:center; color:#888; font-size:15px; margin-top:18px;">
            <a href="{{ route('login') }}" style="color:#6C63FF; font-weight:600;">>{{ __('adminlte::adminlte.go_back') }}</</a>
        </div>
    </div>
</div>
@endsection
