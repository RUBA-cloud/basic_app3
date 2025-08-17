@extends('layouts.app')

{{-- Meta Tags --}}

@section('title', 'Confirm Password')

@section('content')

    <div style="background: #fff; border-radius: 24px; box-shadow: 0 8px 32px 0 rgba(31,38,135,0.12); padding: 48px 32px; max-width: 420px; width: 100%; display: flex; justify-content: center; align-items: center; min-height: 80vh;">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('assets/Images/logo.png') }}" alt="Logo" style="height: 48px; margin-bottom: 12px;">
            <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 8px;">Confirm Password</h2>
            <p style="color: #888; font-size: 1rem;">Please confirm your password before continuing.</p>
        </div>
        @if (session('error'))
            <div class="alert alert-danger" style="margin-bottom: 18px;">
                {{ session('error') }}
            </div>
        @endif
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div style="margin-bottom: 18px;">
                <label for="password" style="display:block; color:#22223B; font-weight:500; margin-bottom:6px;">Password</label>
                <input id="password" type="password" name="password" required autofocus
                    style="width:100%; padding:14px 18px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:16px; background:#f8f8ff;">
                @error('password')
                    <span style="color:#e3342f; font-size:0.95rem;">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" style="width:100%; background:#6C63FF; color:#fff; font-size:1.1rem; font-weight:600; border:none; border-radius:24px; padding:14px 0; margin-bottom:12px; cursor:pointer; box-shadow:0 4px 16px 0 rgba(108,99,255,0.15); transition:background 0.2s;">
                Confirm Password
            </button>
        </form>
        <div style="text-align:center; color:#888; font-size:15px; margin-top:18px;">
            <a href="{{ route('password.request') }}" style="color:#6C63FF; font-weight:600;">Forgot Your Password?</a>
        </div>

</div>
@endsection
