@php
    $dossierCode = 'SANG-' . date('y');
    $pageUrl     = route('entreprise.show', $entreprise);
    $adminUrl    = config('app.url') . '/admin';
    $dateFormatted = $entreprise->rdv_date
        ? \Carbon\Carbon::parse($entreprise->rdv_date)->locale(app()->getLocale())->translatedFormat('j F Y')
        : null;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ __('mail.confirmation_title') }}</title>
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
          <td style="text-align:center;vertical-align:middle;color:rgba(255,255,255,0.3);font-size:16px;font-weight:300;width:40px;">×</td>
          <td style="text-align:right;vertical-align:middle;">
            @if($entreprise->logo_url)
              <img src="{{ $entreprise->logo_url }}" alt="{{ $entreprise->name }}" height="26"
                   style="display:block;margin-left:auto;filter:brightness(0) invert(1);">
            @else
              <span style="color:#ffffff;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:0.12em;">
                {{ strtoupper($entreprise->name) }}
              </span>
            @endif
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── STATUT ───────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#C5000E;padding:9px 32px;border-top:1px solid #A8000C;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="font-size:9px;color:rgba(255,255,255,0.4);letter-spacing:0.14em;text-transform:uppercase;">
            <span style="color:rgba(255,255,255,0.6);font-weight:700;">{{ __('mail.status_label') }} :</span>&nbsp;
            {{ __('mail.status_confirmed') }}
          </td>
          @if($dateFormatted)
          <td style="text-align:right;font-size:9px;color:rgba(255,255,255,0.4);letter-spacing:0.14em;text-transform:uppercase;white-space:nowrap;">
            <span style="color:rgba(255,255,255,0.6);font-weight:700;">{{ __('mail.date_label') }} :</span>&nbsp;
            {{ $dateFormatted }}
          </td>
          @endif
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── HERO ─────────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#0D0D0D;padding:52px 32px 48px;text-align:center;">
      <p style="margin:0 0 28px;font-size:9px;color:rgba(255,255,255,0.3);letter-spacing:0.22em;text-transform:uppercase;">
        {{ __('mail.hero_comms_label') }}
      </p>
      <table cellpadding="0" cellspacing="0" style="margin:0 auto 32px;">
        <tr>
          <td style="border:2px solid #E30613;padding:8px 22px;">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td style="border:1px solid rgba(227,6,19,0.5);padding:5px 14px;">
                  <span style="font-size:12px;font-weight:900;color:#E30613;letter-spacing:0.22em;text-transform:uppercase;">
                    {{ __('mail.hero_stamp') }}
                  </span>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <h1 style="margin:0 0 20px;font-size:38px;font-weight:900;color:#ffffff;line-height:1.05;letter-spacing:-0.5px;">
        {{ __('mail.hero_welcome') }}
      </h1>
      <p style="margin:0 auto;font-size:14px;color:rgba(255,255,255,0.5);line-height:1.75;max-width:340px;">
        {{ __('mail.hero_subtitle') }}
      </p>
    </td>
  </tr>

  {{-- ── ACTIVATION ───────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#ffffff;padding:40px 32px 36px;">
      <p style="margin:0 0 14px;font-size:9px;color:#E30613;letter-spacing:0.22em;text-transform:uppercase;font-weight:700;">
        {{ __('mail.activation_section') }}
      </p>
      <h2 style="margin:0 0 28px;font-size:26px;font-weight:900;color:#111111;line-height:1.15;">
        {{ __('mail.confirmation_title') }}
      </h2>
      <p style="margin:0 0 6px;font-size:15px;color:#111111;line-height:1.7;">
        {{ __('mail.confirmation_greeting') }} <strong>{{ $entreprise->contact_name ?? $entreprise->name }}</strong>,
      </p>
      <p style="margin:0 0 28px;font-size:14px;color:#555555;line-height:1.75;">
        {!! __('mail.confirmation_body', ['name' => '<strong style="color:#111111;">' . e($entreprise->name) . '</strong>']) !!}
      </p>
      <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;border-left:3px solid #E30613;background:#fafafa;">
        <tr>
          <td style="padding:14px 18px;">
            <p style="margin:0 0 5px;font-size:9px;color:#aaaaaa;text-transform:uppercase;letter-spacing:0.14em;font-weight:700;">
              {{ __('mail.confirmation_link_label') }}
            </p>
            <a href="{{ $pageUrl }}" style="color:#E30613;font-size:13px;font-weight:600;text-decoration:none;word-break:break-all;">
              {{ $pageUrl }}
            </a>
          </td>
        </tr>
      </table>
      <table cellpadding="0" cellspacing="0" style="margin-bottom:18px;">
        <tr>
          <td style="background:#E30613;border-radius:6px;overflow:hidden;">
            <a href="{{ $pageUrl }}"
               style="display:inline-block;background:#E30613;color:#ffffff;text-decoration:none;padding:14px 28px;font-size:10px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;border-radius:6px;">
              {{ __('mail.cta_access_page') }}
            </a>
          </td>
        </tr>
      </table>
      <p style="margin:0;">
        <a href="{{ $adminUrl }}"
           style="font-size:10px;color:#888888;text-decoration:none;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">
          {{ __('mail.admin_link') }} →
        </a>
      </p>
    </td>
  </tr>

  {{-- ── KIT DE COMMUNICATION ─────────────────────────────────────── --}}
  <tr>
    <td style="background:#111111;padding:40px 32px;">
      <p style="margin:0 0 10px;font-size:9px;color:#E30613;letter-spacing:0.22em;text-transform:uppercase;font-weight:700;">
        {{ __('mail.kit_section') }}
      </p>
      <h2 style="margin:0 0 8px;font-size:26px;font-weight:900;color:#ffffff;line-height:1.15;">
        {{ __('mail.kit_title') }}
      </h2>
      <p style="margin:0 0 28px;font-size:13px;color:rgba(255,255,255,0.4);line-height:1.65;">
        {{ __('mail.kit_subtitle') }}
      </p>

      {{-- Étape 01 --}}
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;margin-bottom:10px;">
        <tr><td style="padding:20px 24px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
            <tr>
              <td><span style="font-size:9px;color:#E30613;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">{{ __('mail.step1_priority') }}</span></td>
              <td style="text-align:right;"><span style="font-size:9px;color:#999999;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;background:#f0f0f0;padding:3px 9px;">{{ __('mail.step_label') }} 01</span></td>
            </tr>
          </table>
          <h3 style="margin:0 0 8px;font-size:15px;font-weight:800;color:#111111;">{{ __('mail.step1_title') }}</h3>
          <p style="margin:0 0 16px;font-size:12px;color:#666666;line-height:1.65;">{{ __('mail.step1_body') }}</p>
          <a href="{{ $pageUrl }}"
             style="display:inline-block;background:#E30613;color:#ffffff;text-decoration:none;padding:10px 20px;font-size:9px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;border-radius:6px;">
            {{ __('mail.step1_cta') }} →
          </a>
        </td></tr>
      </table>

      {{-- Étape 02 --}}
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;margin-bottom:10px;">
        <tr><td style="padding:20px 24px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
            <tr>
              <td><span style="font-size:9px;color:#888888;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">{{ __('mail.step2_priority') }}</span></td>
              <td style="text-align:right;"><span style="font-size:9px;color:#999999;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;background:#f0f0f0;padding:3px 9px;">{{ __('mail.step_label') }} 02</span></td>
            </tr>
          </table>
          <h3 style="margin:0 0 8px;font-size:15px;font-weight:800;color:#111111;">{{ __('mail.step2_title') }}</h3>
          <p style="margin:0 0 16px;font-size:12px;color:#666666;line-height:1.65;">{{ __('mail.step2_body') }}</p>
          <a href="{{ $adminUrl }}"
             style="display:inline-block;background:#111111;color:#ffffff;text-decoration:none;padding:10px 20px;font-size:9px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;border-radius:6px;">
            {{ __('mail.step2_cta') }} →
          </a>
        </td></tr>
      </table>

      {{-- Étape 03 --}}
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;">
        <tr><td style="padding:20px 24px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
            <tr>
              <td><span style="font-size:9px;color:#888888;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">{{ __('mail.step3_priority') }}</span></td>
              <td style="text-align:right;"><span style="font-size:9px;color:#999999;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;background:#f0f0f0;padding:3px 9px;">{{ __('mail.step_label') }} 03</span></td>
            </tr>
          </table>
          <h3 style="margin:0 0 8px;font-size:15px;font-weight:800;color:#111111;">{{ __('mail.step3_title') }}</h3>
          <p style="margin:0 0 16px;font-size:12px;color:#666666;line-height:1.65;">{{ __('mail.step3_body') }}</p>
          <a href="{{ $adminUrl }}"
             style="display:inline-block;background:#111111;color:#ffffff;text-decoration:none;padding:10px 20px;font-size:9px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;border-radius:6px;">
            {{ __('mail.step3_cta') }} →
          </a>
        </td></tr>
      </table>
    </td>
  </tr>

  {{-- ── RÉCAPITULATIF ────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#F0EBE0;padding:40px 32px;">
      <p style="margin:0 0 10px;font-size:9px;color:#8A7A60;letter-spacing:0.22em;text-transform:uppercase;font-weight:700;">
        {{ __('mail.recap_section') }}
      </p>
      <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
          <td style="vertical-align:bottom;">
            <h2 style="margin:0;font-size:28px;font-weight:900;color:#1A1A1A;line-height:1.1;">
              {{ __('mail.recap_title') }}
            </h2>
          </td>
          <td style="text-align:right;vertical-align:middle;">
            <span style="font-size:9px;color:#E30613;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;border:1.5px solid #E30613;padding:4px 10px;white-space:nowrap;">
              {{ __('mail.recap_badge') }}
            </span>
          </td>
        </tr>
      </table>
      <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #C4B080;">
        <tr>
          <td style="padding:13px 0;border-bottom:1px solid #C4B080;font-size:9px;color:#8A7A60;letter-spacing:0.14em;text-transform:uppercase;font-weight:700;width:130px;">{{ __('mail.recap_company') }}</td>
          <td style="padding:13px 0 13px 16px;border-bottom:1px solid #C4B080;font-size:13px;font-weight:700;color:#1A1A1A;">{{ $entreprise->name }}</td>
        </tr>
        @if($dateFormatted)
        <tr>
          <td style="padding:13px 0;border-bottom:1px solid #C4B080;font-size:9px;color:#8A7A60;letter-spacing:0.14em;text-transform:uppercase;font-weight:700;">{{ __('mail.recap_date') }}</td>
          <td style="padding:13px 0 13px 16px;border-bottom:1px solid #C4B080;font-size:13px;font-weight:700;color:#E30613;">{{ $dateFormatted }}</td>
        </tr>
        @endif
        @if($entreprise->contact_name)
        <tr>
          <td style="padding:13px 0;font-size:9px;color:#8A7A60;letter-spacing:0.14em;text-transform:uppercase;font-weight:700;">{{ __('mail.recap_contact') }}</td>
          <td style="padding:13px 0 13px 16px;font-size:13px;font-weight:700;color:#1A1A1A;">{{ $entreprise->contact_name }}</td>
        </tr>
        @endif
      </table>
    </td>
  </tr>

  {{-- ── RÉFÉRENT ─────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#111111;padding:40px 32px;">
      <p style="margin:0 0 10px;font-size:9px;color:#E30613;letter-spacing:0.22em;text-transform:uppercase;font-weight:700;">
        {{ __('mail.referent_section') }}
      </p>
      <h2 style="margin:0 0 28px;font-size:22px;font-weight:900;color:#ffffff;line-height:1.2;">
        {{ __('mail.referent_title') }}
      </h2>
      <table cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
          <td style="vertical-align:top;padding-right:16px;">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td width="44" height="44" align="center" valign="middle"
                    style="background:#E30613;border-radius:22px;width:44px;height:44px;">
                  <span style="font-size:13px;font-weight:900;color:#ffffff;letter-spacing:0.04em;">FF</span>
                </td>
              </tr>
            </table>
          </td>
          <td style="vertical-align:middle;">
            <p style="margin:0 0 3px;font-size:14px;font-weight:800;color:#ffffff;">François Freitas</p>
            <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.4);line-height:1.55;">
              {{ __('mail.referent_role') }}
            </p>
          </td>
        </tr>
      </table>
      <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #2A2A2A;">
        <tr>
          <td style="padding:13px 0;border-bottom:1px solid #2A2A2A;">
            <span style="font-size:9px;color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:0.14em;font-weight:700;">{{ __('mail.referent_email') }} :</span>
            <a href="mailto:francois.freitas@hug.ch"
               style="color:rgba(255,255,255,0.65);font-size:12px;text-decoration:none;margin-left:12px;">
              francois.freitas@hug.ch
            </a>
          </td>
        </tr>
        <tr>
          <td style="padding:13px 0;">
            <span style="font-size:9px;color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:0.14em;font-weight:700;">{{ __('mail.referent_phone') }} :</span>
            <span style="color:rgba(255,255,255,0.65);font-size:12px;margin-left:12px;">079 553 41 05</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── FOOTER ───────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:#E30613;padding:32px 32px 28px;">
      <p style="margin:0 0 5px;font-size:12px;font-weight:900;color:#ffffff;letter-spacing:0.1em;text-transform:uppercase;">
        HUG × {{ strtoupper($entreprise->name) }} · DOSSIER {{ $dossierCode }}
      </p>
      <p style="margin:0 0 18px;font-size:11px;color:rgba(255,255,255,0.65);">
        {{ __('mail.footer_sub_title') }}
      </p>
      <p style="margin:0 0 18px;font-size:10px;color:rgba(255,255,255,0.5);letter-spacing:0.05em;">
        {{ __('mail.footer_links') }}
      </p>
      <p style="margin:0 0 6px;font-size:10px;color:rgba(255,255,255,0.4);">
        {{ __('mail.footer_copyright', ['year' => date('Y')]) }}
      </p>
      <p style="margin:0;">
        <a href="{{ config('app.url') }}" style="font-size:10px;color:rgba(255,255,255,0.3);text-decoration:none;">
          {{ __('mail.footer_unsubscribe') }}
        </a>
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>

</body>
</html>
