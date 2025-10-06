{{-- resources/views/auth/verify.blade.php --}}
@extends('adminlte::page')

@section('title', __('Verify Email'))

@section('content')
<div style="min-height:70vh; display:flex; align-items:center; justify-content:center;">
  <div style="max-width:520px; width:100%; background:#fff; border-radius:20px; padding:32px; box-shadow:0 10px 30px rgba(0,0,0,.08); text-align:center;">
    <h2 class="mb-2">{{ __('Verify your email') }}</h2>

    @if (session('status') === 'verification-link-sent')
      <div class="alert alert-success" role="alert">
        {{ __('A fresh verification link has been sent to your email address.') }}
      </div>
    @endif

    <p class="text-muted mb-3">
      {{ __("We've sent a verification link to your inbox. Please click it to activate your account.") }}
    </p>

    {{-- Resend verification email --}}
    @php
      $resendRoute = \Illuminate\Support\Facades\Route::has('verification.send')
        ? route('verification.send')
        : url('/email/verification-notification');
    @endphp
    <form method="POST" action="{{ $resendRoute }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-primary">
        {{ __('Resend verification email') }}
      </button>
    </form>

    {{-- Optional: logout --}}
    @php
      $logoutRoute = \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout');
    @endphp
    <form method="POST" action="{{ $logoutRoute }}" class="d-inline ml-2">
      @csrf
      <button type="submit" class="btn btn-outline-secondary">
        {{ __('Log out') }}
      </button>
    </form>

    {{-- Dev-only signed verify URL (safe guards) --}}
    @if (
        app()->environment('local')
        && \Illuminate\Support\Facades\Route::has('verification.verify')
        && auth()->check()
        && ! auth()->user()->hasVerifiedEmail()
    )
      @php
        $user = auth()->user();
        $devSignedUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
          'verification.verify',
          now()->addMinutes(60),
          ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );
      @endphp
      <hr class="my-4">
      <div class="text-left small">
        <strong>Dev shortcut:</strong>
        <a href="{{ $devSignedUrl }}">Verify now (local only)</a>
        <div class="text-muted">Calls <code>/email/verify/{id}/{hash}</code> with a signed URL.</div>
      </div>
    @endif

  </div>
</div>
@endsection
