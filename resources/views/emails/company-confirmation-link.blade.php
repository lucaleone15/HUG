@php
    $dossierCode   = 'SANG-' . date('y');
    $pageUrl       = route('entreprise.show', $entreprise);
    $adminUrl      = config('app.url') . '/admin';
    $dateFormatted = $entreprise->rdv_date
        ? \Carbon\Carbon::parse($entreprise->rdv_date)->locale(app()->getLocale())->translatedFormat('j F Y')
        : null;
    $logoAbsUrl = $entreprise->logo_url
        ? ((substr($entreprise->logo_url, 0, 4) === 'http') ? $entreprise->logo_url : config('app.url') . $entreprise->logo_url)
        : null;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ __('mail.confirmation_title') }}</title>
  <style>
    body, td, th, p, a, span, h1, h2, h3 {
      font-family: {{ $EC['body_font'] }};
    }
  </style>
</head>
<body style="margin:0;padding:0;background:{{ $EC['page_bg'] }};font-family:{{ $EC['body_font'] }};">

<table width="100%" cellpadding="0" cellspacing="0" style="background:{{ $EC['page_bg'] }};padding:32px 16px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  {{-- ── HEADER ──────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['brand'] }};padding:20px 32px;border-radius:12px 12px 0 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="vertical-align:middle;">
            <img src="{{ config('app.url') }}/images/hug-logo_blanc.png" alt="HUG" height="26" style="display:block;">
          </td>
          @if($logoAbsUrl)
          <td style="text-align:center;vertical-align:middle;color:{{ $EC['cross'] }};font-size:15px;font-weight:300;width:40px;">×</td>
          <td style="text-align:right;vertical-align:middle;">
            <table cellpadding="0" cellspacing="0" style="margin-left:auto;">
              <tr>
                <td style="background:{{ $EC['white'] }};border-radius:6px;padding:5px 10px;vertical-align:middle;">
                  <img src="{{ $logoAbsUrl }}" alt="{{ $entreprise->name }}" height="22" style="display:block;">
                </td>
              </tr>
            </table>
          </td>
          @else
          <td style="text-align:right;vertical-align:middle;">
            <span style="color:{{ $EC['on_dark_full'] }};font-size:13px;font-weight:700;">{{ $entreprise->name }}</span>
          </td>
          @endif
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── STATUT ───────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['brand_dark'] }};padding:8px 32px;border-top:1px solid {{ $EC['brand_border'] }};">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="font-size:10px;color:{{ $EC['status_text'] }};">
            <span style="font-weight:700;">{{ __('mail.status_label') }} :</span>&nbsp;{{ __('mail.status_confirmed') }}
          </td>
          @if($dateFormatted)
          <td style="text-align:right;font-size:10px;color:{{ $EC['status_date'] }};font-weight:700;white-space:nowrap;">
            {{ __('mail.date_label') }} : {{ $dateFormatted }}
          </td>
          @endif
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── HERO ─────────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['hero'] }};padding:52px 32px 48px;text-align:center;">
      <p style="margin:0 0 22px;font-size:10px;color:{{ $EC['on_dark_lo'] }};letter-spacing:0.08em;">
        {{ __('mail.hero_comms_label') }}
      </p>
      <table cellpadding="0" cellspacing="0" style="margin:0 auto 32px;">
        <tr>
          <td style="border:2px solid {{ $EC['brand'] }};border-radius:8px;padding:10px 26px;text-align:center;">
            <span style="font-size:13px;font-weight:700;color:{{ $EC['brand'] }};letter-spacing:0.04em;">
              {{ __('mail.hero_stamp') }}
            </span>
          </td>
        </tr>
      </table>
      <h1 style="margin:0 0 20px;font-size:36px;font-weight:900;color:{{ $EC['on_dark_full'] }};line-height:1.05;letter-spacing:-0.5px;">
        {{ __('mail.hero_welcome') }}
      </h1>
      <p style="margin:0 auto;font-size:14px;color:{{ $EC['on_brand_mid'] }};line-height:1.75;max-width:340px;">
        {{ __('mail.hero_subtitle') }}
      </p>
    </td>
  </tr>

  {{-- ── ACTIVATION ───────────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['white'] }};padding:40px 32px 36px;">
      <p style="margin:0 0 6px;font-size:11px;color:{{ $EC['brand'] }};font-weight:600;">
        {{ __('mail.activation_section') }}
      </p>
      <h2 style="margin:0 0 28px;font-size:26px;font-weight:900;color:{{ $EC['ink'] }};line-height:1.15;">
        {{ __('mail.confirmation_title') }}
      </h2>
      <p style="margin:0 0 6px;font-size:15px;color:{{ $EC['ink'] }};line-height:1.7;">
        {{ __('mail.confirmation_greeting') }} <strong>{{ $entreprise->contact_name ?? $entreprise->name }}</strong>,
      </p>
      <p style="margin:0 0 28px;font-size:14px;color:{{ $EC['body_text'] }};line-height:1.75;">
        {!! __('mail.confirmation_body', ['name' => '<strong style="color:' . $EC['ink'] . ';">' . e($entreprise->name) . '</strong>']) !!}
      </p>
      <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;border-left:3px solid {{ $EC['brand'] }};background:{{ $EC['card_bg'] }};border-radius:0 6px 6px 0;">
        <tr>
          <td style="padding:14px 18px;">
            <p style="margin:0 0 5px;font-size:10px;color:{{ $EC['label'] }};font-weight:600;">
              {{ __('mail.confirmation_link_label') }}
            </p>
            <a href="{{ $pageUrl }}" style="color:{{ $EC['brand'] }};font-size:13px;font-weight:600;text-decoration:none;word-break:break-all;">
              {{ $pageUrl }}
            </a>
          </td>
        </tr>
      </table>
      <table cellpadding="0" cellspacing="0" style="margin-bottom:18px;">
        <tr>
          <td style="background:{{ $EC['brand'] }};border-radius:8px;overflow:hidden;">
            <a href="{{ $pageUrl }}"
               style="display:inline-block;background:{{ $EC['brand'] }};color:{{ $EC['white'] }};text-decoration:none;padding:14px 28px;font-size:14px;font-weight:700;border-radius:8px;">
              {{ __('mail.cta_access_page') }}
            </a>
          </td>
        </tr>
      </table>
      <p style="margin:0;">
        <a href="{{ $adminUrl }}" style="font-size:12px;color:{{ $EC['muted'] }};text-decoration:none;font-weight:600;">
          {{ __('mail.admin_link') }}
        </a>
      </p>
    </td>
  </tr>

  {{-- ── KIT DE COMMUNICATION ─────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['dark'] }};padding:40px 32px;">
      <p style="margin:0 0 8px;font-size:11px;color:{{ $EC['brand'] }};font-weight:600;">
        {{ __('mail.kit_section') }}
      </p>
      <h2 style="margin:0 0 8px;font-size:26px;font-weight:900;color:{{ $EC['on_dark_full'] }};line-height:1.15;">
        {{ __('mail.kit_title') }}
      </h2>
      <p style="margin:0 0 28px;font-size:13px;color:{{ $EC['on_dark_mid'] }};line-height:1.65;">
        {{ __('mail.kit_subtitle') }}
      </p>

      {{-- Étape 01 --}}
      <table width="100%" cellpadding="0" cellspacing="0" style="background:{{ $EC['white'] }};margin-bottom:10px;border-radius:8px;overflow:hidden;">
        <tr><td style="padding:20px 24px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
            <tr>
              <td><span style="font-size:10px;color:{{ $EC['brand'] }};font-weight:700;">{{ __('mail.step1_priority') }}</span></td>
              <td style="text-align:right;"><span style="font-size:10px;color:#999999;font-weight:600;background:#f0f0f0;padding:3px 9px;border-radius:4px;">{{ __('mail.step_label') }} 01</span></td>
            </tr>
          </table>
          <h3 style="margin:0 0 8px;font-size:15px;font-weight:800;color:{{ $EC['ink'] }};">{{ __('mail.step1_title') }}</h3>
          <p style="margin:0 0 16px;font-size:12px;color:{{ $EC['step_text'] }};line-height:1.65;">{{ __('mail.step1_body') }}</p>
          <a href="{{ $pageUrl }}"
             style="display:inline-block;background:{{ $EC['brand'] }};color:{{ $EC['white'] }};text-decoration:none;padding:10px 20px;font-size:13px;font-weight:700;border-radius:6px;">
            {{ __('mail.step1_cta') }}
          </a>
        </td></tr>
      </table>

      {{-- Étape 02 --}}
      <table width="100%" cellpadding="0" cellspacing="0" style="background:{{ $EC['white'] }};margin-bottom:10px;border-radius:8px;overflow:hidden;">
        <tr><td style="padding:20px 24px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
            <tr>
              <td><span style="font-size:10px;color:{{ $EC['muted'] }};font-weight:700;">{{ __('mail.step2_priority') }}</span></td>
              <td style="text-align:right;"><span style="font-size:10px;color:#999999;font-weight:600;background:#f0f0f0;padding:3px 9px;border-radius:4px;">{{ __('mail.step_label') }} 02</span></td>
            </tr>
          </table>
          <h3 style="margin:0 0 8px;font-size:15px;font-weight:800;color:{{ $EC['ink'] }};">{{ __('mail.step2_title') }}</h3>
          <p style="margin:0 0 16px;font-size:12px;color:{{ $EC['step_text'] }};line-height:1.65;">{{ __('mail.step2_body') }}</p>
          <a href="{{ $adminUrl }}"
             style="display:inline-block;background:{{ $EC['brand'] }};color:{{ $EC['white'] }};text-decoration:none;padding:10px 20px;font-size:13px;font-weight:700;border-radius:6px;">
            {{ __('mail.step2_cta') }}
          </a>
        </td></tr>
      </table>

      {{-- Étape 03 --}}
      <table width="100%" cellpadding="0" cellspacing="0" style="background:{{ $EC['white'] }};border-radius:8px;overflow:hidden;">
        <tr><td style="padding:20px 24px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
            <tr>
              <td><span style="font-size:10px;color:{{ $EC['muted'] }};font-weight:700;">{{ __('mail.step3_priority') }}</span></td>
              <td style="text-align:right;"><span style="font-size:10px;color:#999999;font-weight:600;background:#f0f0f0;padding:3px 9px;border-radius:4px;">{{ __('mail.step_label') }} 03</span></td>
            </tr>
          </table>
          <h3 style="margin:0 0 8px;font-size:15px;font-weight:800;color:{{ $EC['ink'] }};">{{ __('mail.step3_title') }}</h3>
          <p style="margin:0 0 16px;font-size:12px;color:{{ $EC['step_text'] }};line-height:1.65;">{{ __('mail.step3_body') }}</p>
          <a href="{{ $adminUrl }}"
             style="display:inline-block;background:{{ $EC['brand'] }};color:{{ $EC['white'] }};text-decoration:none;padding:10px 20px;font-size:13px;font-weight:700;border-radius:6px;">
            {{ __('mail.step3_cta') }}
          </a>
        </td></tr>
      </table>
    </td>
  </tr>

  {{-- ── RÉCAPITULATIF ────────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['recap_bg'] }};padding:40px 32px;">
      <p style="margin:0 0 10px;font-size:11px;color:{{ $EC['recap_text'] }};font-weight:600;">
        {{ __('mail.recap_section') }}
      </p>
      <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
          <td style="vertical-align:bottom;">
            <h2 style="margin:0;font-size:28px;font-weight:900;color:{{ $EC['recap_ink'] }};line-height:1.1;">
              {{ __('mail.recap_title') }}
            </h2>
          </td>
          <td style="text-align:right;vertical-align:middle;">
            <span style="font-size:11px;color:{{ $EC['brand'] }};font-weight:700;border:1.5px solid {{ $EC['brand'] }};padding:5px 12px;white-space:nowrap;border-radius:20px;">
              {{ __('mail.recap_badge') }}
            </span>
          </td>
        </tr>
      </table>
      <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid {{ $EC['recap_border'] }};">
        <tr>
          <td style="padding:13px 0;border-bottom:1px solid {{ $EC['recap_border'] }};font-size:10px;color:{{ $EC['recap_text'] }};font-weight:600;width:130px;">{{ __('mail.recap_company') }}</td>
          <td style="padding:13px 0 13px 16px;border-bottom:1px solid {{ $EC['recap_border'] }};font-size:13px;font-weight:700;color:{{ $EC['recap_ink'] }};">{{ $entreprise->name }}</td>
        </tr>
        @if($dateFormatted)
        <tr>
          <td style="padding:13px 0;border-bottom:1px solid {{ $EC['recap_border'] }};font-size:10px;color:{{ $EC['recap_text'] }};font-weight:600;">{{ __('mail.recap_date') }}</td>
          <td style="padding:13px 0 13px 16px;border-bottom:1px solid {{ $EC['recap_border'] }};font-size:13px;font-weight:700;color:{{ $EC['brand'] }};">{{ $dateFormatted }}</td>
        </tr>
        @endif
        @if($entreprise->contact_name)
        <tr>
          <td style="padding:13px 0;font-size:10px;color:{{ $EC['recap_text'] }};font-weight:600;">{{ __('mail.recap_contact') }}</td>
          <td style="padding:13px 0 13px 16px;font-size:13px;font-weight:700;color:{{ $EC['recap_ink'] }};">{{ $entreprise->contact_name }}</td>
        </tr>
        @endif
      </table>
    </td>
  </tr>

  {{-- ── RÉFÉRENT ─────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['dark'] }};padding:40px 32px;">
      <p style="margin:0 0 10px;font-size:11px;color:{{ $EC['brand'] }};font-weight:600;">
        {{ __('mail.referent_section') }}
      </p>
      <h2 style="margin:0 0 28px;font-size:22px;font-weight:900;color:{{ $EC['on_dark_full'] }};line-height:1.2;">
        {{ __('mail.referent_title') }}
      </h2>
      <table cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
          <td style="vertical-align:top;padding-right:16px;">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td width="44" height="44" align="center" valign="middle"
                    style="background:{{ $EC['brand'] }};border-radius:22px;width:44px;height:44px;">
                  <span style="font-size:13px;font-weight:900;color:{{ $EC['white'] }};">FF</span>
                </td>
              </tr>
            </table>
          </td>
          <td style="vertical-align:middle;">
            <p style="margin:0 0 3px;font-size:14px;font-weight:800;color:{{ $EC['on_dark_full'] }};">François Freitas</p>
            <p style="margin:0;font-size:11px;color:{{ $EC['on_dark_mid'] }};line-height:1.55;">
              {{ __('mail.referent_role') }}
            </p>
          </td>
        </tr>
      </table>
      <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid {{ $EC['dark_border'] }};">
        <tr>
          <td style="padding:13px 0;border-bottom:1px solid {{ $EC['dark_border'] }};">
            <span style="font-size:10px;color:{{ $EC['on_dark_lo'] }};font-weight:600;">{{ __('mail.referent_email') }} :</span>
            <a href="mailto:francois.freitas@hug.ch"
               style="color:{{ $EC['on_dark_hi'] }};font-size:12px;text-decoration:none;margin-left:10px;">
              francois.freitas@hug.ch
            </a>
          </td>
        </tr>
        <tr>
          <td style="padding:13px 0;">
            <span style="font-size:10px;color:{{ $EC['on_dark_lo'] }};font-weight:600;">{{ __('mail.referent_phone') }} :</span>
            <span style="color:{{ $EC['on_dark_hi'] }};font-size:12px;margin-left:10px;">079 553 41 05</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── FOOTER ───────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['brand'] }};padding:32px 32px 28px;border-radius:0 0 12px 12px;">
      <p style="margin:0 0 5px;font-size:13px;font-weight:900;color:{{ $EC['on_dark_full'] }};">
        HUG × {{ $entreprise->name }} · Dossier {{ $dossierCode }}
      </p>
      <p style="margin:0 0 18px;font-size:11px;color:{{ $EC['on_brand_hi'] }};">
        {{ __('mail.footer_sub_title') }}
      </p>
      <p style="margin:0 0 18px;font-size:10px;color:{{ $EC['on_brand_mid'] }};">
        {{ __('mail.footer_links') }}
      </p>
      <p style="margin:0 0 6px;font-size:10px;color:{{ $EC['on_dark_mid'] }};">
        {{ __('mail.footer_copyright', ['year' => date('Y')]) }}
      </p>
      <p style="margin:0;">
        <a href="{{ config('app.url') }}" style="font-size:10px;color:{{ $EC['on_dark_sep'] }};text-decoration:none;">
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
