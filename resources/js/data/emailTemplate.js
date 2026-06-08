export const FIXED_IMAGES = [
    {
        url: "https://donnez-votre-sang.ch/assets/hug-logo.png",
        localUrl: "/images/hug-logo.png",
        cid: "huglogo",
    },
    {
        url: "https://donnez-votre-sang.ch/assets/loupe.png",
        localUrl: "/images/loupe.png",
        cid: "loupe",
    },
    {
        url: "https://donnez-votre-sang.ch/assets/chapeau.png",
        localUrl: "/images/chapeau.png",
        cid: "chapeau",
    },
];

export const LOGO_IMG_DEFAULT =
    '<img src="https://donnez-votre-sang.ch/assets/logo.png" width="132" height="44" alt="Logo entreprise" style="display:inline-block; width:132px; height:44px;">';

export const EMAIL_TEMPLATE = `<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<title>Dossier #SANG-26</title>
<!--[if mso]>
<noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
<![endif]-->
<style>
  @font-face{font-family:'Cooper Hewitt';font-style:normal;font-weight:400;src:url('https://cdn.jsdelivr.net/npm/@fontsource/cooper-hewitt@5.2.5/files/cooper-hewitt-latin-400-normal.woff2') format('woff2');}
  @font-face{font-family:'Cooper Hewitt';font-style:normal;font-weight:700;src:url('https://cdn.jsdelivr.net/npm/@fontsource/cooper-hewitt@5.2.5/files/cooper-hewitt-latin-700-normal.woff2') format('woff2');}
  @font-face{font-family:'Cooper Hewitt';font-style:normal;font-weight:800;src:url('https://cdn.jsdelivr.net/npm/@fontsource/cooper-hewitt@5.2.5/files/cooper-hewitt-latin-800-normal.woff2') format('woff2');}
</style>
<style>
  body,table,td,a{ -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
  table,td{ mso-table-lspace:0pt; mso-table-rspace:0pt; }
  img{ -ms-interpolation-mode:bicubic; border:0; outline:none; text-decoration:none; display:block; }
  body{ margin:0; padding:0; width:100%!important; background:#ffffff; }
  a{ text-decoration:none; }
  @media only screen and (max-width:640px){
    .container{ width:100%!important; }
    .px{ padding-left:24px!important; padding-right:24px!important; }
    .h1{ font-size:44px!important; line-height:1.04!important; }
    .h2{ font-size:34px!important; }
    .hbig{ font-size:32px!important; white-space:normal!important; }
  }
</style>
</head>
<body class="body" style="margin:0; padding:0; background:#ffffff;">
<div style="display:none; max-height:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:#ffffff; opacity:0;">
  [EMAIL_PREHEADER]&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
</div>
<center style="width:100%; background:#ffffff;">
<table role="presentation" class="container" width="640" align="center" border="0" cellpadding="0" cellspacing="0" style="width:640px; max-width:640px; margin:0 auto; background:#ffffff;">
  <!--KIT_HIDE--><tr>
    <td bgcolor="#ffffff" class="px" style="background:#ffffff; padding:9px 32px; text-align:center; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:12px; color:#6b7280;"><font color="#6b7280">[EMAIL_BAD_DISPLAY] <a href="[URL_PAGE]" style="color:#E30613; text-decoration:underline;"><font color="#E30613">[EMAIL_VIEW_ONLINE]</font></a></font></td>
  </tr><!--/KIT_HIDE-->
  <tr>
    <td bgcolor="#190507" class="px" style="background:#190507; padding:26px 32px 16px 32px;">
      <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="middle" style="vertical-align:middle;">
            <img src="https://donnez-votre-sang.ch/assets/hug-logo.png" width="206" height="50" alt="HUG &mdash; H&ocirc;pitaux Universitaires Gen&egrave;ve" style="display:inline-block; width:206px; height:50px; vertical-align:middle;">
          </td>
          <td valign="middle" align="right" style="vertical-align:middle; text-align:right;">
            <img src="https://donnez-votre-sang.ch/assets/logo.png" width="132" height="44" alt="Logo entreprise" style="display:inline-block; width:132px; height:44px;">
          </td>
        </tr>
      </table>
      <div style="text-align:center; padding-top:16px; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:700; letter-spacing:3px; color:#ffffff;"><font color="#ffffff">[EMAIL_DOSSIER_BADGE]</font></div>
    </td>
  </tr>
  <tr>
    <td bgcolor="#ffffff" class="px" style="background:#ffffff; padding:48px 32px 8px 32px;">
      <div style="display:inline-block;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border:2px solid #E30613;">
          <tr><td style="padding:11px 16px; font-family:'Cooper Hewitt','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:700; letter-spacing:3px; color:#E30613; white-space:nowrap;"><font color="#E30613">[EMAIL_MISSION_BADGE]</font></td></tr>
        </table>
      </div>
    </td>
  </tr>
  <tr>
    <td bgcolor="#ffffff" class="px" style="background:#ffffff; padding:22px 32px 8px 32px;">
      <!--[if mso]>
      <table role="presentation" width="576" border="0" cellpadding="0" cellspacing="0"><tr>
      <td width="300" valign="top" style="vertical-align:top;">
      <![endif]-->
      <div style="max-width:576px;">
        <!--[if !mso]><!-->
        <img src="https://donnez-votre-sang.ch/assets/loupe.png" width="248" height="234" align="right" alt="[EMAIL_ALT_LOUPE]" style="float:right; width:248px; height:234px; margin:6px 0 8px 18px;">
        <!--<![endif]-->
        <div class="h1" style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:52px; line-height:1.02; font-weight:800; color:#190507; letter-spacing:-1px;"><font color="#190507">[EMAIL_HERO_VERB]<br>[EMAIL_HERO_NOUN] <span style="color:#E30613; text-decoration:underline; text-underline-offset:6px;"><font color="#E30613">[EMAIL_HERO_ADJ]</font></span></font></div>
        <div style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:20px; line-height:1.5; font-weight:400; color:#190507; padding-top:30px;"><font color="#190507">
          [EMAIL_BODY1]<br>
          [EMAIL_BODY2]<br>
          [EMAIL_BODY3]<br>
          [EMAIL_BODY4]<br>
          <span style="color:#E30613; font-weight:700;"><font color="#E30613">[EMAIL_BODY5]</font></span>
        </font></div>
      </div>
      <!--[if mso]>
      </td>
      <td width="276" valign="top" style="vertical-align:top; text-align:right;">
      <img src="https://donnez-votre-sang.ch/assets/loupe.png" width="248" height="234" alt="Loupe" style="width:248px; height:234px;">
      </td>
      </tr></table>
      <![endif]-->
    </td>
  </tr>
  <tr>
    <td bgcolor="#ffffff" class="px" style="background:#ffffff; padding:24px 32px 52px 32px; text-align:center;">
      <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr><td bgcolor="#E30613" style="background:#E30613; border-radius:2px;">
          <a href="[URL_ELIGIBILITE]" style="display:inline-block; padding:13px 32px; font-family:'Cooper Hewitt','Helvetica Neue',Arial,sans-serif; font-size:14px; font-weight:600; letter-spacing:0.05em; color:#ffffff; text-decoration:none;"><font color="#ffffff">[EMAIL_CTA_ELIG]</font></a>
        </td></tr>
      </table>
    </td>
  </tr>
  <tr>
    <td bgcolor="#ffffff" class="px" style="background:#ffffff; padding:8px 32px 0 32px;">
      <div class="h2" style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:40px; line-height:1.08; font-weight:800; color:#190507; letter-spacing:-0.5px;"><font color="#190507">[EMAIL_COLLECTE1]<br>[EMAIL_COLLECTE2] <span style="color:#E30613;"><font color="#E30613">[EMAIL_CHEZ] [VOTRE ENTREPRISE].</font></span></font></div>
    </td>
  </tr>
  <tr>
    <td bgcolor="#ffffff" class="px" style="background:#ffffff; padding:40px 32px 0 32px;">
      <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top:1px solid #e5e7eb;">
        <tr>
          <td width="120" style="width:120px; padding:13px 0; vertical-align:middle; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:400; letter-spacing:0.5px; color:#6b7280;"><font color="#6b7280">[EMAIL_LABEL_DATE]</font></td>
          <td style="padding:13px 0; vertical-align:middle; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:16px; font-weight:700; color:#E30613;"><font color="#E30613">[JJ MOIS AAAA]</font></td>
        </tr>
        <tr>
          <td style="padding:13px 0; vertical-align:middle; border-top:1px solid #e5e7eb; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:400; letter-spacing:0.5px; color:#6b7280;"><font color="#6b7280">[EMAIL_LABEL_HORAIRES]</font></td>
          <td style="padding:13px 0; vertical-align:middle; border-top:1px solid #e5e7eb; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:16px; font-weight:700; color:#190507;"><font color="#190507">[HHhMM &ndash; HHhMM]</font></td>
        </tr>
        <tr>
          <td style="padding:13px 0; vertical-align:middle; border-top:1px solid #e5e7eb; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:400; letter-spacing:0.5px; color:#6b7280;"><font color="#6b7280">[EMAIL_LABEL_LIEU]</font></td>
          <td style="padding:13px 0; vertical-align:middle; border-top:1px solid #e5e7eb; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:16px; font-weight:700; color:#190507;"><font color="#190507">[VOTRE ENTREPRISE] &middot; [B&Acirc;TIMENT / SALLE]</font></td>
        </tr>
        <tr>
          <td style="padding:13px 0; vertical-align:middle; border-top:1px solid #e5e7eb; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:400; letter-spacing:0.5px; color:#6b7280;"><font color="#6b7280">[EMAIL_LABEL_ADRESSE]</font></td>
          <td style="padding:13px 0; vertical-align:middle; border-top:1px solid #e5e7eb; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:16px; font-weight:700; color:#190507;"><font color="#190507">[RUE ET NUM&Eacute;RO, VILLE]</font></td>
        </tr>
        <tr>
          <td style="padding:13px 0; vertical-align:middle; border-top:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:400; letter-spacing:0.5px; color:#6b7280;"><font color="#6b7280">[EMAIL_LABEL_STATUT]</font></td>
          <td style="padding:13px 0; vertical-align:middle; border-top:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:16px; font-weight:700; color:#E30613;"><font color="#E30613">[EMAIL_STATUT_VALUE]</font></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td bgcolor="#ffffff" class="px" style="background:#ffffff; padding:44px 32px 14px 32px; text-align:center;">
      <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr><td bgcolor="#E30613" style="background:#E30613; border-radius:2px;">
          <a href="[URL_ELIGIBILITE]" style="display:inline-block; padding:13px 32px; font-family:'Cooper Hewitt','Helvetica Neue',Arial,sans-serif; font-size:14px; font-weight:600; letter-spacing:0.05em; color:#ffffff; text-decoration:none;"><font color="#ffffff">[EMAIL_CTA_ELIG]</font></a>
        </td></tr>
      </table>
    </td>
  </tr>
  <tr>
    <td bgcolor="#ffffff" style="background:#ffffff; padding:0 32px 46px 32px; text-align:center;">
      <a href="[URL_RESERVATION_DON]" style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:400; color:#6b7280; text-decoration:underline;"><font color="#6b7280">[EMAIL_LINK_RDV]</font></a>
    </td>
  </tr>
  <tr>
    <td bgcolor="#E30613" class="px" style="background:#E30613; padding:20px 32px 8px 32px; text-align:center;">
      <div style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:700; letter-spacing:3px; color:#ffffff;"><font color="#ffffff">[EMAIL_INTERRO_BADGE]</font></div>
    </td>
  </tr>
  <tr>
    <td bgcolor="#E30613" class="px" style="background:#E30613; padding:6px 32px 0 32px;">
      <div style="text-align:right; margin-bottom:-6px;">
        <img src="https://donnez-votre-sang.ch/assets/chapeau.png" width="168" height="110" alt="[EMAIL_ALT_CHAPEAU]" style="display:inline-block; width:168px; height:110px;">
      </div>
      <div class="hbig" style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:42px; line-height:1.1; font-weight:800; color:#ffffff; letter-spacing:-0.5px; white-space:nowrap; text-align:center;"><font color="#ffffff">[EMAIL_ETES_VOUS]</font> <font color="#190507">[EMAIL_ELIGIBLE]</font></div>
    </td>
  </tr>
  <tr>
    <td bgcolor="#E30613" class="px" style="background:#E30613; padding:16px 32px 0 32px; text-align:center;">
      <div style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:18px; line-height:1.5; font-weight:400; color:#ffffff;"><font color="#ffffff">
        [EMAIL_QUIZ_DESC1]<br>[EMAIL_QUIZ_DESC2]
      </font></div>
    </td>
  </tr>
  <tr>
    <td bgcolor="#E30613" class="px" style="background:#E30613; padding:28px 32px 0 32px; text-align:center;">
      <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr><td bgcolor="#190507" style="background:#190507; border-radius:2px;">
          <a href="[URL_ELIGIBILITE]" style="display:inline-block; padding:13px 32px; font-family:'Cooper Hewitt','Helvetica Neue',Arial,sans-serif; font-size:14px; font-weight:600; letter-spacing:0.05em; color:#ffffff; text-decoration:none;"><font color="#ffffff">[EMAIL_CTA_ENQUETE]</font></a>
        </td></tr>
      </table>
    </td>
  </tr>
  <tr>
    <td bgcolor="#E30613" class="px" style="background:#E30613; padding:18px 32px 6px 32px; text-align:center;">
      <div style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:700; letter-spacing:2px; color:#190507;"><font color="#190507">[EMAIL_QUIZ_BADGE]</font></div>
    </td>
  </tr>
  <tr>
    <td bgcolor="#E30613" class="px" style="background:#E30613; padding:10px 32px 40px 32px; text-align:center;">
      <a href="[URL_RESERVATION_DON]" style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; font-weight:400; color:#190507; text-decoration:underline;"><font color="#190507">[EMAIL_LINK_RDV]</font></a>
    </td>
  </tr>
  <tr>
    <td bgcolor="#ffffff" class="px" style="background:#ffffff; padding:46px 32px 50px 32px; text-align:center; border-top:1px solid #e5e7eb;">
      <div style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:14px; font-weight:700; letter-spacing:0.5px; color:#190507;"><font color="#190507">HUG &times; [VOTRE ENTREPRISE] &middot; DOSSIER SANG-26</font></div>
      <div style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:14px; line-height:1.5; font-weight:600; color:#190507; padding-top:18px;"><font color="#190507">[EMAIL_FOOTER_ORG]<br>[EMAIL_FOOTER_HOSPITAL]</font></div>
      <div style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:13px; line-height:1.6; font-weight:400; color:#190507; padding-top:22px;"><font color="#190507">[EMAIL_FOOTER_LINKS] <a href="https://donnez-votre-sang.ch/" style="color:#E30613; text-decoration:underline;"><font color="#E30613">[EMAIL_FOOTER_LEARN]</font></a></font></div>
      <div style="font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:12px; line-height:1.7; font-weight:400; color:#9ca3af; padding-top:22px;"><font color="#9ca3af">[EMAIL_FOOTER_COPY]</font></div>
    </td>
  </tr>
</table>
</center>
</body>
</html>`;
