<!DOCTYPE html>
<html lang="fr">
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
              Votre espace est activé
            </p>
            <h1 style="margin:0 0 20px;font-size:22px;font-weight:600;color:#1a1a1a;line-height:1.3;">
              Votre page co-brandée est en ligne
            </h1>
            <p style="margin:0 0 16px;font-size:15px;color:#444;line-height:1.65;">
              Bonjour <strong style="color:#1a1a1a;">{{ $entreprise->contact_name }}</strong>,
            </p>
            <p style="margin:0 0 0;font-size:15px;color:#444;line-height:1.65;">
              La page de collecte de <strong style="color:#1a1a1a;">{{ $entreprise->name }}</strong>
              est maintenant active. Partagez ce lien à vos collaborateurs pour qu'ils puissent
              participer à la campagne.
            </p>
          </td>
        </tr>

        {{-- Lien mis en avant --}}
        <tr>
          <td style="padding:28px 40px 0;">
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#FEF2F2;border-radius:8px;border-left:4px solid #C41B1B;padding:20px 24px;">
              <tr>
                <td style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:0.8px;font-weight:600;padding-bottom:10px;">
                  Lien de votre espace entreprise
                </td>
              </tr>
              <tr>
                <td style="font-size:15px;color:#C41B1B;font-weight:600;word-break:break-all;">
                  {{ route('entreprise.show', $entreprise) }}
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- CTA --}}
        <tr>
          <td style="padding:32px 40px;text-align:center;">
            <a href="{{ route('entreprise.show', $entreprise) }}"
               style="display:inline-block;background:#C41B1B;color:#ffffff;text-decoration:none;padding:13px 32px;border-radius:6px;font-size:14px;font-weight:600;letter-spacing:0.3px;">
              Accéder à ma page →
            </a>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="border-top:1px solid #f0f0f0;padding:24px 40px;background:#fafafa;">
            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;color:#999;">
              <tr>
                <td>
                  <p style="margin:0 0 4px;color:#666;font-weight:500;">HUG – Hôpitaux Universitaires de Genève</p>
                  <p style="margin:0;">Centre de Transfusion Sanguine · Genève</p>
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
