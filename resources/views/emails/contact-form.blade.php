<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ __('mail.contact_new_message') }}</title>
  <style>
    body, td, th, p, a, span, h1, h2, h3 {
      font-family: 'Cooper Hewitt', 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
  </style>
</head>
<body style="margin:0;padding:0;background:#F0EDE8;font-family:'Cooper Hewitt','Helvetica Neue',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F0EDE8;padding:32px 16px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  {{-- ── HEADER ──────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#E30613;padding:20px 32px;border-radius:12px 12px 0 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="vertical-align:middle;">
            <img src="{{ config('app.url') }}/images/hug-logo_blanc.svg" alt="HUG" height="26" style="display:block;">
          </td>
          <td style="text-align:right;vertical-align:middle;">
            <span style="font-size:11px;color:rgba(255,255,255,0.45);">donnez-votre-sang.ch</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── TYPE ─────────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#C5000E;padding:8px 32px;border-top:1px solid #A8000C;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="font-size:10px;color:rgba(255,255,255,0.6);">
            <span style="font-weight:700;">{{ __('mail.contact_new_message') }}</span>
          </td>
          <td style="text-align:right;font-size:10px;color:rgba(255,255,255,0.75);font-weight:700;white-space:nowrap;">
            {{ __('mail.' . $type) }}
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── HERO ─────────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#0D0D0D;padding:48px 32px 44px;text-align:center;">
      <p style="margin:0 0 22px;font-size:10px;color:rgba(255,255,255,0.35);letter-spacing:0.08em;">
        {{ __('mail.hero_comms_label') }}
      </p>
      <table cellpadding="0" cellspacing="0" style="margin:0 auto 28px;">
        <tr>
          <td style="border:2px solid #E30613;border-radius:8px;padding:10px 26px;text-align:center;">
            <span style="font-size:13px;font-weight:700;color:#E30613;letter-spacing:0.04em;">
              {{ __('mail.contact_new_message') }}
            </span>
          </td>
        </tr>
      </table>
      <h1 style="margin:0 0 14px;font-size:30px;font-weight:900;color:#ffffff;line-height:1.1;letter-spacing:-0.4px;">
        {{ __('mail.' . $type) }}
      </h1>
      <p style="margin:0 auto;font-size:13px;color:rgba(255,255,255,0.4);line-height:1.6;max-width:320px;">
        {{ $senderName }} · {{ $senderEmail }}
      </p>
    </td>
  </tr>

  {{-- ── EXPÉDITEUR ───────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#ffffff;padding:36px 32px 0;">
      <p style="margin:0 0 10px;font-size:11px;color:#E30613;font-weight:600;">
        {{ __('mail.contact_from') }}
      </p>
      <table width="100%" cellpadding="0" cellspacing="0" style="border-left:3px solid #E30613;background:#fafafa;border-radius:0 6px 6px 0;">
        <tr>
          <td style="padding:14px 18px;">
            <p style="margin:0 0 4px;font-size:15px;font-weight:800;color:#111111;">{{ $senderName }}</p>
            <a href="mailto:{{ $senderEmail }}"
               style="color:#E30613;font-size:13px;font-weight:600;text-decoration:none;">
              {{ $senderEmail }}
            </a>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── MESSAGE ──────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#ffffff;padding:24px 32px 32px;">
      <p style="margin:0 0 10px;font-size:11px;color:#888888;font-weight:600;">
        {{ __('mail.contact_message') }}
      </p>
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#F7F5F2;border-left:3px solid #E5E0D8;border-radius:0 6px 6px 0;">
        <tr>
          <td style="padding:18px 22px;">
            <p style="margin:0;font-size:14px;color:#333333;line-height:1.75;white-space:pre-wrap;font-style:italic;">{{ $userMessage }}</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── CTA RÉPONDRE ─────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#ffffff;padding:0 32px 40px;">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td style="background:#E30613;border-radius:8px;overflow:hidden;">
            <a href="mailto:{{ $senderEmail }}"
               style="display:inline-block;background:#E30613;color:#ffffff;text-decoration:none;padding:14px 28px;font-size:14px;font-weight:700;border-radius:8px;">
              {{ __('mail.contact_reply_cta', ['name' => $senderName]) }}
            </a>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── FOOTER ───────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#E30613;padding:28px 32px;border-radius:0 0 12px 12px;">
      <p style="margin:0 0 4px;font-size:13px;font-weight:900;color:#ffffff;">
        {{ __('mail.footer_org') }}
      </p>
      <p style="margin:0 0 16px;font-size:11px;color:rgba(255,255,255,0.65);">
        {{ __('mail.footer_sub_title') }}
      </p>
      <p style="margin:0;font-size:10px;color:rgba(255,255,255,0.4);">
        {{ __('mail.footer_copyright', ['year' => date('Y')]) }}
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>

</body>
</html>
