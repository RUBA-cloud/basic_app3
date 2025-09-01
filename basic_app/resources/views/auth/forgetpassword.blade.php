{{-- filepath: resources/views/emails/password_reset.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('adminlte::adminlte.reset_password') }}</title>
</head>
<body style="margin:0; padding:0; background: #f6f8fc;">
    <div style="width:100%; background: #f6f8fc; padding: 40px 0;">
        <div style="max-width: 480px; margin: 0 auto; background: #fff; border-radius: 18px; box-shadow: 0 8px 32px 0 rgba(31,38,135,0.10); padding: 40px 32px; font-family: 'Inter', Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 24px;">
                <h2 style="font-size: 1.7rem; font-weight: 700; color: #22223B; margin-bottom: 8px;">{{ __('adminlte::adminlte.reset_password') }}</h2>
            </div>
            <p style="color: #888; font-size: 1rem; margin-bottom: 28px; text-align:center;">
              {{ __('adminlte::adminlte.you_are_receiving_this_email') }}<br><br>
                <strong>This password reset link will expire in 60 minutes.</strong><br><br>
                If you did not request a password reset, no further action is required.
            </p>



            <div style="text-align: center; margin-bottom: 32px;">
                <a href="{{ $url }}" style="display:inline-block; background:#6C63FF; color:#fff; font-size:1.1rem; font-weight:600; border:none; border-radius:24px; padding:14px 32px; text-decoration:none; box-shadow:0 4px 16px 0 rgba(108,99,255,0.15); transition:background 0.2s;">
  {{ __('adminlte::adminlte.reset_password')}}                </a>
            </div>

            <p style="color: #bbb; font-size: 0.95rem; text-align:center;">
                If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
                <a href="{{ $url }}" style="color:#6C63FF;">{{ $url }}</a>
            </p>
        </div>
    </div>
</body>
</html>
