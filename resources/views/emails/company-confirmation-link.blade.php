<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:48px 16px;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- Header blanc logo --}}
        <tr>
          <td style="background:#ffffff;padding:18px 40px;border-bottom:3px solid #E30613;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="vertical-align:middle;">
                  <img src="{{ config('app.url') }}/images/hug-logo.svg" alt="HUG × CTS" height="36" style="display:block;">
                </td>
                <td style="text-align:right;vertical-align:middle;">
                  <span style="font-size:11px;color:#aaaaaa;letter-spacing:0.5px;">donnez-votre-sang.ch</span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Titre sur fond rouge --}}
        <tr>
          <td style="background:#E30613;padding:32px 40px 36px;">
            <p style="margin:0 0 8px;font-size:11px;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:2px;font-weight:600;">
              {{ __('mail.confirmation_activated') }}
            </p>
            <h1 style="margin:0;font-size:26px;font-weight:700;color:#ffffff;line-height:1.25;letter-spacing:-0.3px;">
              {{ __('mail.confirmation_title') }}
            </h1>
          </td>
        </tr>

        {{-- Corps blanc --}}
        <tr>
          <td style="background:#ffffff;padding:40px 40px 32px;">
            <p style="margin:0 0 20px;font-size:15px;color:#111111;line-height:1.7;">
              {{ __('mail.confirmation_greeting') }} <strong>{{ $entreprise->contact_name }}</strong>,
            </p>
            <p style="margin:0;font-size:15px;color:#555555;line-height:1.7;">
              {!! __('mail.confirmation_body', ['name' => '<strong style="color:#111111;">' . e($entreprise->name) . '</strong>']) !!}
            </p>
          </td>
        </tr>

        {{-- Bloc lien --}}
        <tr>
          <td style="background:#ffffff;padding:0 40px 40px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#FEF2F2;border-left:3px solid #E30613;padding:20px 24px;">
              <tr>
                <td>
                  <p style="margin:0 0 8px;font-size:10px;color:#888888;text-transform:uppercase;letter-spacing:1.5px;font-weight:700;">
                    {{ __('mail.confirmation_link_label') }}
                  </p>
                  <p style="margin:0;font-size:14px;color:#E30613;font-weight:600;word-break:break-all;line-height:1.5;">
                    {{ route('entreprise.show', $entreprise) }}
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- CTA --}}
        <tr>
          <td style="background:#ffffff;padding:0 40px 48px;text-align:center;">
            <a href="{{ route('entreprise.show', $entreprise) }}"
               style="display:inline-block;background:#E30613;color:#ffffff;text-decoration:none;padding:14px 36px;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;">
              {{ __('mail.confirmation_cta') }}
            </a>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="background:#111111;padding:28px 40px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td>
                  <p style="margin:0 0 3px;font-size:12px;color:#cccccc;font-weight:600;">
                    {{ __('mail.footer_org') }}
                  </p>
                  <p style="margin:0;font-size:11px;color:#888888;">
                    {{ __('mail.footer_center') }}
                  </p>
                </td>
                <td style="text-align:right;vertical-align:middle;">
                  <span style="font-size:11px;color:#E30613;font-weight:700;letter-spacing:1px;text-transform:uppercase;">
                    ♥
                  </span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>

</body>
</html>
