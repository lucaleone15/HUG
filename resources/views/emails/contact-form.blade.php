<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ __('mail.contact_new_message') }}</title>
</head>
<body style="margin:0;padding:0;background:#E8E2D4;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#E8E2D4;padding:32px 16px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  {{-- ── HEADER ──────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#E30613;padding:18px 32px;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="vertical-align:middle;">
            <img src="{{ config('app.url') }}/images/hug-logo_blanc.svg" alt="HUG" height="26" style="display:block;">
          </td>
          <td style="text-align:right;vertical-align:middle;">
            <span style="font-size:10px;color:rgba(255,255,255,0.35);letter-spacing:0.1em;text-transform:uppercase;">donnez-votre-sang.ch</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── TYPE / STATUT ────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#C5000E;padding:9px 32px;border-top:1px solid #A8000C;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="font-size:9px;color:rgba(255,255,255,0.4);letter-spacing:0.14em;text-transform:uppercase;">
            <span style="color:rgba(255,255,255,0.6);font-weight:700;">{{ __('mail.contact_new_message') }}</span>
          </td>
          <td style="text-align:right;font-size:9px;color:rgba(255,255,255,0.55);letter-spacing:0.1em;text-transform:uppercase;font-weight:700;white-space:nowrap;">
            {{ __('mail.' . $type) }}
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── HERO ─────────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#0D0D0D;padding:44px 32px 40px;text-align:center;">
      <p style="margin:0 0 28px;font-size:9px;color:rgba(255,255,255,0.3);letter-spacing:0.22em;text-transform:uppercase;">
        {{ __('mail.hero_comms_label') }}
      </p>
      <table cellpadding="0" cellspacing="0" style="margin:0 auto 28px;">
        <tr>
          <td style="border:2px solid #E30613;padding:8px 22px;">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td style="border:1px solid rgba(227,6,19,0.5);padding:5px 14px;">
                  <span style="font-size:12px;font-weight:900;color:#E30613;letter-spacing:0.22em;text-transform:uppercase;">
                    {{ __('mail.contact_new_message') }}
                  </span>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <h1 style="margin:0 0 12px;font-size:32px;font-weight:900;color:#ffffff;line-height:1.1;letter-spacing:-0.5px;">
        {{ __('mail.' . $type) }}
      </h1>
      <p style="margin:0 auto;font-size:13px;color:rgba(255,255,255,0.4);line-height:1.6;max-width:320px;">
        {{ $senderName }} · {{ $senderEmail }}
      </p>
    </td>
  </tr>

  {{-- ── EXPÉDITEUR ───────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#ffffff;padding:32px 32px 24px;">
      <p style="margin:0 0 14px;font-size:9px;color:#E30613;letter-spacing:0.22em;text-transform:uppercase;font-weight:700;">
        {{ __('mail.contact_from') }}
      </p>
      <table width="100%" cellpadding="0" cellspacing="0" style="border-left:3px solid #E30613;background:#fafafa;">
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
    <td style="background:#ffffff;padding:0 32px 32px;">
      <p style="margin:0 0 14px;font-size:9px;color:#8A7A60;letter-spacing:0.22em;text-transform:uppercase;font-weight:700;">
        {{ __('mail.contact_message') }}
      </p>
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#F0EBE0;border-top:2px solid #C4B080;">
        <tr>
          <td style="padding:20px 24px;">
            <p style="margin:0;font-size:14px;color:#3A2A1A;line-height:1.75;white-space:pre-wrap;font-style:italic;">{{ $userMessage }}</p>
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
          <td style="background:#E30613;border-radius:6px;overflow:hidden;">
            <a href="mailto:{{ $senderEmail }}"
               style="display:inline-block;background:#E30613;color:#ffffff;text-decoration:none;padding:14px 28px;font-size:10px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;border-radius:6px;">
              {{ __('mail.contact_reply_cta', ['name' => $senderName]) }}
            </a>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── FOOTER ───────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#E30613;padding:28px 32px;">
      <p style="margin:0 0 4px;font-size:12px;font-weight:900;color:#ffffff;letter-spacing:0.1em;text-transform:uppercase;">
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
