@php
    $adminUrl = config('app.url') . '/admin/entreprises';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ __('mail.registration_badge') }} · {{ $entreprise->name }}</title>
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
          <td style="text-align:right;vertical-align:middle;">
            <span style="font-size:11px;color:{{ $EC['on_brand_lo'] }};">donnez-votre-sang.ch</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── BANDEAU STATUT ───────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['brand_dark'] }};padding:8px 32px;border-top:1px solid {{ $EC['brand_border'] }};">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="font-size:10px;color:{{ $EC['status_text'] }};">
            <span style="font-weight:700;">{{ __('mail.registration_badge') }}</span>
          </td>
          <td style="text-align:right;font-size:10px;color:{{ $EC['status_date'] }};font-weight:700;white-space:nowrap;">
            {{ date('d/m/Y H:i') }}
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── HERO ─────────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['hero'] }};padding:48px 32px 44px;text-align:center;">
      <p style="margin:0 0 22px;font-size:10px;color:{{ $EC['on_dark_lo'] }};letter-spacing:0.08em;">
        {{ __('mail.registration_comms_label') }}
      </p>
      <table cellpadding="0" cellspacing="0" style="margin:0 auto 28px;">
        <tr>
          <td style="border:2px solid {{ $EC['brand'] }};border-radius:8px;padding:10px 26px;text-align:center;">
            <span style="font-size:13px;font-weight:700;color:{{ $EC['brand'] }};letter-spacing:0.04em;">
              {{ __('mail.registration_badge') }}
            </span>
          </td>
        </tr>
      </table>
      <h1 style="margin:0 0 14px;font-size:30px;font-weight:900;color:{{ $EC['on_dark_full'] }};line-height:1.1;letter-spacing:-0.4px;">
        {{ __('mail.registration_title', ['name' => $entreprise->name]) }}
      </h1>
      <p style="margin:0 auto;font-size:13px;color:{{ $EC['on_dark_mid'] }};line-height:1.6;max-width:340px;">
        {{ $entreprise->contact_name }}@if($entreprise->contact_email) · {{ $entreprise->contact_email }}@endif
      </p>
    </td>
  </tr>

  {{-- ── CORPS ────────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['white'] }};padding:40px 32px 36px;">
      <p style="margin:0 0 6px;font-size:11px;color:{{ $EC['brand'] }};font-weight:600;">
        {{ __('mail.registration_action_required') }}
      </p>
      <h2 style="margin:0 0 24px;font-size:26px;font-weight:900;color:{{ $EC['ink'] }};line-height:1.15;">
        {{ __('mail.registration_title', ['name' => $entreprise->name]) }}
      </h2>
      <p style="margin:0 0 28px;font-size:14px;color:{{ $EC['body_text'] }};line-height:1.75;">
        {{ __('mail.registration_body') }}
      </p>

      {{-- Récap entreprise --}}
      <table width="100%" cellpadding="0" cellspacing="0"
             style="border-left:3px solid {{ $EC['brand'] }};background:{{ $EC['card_bg'] }};border-radius:0 6px 6px 0;margin-bottom:28px;">
        <tr><td style="padding:16px 20px;">

          <p style="margin:0 0 4px;font-size:10px;color:{{ $EC['label'] }};font-weight:600;">
            {{ __('mail.registration_company_label') }}
          </p>
          <p style="margin:0 0 14px;font-size:16px;font-weight:800;color:{{ $EC['ink'] }};">{{ $entreprise->name }}</p>

          @if($entreprise->type)
          <p style="margin:0 0 4px;font-size:10px;color:{{ $EC['label'] }};font-weight:600;">
            {{ __('mail.registration_sector_label') }}
          </p>
          <p style="margin:0 0 14px;font-size:13px;color:{{ $EC['text'] }};">{{ ucfirst($entreprise->type) }}</p>
          @endif

          @if($entreprise->employee_count)
          <p style="margin:0 0 4px;font-size:10px;color:{{ $EC['label'] }};font-weight:600;">
            {{ __('mail.registration_headcount_label') }}
          </p>
          <p style="margin:0 0 14px;font-size:13px;color:{{ $EC['text'] }};">
            {{ number_format($entreprise->employee_count) }} {{ __('mail.registration_employees') }}
          </p>
          @endif

          @if($entreprise->contact_name || $entreprise->contact_email)
          <p style="margin:0 0 4px;font-size:10px;color:{{ $EC['label'] }};font-weight:600;">
            {{ __('mail.registration_contact_label') }}
          </p>
          <p style="margin:0;font-size:13px;color:{{ $EC['text'] }};">
            {{ $entreprise->contact_name }}
            @if($entreprise->contact_email)
              · <a href="mailto:{{ $entreprise->contact_email }}" style="color:{{ $EC['brand'] }};text-decoration:none;">{{ $entreprise->contact_email }}</a>
            @endif
          </p>
          @endif

        </td></tr>
      </table>

      <table cellpadding="0" cellspacing="0">
        <tr>
          <td style="background:{{ $EC['brand'] }};border-radius:8px;overflow:hidden;">
            <a href="{{ $adminUrl }}"
               style="display:inline-block;background:{{ $EC['brand'] }};color:{{ $EC['white'] }};text-decoration:none;padding:14px 28px;font-size:14px;font-weight:700;border-radius:8px;">
              {{ __('mail.registration_cta') }}
            </a>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ── FOOTER ───────────────────────────────────────────────────── --}}
  <tr>
    <td style="background:{{ $EC['brand'] }};padding:28px 32px;border-radius:0 0 12px 12px;">
      <p style="margin:0 0 4px;font-size:13px;font-weight:900;color:{{ $EC['on_dark_full'] }};">
        {{ __('mail.footer_org') }}
      </p>
      <p style="margin:0 0 16px;font-size:11px;color:{{ $EC['on_brand_hi'] }};">
        {{ __('mail.footer_sub_title') }}
      </p>
      <p style="margin:0;font-size:10px;color:{{ $EC['on_brand_mid'] }};">
        {{ __('mail.registration_footer_note') }} · {{ date('Y') }}
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>

</body>
</html>
