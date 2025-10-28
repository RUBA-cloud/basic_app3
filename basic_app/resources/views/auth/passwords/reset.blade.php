@php
    $dir   = in_array(strtolower($locale ?? app()->getLocale()), ['ar','he','fa','ur']) ? 'rtl' : 'ltr';
    $align = $dir === 'rtl' ? 'right' : 'left';
    $brand = $colors ?? [
        'main_color' => '#6C5CE7',  // primary (button, accents)
        'sub_color'  => '#1A202C',  // header background
        'text_color' => '#22223B',  // body text
    ];

    // sensible fallbacks
    $appName     = $appName     ?? config('app.name', 'My App');
    $logoUrl     = $logoUrl     ?? null;
    $preheader   = $preheader   ?? __('adminlte::adminlte.reset_preheader', ['app' => $appName]);
    $expiresIn   = $expiresIn   ?? 60; // minutes
    $resetUrl    = $resetUrl    ?? '#';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale ?? app()->getLocale() }}" dir="{{ $dir }}">
<head>
  <meta charset="utf-8">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('adminlte::adminlte.reset_password_subject', ['app' => $appName]) }}</title>

  <style>
    /* Layout helpers (email-safe) */
    .container { width: 600px; max-width: 100%; }
    .card {
      background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; overflow:hidden;
      box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    }
    .header-hero {
      padding: 28px 24px;
      /* subtle gradient header */
      background: linear-gradient(135deg, {{ $brand['sub_color'] }} 0%, #0f172a 100%);
      color:#fff;
    }
    .brand {
      font-size: 18px; font-weight: 700; margin:0;
    }
    .body { padding: 28px 24px; color: {{ $brand['text_color'] }}; }
    .headline { margin: 0 0 12px 0; font-size: 22px; }
    .lead { margin: 0 0 16px 0; line-height: 1.6; }
    .btn {
      display:inline-block; padding:14px 22px; border-radius:10px;
      text-decoration:none; color:#fff; background: {{ $brand['main_color'] }};
      font-weight: 700;
    }
    .btn:hover { opacity: .95; }
    .muted { color:#6B7280; font-size: 12px; line-height: 1.6; }
    .divider { height: 1px; background: #e5e7eb; }
    .footer { padding: 16px 24px; }

    /* “lock” icon using safe inline styles (fallback to emoji if images blocked) */
    .lock-wrap { display:inline-flex; align-items:center; gap:10px; }
    .lock {
      width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,.12);
      display:inline-flex; align-items:center; justify-content:center;
    }

    /* Dark mode */
    @media (prefers-color-scheme: dark){
      body { background:#0b0d12!important; color:#e5e7eb!important; }
      .card { background:#0f172a!important; border-color:#1f2937!important; }
      .divider { background: #1f2937!important; }
      .muted { color:#9CA3AF!important; }
    }

    /* Mobile tweaks */
    @media (max-width: 480px) {
      .body, .header-hero, .footer { padding: 22px 18px !important; }
      .headline { font-size: 20px !important; }
    }
  </style>
</head>

<body style="margin:0;padding:0;background:#f6f7fb;color:{{ $brand['text_color'] }};direction:{{ $dir }};text-align:{{ $align }}">
  <!-- Preheader: appears as preview text in inbox -->
  <div style="display:none;opacity:0;max-height:0;overflow:hidden;">
    {{ $preheader }}
  </div>

  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f7fb;padding:24px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" class="container" cellspacing="0" cellpadding="0">
          <tr>
            <td>

              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" class="card">
                <!-- Header -->
                <tr>
                  <td class="header-hero">
                    <table width="100%" role="presentation">
                      <tr>
                        <td style="text-align:{{ $align }};">
                          @if(!empty($logoUrl))
                            <img src="{{ $logoUrl }}" alt="{{ $appName }}" width="120" style="display:block; border:0;">
                          @else
                            <h1 class="brand" style="color:#fff;">{{ $appName }}</h1>
                          @endif
                        </td>
                        <td style="text-align:{{ $dir === 'rtl' ? 'left' : 'right' }};">
                          <span class="lock-wrap" aria-hidden="true" style="color:#fff;">
                            <span class="lock">🔒</span>
                            <span style="opacity:.9;font-size:13px;">{{ __('adminlte::adminlte.security_notice') }}</span>
                          </span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>

                <!-- Body -->
                <tr>
                  <td class="body">
                    <h2 class="headline">{{ __('adminlte::adminlte.reset_headline') }}</h2>

                    <p class="lead">
                      {{ __('adminlte::adminlte.reset_intro', ['name' => $user->name ?? __('adminlte::adminlte.auth.user')]) }}
                    </p>

                    <p class="lead">
                      {{ __('adminlte::adminlte.reset_cta_text') }}
                    </p>

                    <p style="margin: 0 0 24px 0;">
                      <a href="{{ $resetUrl }}" class="btn">{{ __('adminlte::adminlte.reset_button') }}</a>
                    </p>

                    <p class="muted" style="margin: 0 0 8px 0;">
                      {{ __('adminlte::adminlte.reset_expiry_note', ['minutes' => $expiresIn]) }}
                    </p>

                    <p class="muted" style="margin:0;">
                      {{ __('adminlte::adminlte.reset_alt', ['url' => $resetUrl]) }}
                    </p>
                  </td>
                </tr>

                <tr><td class="divider"></td></tr>

                <!-- Footer -->
                <tr>
                  <td class="footer">
                    <p class="muted" style="margin:0;">
                      {{ __('adminlte::adminlte.email_footer_notice', ['app' => $appName]) }}
                    </p>
                  </td>
                </tr>
              </table>

            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>
</body>
</html>
