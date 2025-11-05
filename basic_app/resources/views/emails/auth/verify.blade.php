@php
    use App\Helpers\CustomSettings;

    $s = CustomSettings::appSettings();

    $locale = $locale ?? app()->getLocale();
    $dir = in_array(strtolower($locale), ['ar','he','fa','ur']) ? 'rtl' : 'ltr';
    $align = $dir === 'rtl' ? 'right' : 'left';

    $brand = [
        'main'         => $s['main_color'] ?? '#ff7e00',     // like Headspace orange
        'sub'          => $s['sub_color'] ?? '#fff7ef',
        'text'         => $s['text_color'] ?? '#2d2d2d',
        'button'       => $s['button_color'] ?? '#ff7e00',
        'button_text'  => $s['button_text_color'] ?? '#ffffff',
    ];

    $companyName = $s['name_' . ($locale === 'ar' ? 'ar' : 'en')] ?? 'Ecommerce App';
    $logoUrl     = $s['image'] ? asset('storage/' . $s['image']) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>{{ __('auth.verify_email_subject', ['app' => $companyName]) }}</title>
    <style>
        body {
            margin:0; padding:0;
            background:#fefbf6;
            font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;
            color:{{ $brand['text'] }};
            text-align:{{ $align }};
            direction:{{ $dir }};
        }
        .container {
            width:100%; max-width:620px;
            margin:0 auto;
            background:#fff;
            border-radius:16px;
            box-shadow:0 2px 12px rgba(0,0,0,0.05);
            overflow:hidden;
        }
        .header {
            text-align:center;
            padding:32px 20px 16px;
            background:{{ $brand['sub'] }};
        }
        .header img {
            height:48px; width:auto;
        }
        .illustration {
            width:100%;
            display:block;
            margin:0 auto;
            border-bottom:1px solid #eee;
        }
        .content {
            padding:32px 30px;
        }
        h1 {
            font-size:24px;
            margin:0 0 16px;
            color:{{ $brand['text'] }};
        }
        p {
            font-size:16px;
            line-height:1.6;
            margin:0 0 16px;
        }
        .btn {
            display:inline-block;
            padding:14px 28px;
            background:{{ $brand['button'] }};
            color:{{ $brand['button_text'] }};
            text-decoration:none;
            font-weight:600;
            border-radius:8px;
            margin:20px 0;
        }
        .btn:hover { opacity:.9; }
        .footer {
            font-size:13px;
            text-align:center;
            color:#777;
            padding:24px;
            border-top:1px solid #eee;
            line-height:1.5;
        }
        .footer a {
            color:{{ $brand['main'] }};
            text-decoration:none;
        }
    </style>
</head>
<body>

<!-- Hidden preheader -->
<div style="display:none;opacity:0;height:0;overflow:hidden">
    {{ __('auth.verify_preheader', ['app' => $companyName]) }}
</div>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
<tr><td align="center" style="padding:24px 0">

    <div class="container">
        <div class="header">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $companyName }}">
            @else
                <h2 style="color:{{ $brand['main'] }}">{{ $companyName }}</h2>
            @endif
        </div>

        <img src="{{ asset('images/email-verify-illustration.png') }}" alt="" class="illustration">

        <div class="content">
            <h1>{{ __('auth.verify_headline') }}</h1>
            <p>{{ __('auth.verify_intro', ['name' => $user->name ?? __('auth.user')]) }}</p>
            <p>{{ __('auth.verify_cta_text') }}</p>

            <p style="text-align:center;">
                <a href="{{ $verificationUrl }}" class="btn">{{ __('auth.verify_button') }}</a>
            </p>

            <p style="font-size:13px;color:#888;">
                {{ __('auth.verify_alt', ['url' => $verificationUrl]) }}
            </p>
        </div>

        <div class="footer">
            {{ __('auth.email_footer_notice', ['app' => $companyName]) }}<br>
            <a href="{{ url('/') }}">{{ $companyName }}</a>
        </div>
    </div>

</td></tr>
</table>
</body>
</html>
