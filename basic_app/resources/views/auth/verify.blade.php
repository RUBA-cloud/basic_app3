@extends('adminlte::page')

@section('content')
<div style="min-height: 100vh; display: flex; justify-content: center; align-items: center;">
    <div style="background: #fff; border-radius: 24px; box-shadow: 0 8px 32px 0 rgba(31,38,135,0.12); padding: 48px 32px; max-width: 420px; width: 100%; text-align: center;">
        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 12px;">
            {{ __('adminlte::adminlte.verify_your_email') }}
        </h2>

        <p style="color: #888; font-size: 1rem; margin-bottom: 28px;">
            {{ __('adminlte::adminlte.verify_email_instruction') ?? 'Please click the button below to verify your email address.' }}
        </p>

        @if (session('resent'))
            <div style="color: #28a745; margin-bottom: 16px;">
                {{ __('adminlte::adminlte.verify_your_email') }}
            </div>
        @endif

        <form method="GET" action="{{ route('verify') }}">
            @csrf
            <button type="submit"
                style="width: 100%; background: #6C63FF; color: #fff; font-size: 1.1rem; font-weight: 600; border: none; border-radius: 24px; padding: 14px 0; margin-bottom: 18px; cursor: pointer; box-shadow: 0 4px 16px 0 rgba(108,99,255,0.15); transition: background 0.2s;">            Submit
</button>
        </form>
    </div>
</div>
@endsection
