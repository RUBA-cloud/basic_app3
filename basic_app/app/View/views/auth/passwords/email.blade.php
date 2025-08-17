@extends('adminlte::auth.auth-page')

@section('title', 'Register')

@section('content')
<div style="min-height: 100vh;   display: flex; flex-direction: row; align-items:stretch;">

    <div style="flex:1.2; background:#ffff; border-radius: 0 24px 24px 0; display:flex; flex-direction:column; justify-content:center; align-items:center; min-width:320px; box-shadow: 0 8px 32px 0 rgba(31,38,135,0.12);">
        <div style="width:50%; max-width:420px; margin:0 auto; padding:48px 32px;">

                <h2 style="font-size:2rem; font-weight:700; color:#22223B; margin-bottom:8px;">{{ __('adminlte::adminlte.forgot_password_instruction') }}</h2>
                <p style="color:#888; font-size:1rem; margin-bottom:0;">{{ __('adminlte::adminlte.send_reset_passwaord_mail') }}</p>
            </div>

            @if(session('message'))
                <div class="alert alert-info" role="alert" style="margin-bottom: 18px;">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div style="margin-bottom: 18px;">
                    <label for="email" style="display:block; color:#22223B; font-weight:500; margin-bottom:6px;">{{ __('adminlte::adminlte.email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        style="width:100%; padding:14px 18px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:16px; background:#f8f8ff;">
                    @error('email')
                        <span style="color:#e3342f; font-size:0.95rem;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" style="width:100%; background:#6C63FF; color:#fff; font-size:1.1rem; font-weight:600; border:none; border-radius:24px; padding:14px 0; margin-bottom:18px; cursor:pointer; box-shadow:0 4px 16px 0 rgba(108,99,255,0.15); transition:background 0.2s;">
                    {{  __('adminlte::adminlte.confirm_password')}}
                </button>
            </form>

        </div>
    </div>
</div>

{{-- Responsive styles --}}
<style>
@media (max-width: 900px) {
    div[style*="display: flex; flex-direction: row;"] {
        flex-direction: column !important;
    }
    div[style*="flex:1.1;"] {
        display: none !important;
    }
    div[style*="flex:1.3;"] {
        border-radius: 24px !important;
    }
}
