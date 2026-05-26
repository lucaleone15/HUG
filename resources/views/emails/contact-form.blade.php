<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:40px 16px;">
    <tr><td align="center">
      <table width="580" cellpadding="0" cellspacing="0"
             style="background:#ffffff;border-radius:8px;overflow:hidden;max-width:580px;width:100%;">

        {{-- Header --}}
        <tr>
          <td style="background:#C41B1B;padding:32px 40px;text-align:center;">
            <span style="color:#ffffff;font-size:18px;font-weight:600;letter-spacing:0.3px;">
              ♥ donnez-votre-sang.ch
            </span>
          </td>
        </tr>

        {{-- Corps --}}
        <tr>
          <td style="padding:40px 40px 0;">
            <p style="margin:0 0 6px;font-size:12px;color:#888;text-transform:uppercase;letter-spacing:1px;font-weight:500;">
              {{ __('mail.contact_new_message') }}
            </p>
            <h1 style="margin:0 0 24px;font-size:20px;font-weight:600;color:#1a1a1a;">
              {{ __('mail.' . $type) }}
            </h1>

            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
              <tr>
                <td style="font-size:12px;color:#888;padding-bottom:4px;text-transform:uppercase;letter-spacing:0.8px;font-weight:600;">{{ __('mail.contact_from') }}</td>
              </tr>
              <tr>
                <td style="font-size:15px;color:#1a1a1a;font-weight:500;">{{ $senderName }}</td>
              </tr>
              <tr>
                <td style="font-size:14px;color:#C41B1B;">{{ $senderEmail }}</td>
              </tr>
            </table>

            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#f9f9f9;border-radius:8px;padding:20px 24px;">
              <tr>
                <td style="font-size:12px;color:#888;padding-bottom:12px;text-transform:uppercase;letter-spacing:0.8px;font-weight:600;">{{ __('mail.contact_message') }}</td>
              </tr>
              <tr>
                <td style="font-size:15px;color:#444;line-height:1.65;white-space:pre-wrap;">{{ $userMessage }}</td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Répondre --}}
        <tr>
          <td style="padding:32px 40px;text-align:center;">
            <a href="mailto:{{ $senderEmail }}"
               style="display:inline-block;background:#C41B1B;color:#ffffff;text-decoration:none;padding:13px 32px;border-radius:6px;font-size:14px;font-weight:600;letter-spacing:0.3px;">
              {{ __('mail.contact_reply_cta', ['name' => $senderName]) }}
            </a>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="border-top:1px solid #f0f0f0;padding:24px 40px;background:#fafafa;">
            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;color:#999;">
              <tr>
                <td>
                  <p style="margin:0 0 4px;color:#666;font-weight:500;">{{ __('mail.footer_org') }}</p>
                  <p style="margin:0;">{{ __('mail.footer_center') }}</p>
                </td>
                <td style="text-align:right;vertical-align:top;">
                  <a href="{{ config('app.url') }}" style="color:#999;text-decoration:none;">donnez-votre-sang.ch</a>
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
