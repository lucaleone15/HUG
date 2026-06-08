<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import NavBar from "../components/ui/NavBar.vue";
import Footer from "../components/ui/Footer.vue";
import BaseInput from "../components/ui/BaseInput.vue";
import BaseModal from "../components/ui/BaseModal.vue";

const { t } = useI18n();

// Email HTML template
// Image URLs in the template are used as replacement keys (see FIXED_IMAGES).
const EMAIL_TEMPLATE = `<!DOCTYPE html>
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
          <a href="[URL_ELIGIBILITE]" style="display:inline-block; padding:17px 30px; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:14px; font-weight:700; letter-spacing:1px; color:#ffffff; text-decoration:none;"><font color="#ffffff">[EMAIL_CTA_ELIG]</font></a>
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
          <a href="[URL_ELIGIBILITE]" style="display:inline-block; padding:17px 30px; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:14px; font-weight:700; letter-spacing:1px; color:#ffffff; text-decoration:none;"><font color="#ffffff">[EMAIL_CTA_ELIG]</font></a>
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
          <a href="[URL_ELIGIBILITE]" style="display:inline-block; padding:17px 30px; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:14px; font-weight:700; letter-spacing:1px; color:#ffffff; text-decoration:none;"><font color="#ffffff">[EMAIL_CTA_ENQUETE]</font></a>
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

// Original company logo <img> tag as it appears in the template (replacement key).
const LOGO_IMG_DEFAULT =
    '<img src="https://donnez-votre-sang.ch/assets/logo.png" width="132" height="44" alt="Logo entreprise" style="display:inline-block; width:132px; height:44px;">';

// Fixed campaign images: template URL → local public path + CID for .eml
const FIXED_IMAGES = [
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

// State
const form = reactive({
    entreprise: "",
    objet: "",
    date: "",
    horaires: "",
    salle: "",
    adresse: "",
    urlElig: "",
    urlResa: "",
});

const logoData = reactive({
    dataUri: "",
    base64: "",
    mime: "image/png",
    filename: "logo.png",
    width: 132,
    height: 44,
});

// base64-encoded campaign images loaded on mount (needed for .eml CID parts)
const imagesB64 = reactive({ huglogo: "", loupe: "", chapeau: "" });

const errors = reactive({});
const logoInputEl = ref(null);
const clipHolder = ref(null);
const modalGmail = ref(false);
const modalOutlook = ref(false);
const gmailCopyOk = ref(false);

// Image preload on mount ─────────────────────────────────────────────────
async function urlToBase64(url) {
    const res = await fetch(url);
    const buf = await res.arrayBuffer();
    const bytes = new Uint8Array(buf);
    let bin = "";
    for (let i = 0; i < bytes.length; i++) bin += String.fromCharCode(bytes[i]);
    return btoa(bin);
}

onMounted(async () => {
    form.objet = t("kit.gen_ph_objet");
    for (const img of FIXED_IMAGES) {
        try {
            imagesB64[img.cid] = await urlToBase64(img.localUrl);
        } catch (_) {
            // fallback: preview will use local URL, .eml will have empty part
        }
    }
});

// Utilities
function esc(s) {
    return String(s)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}
function escAttr(s) {
    return String(s)
        .replace(/&/g, "&amp;")
        .replace(/"/g, "&quot;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}
function repAll(str, find, val) {
    return str.split(find).join(val);
}

function fitLogo(natW, natH) {
    const MAX_W = 200,
        MAX_H = 50;
    if (!natW || !natH) return { w: 132, h: 44 };
    const ratio = Math.min(MAX_W / natW, MAX_H / natH);
    return {
        w: Math.max(1, Math.round(natW * ratio)),
        h: Math.max(1, Math.round(natH * ratio)),
    };
}

function b64utf8(str) {
    return btoa(unescape(encodeURIComponent(str)));
}
function wrap76(b64) {
    return b64.match(/.{1,76}/g).join("\r\n");
}

function imgPart(boundary, cid, filename, mime, b64) {
    return [
        "--" + boundary,
        `Content-Type: ${mime}; name="${filename}"`,
        "Content-Transfer-Encoding: base64",
        `Content-ID: <${cid}>`,
        `Content-Disposition: inline; filename="${filename}"`,
        "",
        b64 ? wrap76(b64) : "",
    ];
}

function download(content, filename, mime) {
    const blob = new Blob([content], { type: mime });
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    setTimeout(() => {
        document.body.removeChild(a);
        URL.revokeObjectURL(a.href);
    }, 100);
}

function slug() {
    const s = (form.entreprise || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[̀-ͯ]/g, "")
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "");
    return s || "email-sang26";
}

// Build HTML
function buildHtml(mode = "web") {
    const entreprise = esc(form.entreprise);
    const date = esc(form.date);
    const horaires = esc(form.horaires);
    const salle = form.salle; // not escaped: may contain intentional HTML entities
    const adresse = esc(form.adresse);
    const urlElig = escAttr(form.urlElig.trim());
    const urlResa = escAttr(form.urlResa.trim());
    const urlPage = urlElig;

    let html = EMAIL_TEMPLATE;
    html = repAll(
        html,
        "[VOTRE ENTREPRISE]",
        entreprise || t("kit.email_ph_company"),
    );
    html = repAll(html, "[JJ MOIS AAAA]", date || t("kit.email_ph_date"));
    html = repAll(
        html,
        "[HHhMM &ndash; HHhMM]",
        horaires || t("kit.email_ph_horaires"),
    );
    html = repAll(
        html,
        "[B&Acirc;TIMENT / SALLE]",
        salle || t("kit.email_ph_salle"),
    );
    html = repAll(
        html,
        "[RUE ET NUM&Eacute;RO, VILLE]",
        adresse || t("kit.email_ph_adresse"),
    );
    html = repAll(html, "[URL_ELIGIBILITE]", urlElig || "#");
    html = repAll(html, "[URL_RESERVATION_DON]", urlResa || "#");
    html = repAll(html, "[URL_PAGE]", urlPage || "#");

    // Email content translations
    for (const [ph, key] of [
        ["[EMAIL_PREHEADER]", "email_preheader"],
        ["[EMAIL_BAD_DISPLAY]", "email_bad_display"],
        ["[EMAIL_VIEW_ONLINE]", "email_view_online"],
        ["[EMAIL_DOSSIER_BADGE]", "email_dossier_badge"],
        ["[EMAIL_MISSION_BADGE]", "email_mission_badge"],
        ["[EMAIL_ALT_LOUPE]", "email_alt_loupe"],
        ["[EMAIL_HERO_VERB]", "email_hero_verb"],
        ["[EMAIL_HERO_NOUN]", "email_hero_noun"],
        ["[EMAIL_HERO_ADJ]", "email_hero_adj"],
        ["[EMAIL_BODY1]", "email_body1"],
        ["[EMAIL_BODY2]", "email_body2"],
        ["[EMAIL_BODY3]", "email_body3"],
        ["[EMAIL_BODY4]", "email_body4"],
        ["[EMAIL_BODY5]", "email_body5"],
        ["[EMAIL_CTA_ELIG]", "email_cta_elig"],
        ["[EMAIL_COLLECTE1]", "email_collecte1"],
        ["[EMAIL_COLLECTE2]", "email_collecte2"],
        ["[EMAIL_CHEZ]", "email_chez"],
        ["[EMAIL_LABEL_DATE]", "email_label_date"],
        ["[EMAIL_LABEL_HORAIRES]", "email_label_horaires"],
        ["[EMAIL_LABEL_LIEU]", "email_label_lieu"],
        ["[EMAIL_LABEL_ADRESSE]", "email_label_adresse"],
        ["[EMAIL_LABEL_STATUT]", "email_label_statut"],
        ["[EMAIL_STATUT_VALUE]", "email_statut_value"],
        ["[EMAIL_LINK_RDV]", "email_link_rdv"],
        ["[EMAIL_INTERRO_BADGE]", "email_interro_badge"],
        ["[EMAIL_ALT_CHAPEAU]", "email_alt_chapeau"],
        ["[EMAIL_ETES_VOUS]", "email_etes_vous"],
        ["[EMAIL_ELIGIBLE]", "email_eligible"],
        ["[EMAIL_QUIZ_DESC1]", "email_quiz_desc1"],
        ["[EMAIL_QUIZ_DESC2]", "email_quiz_desc2"],
        ["[EMAIL_CTA_ENQUETE]", "email_cta_enquete"],
        ["[EMAIL_QUIZ_BADGE]", "email_quiz_badge"],
        ["[EMAIL_FOOTER_ORG]", "email_footer_org"],
        ["[EMAIL_FOOTER_HOSPITAL]", "email_footer_hospital"],
        ["[EMAIL_FOOTER_LINKS]", "email_footer_links"],
        ["[EMAIL_FOOTER_LEARN]", "email_footer_learn"],
        ["[EMAIL_FOOTER_COPY]", "email_footer_copy"],
    ]) {
        html = repAll(html, ph, t("kit." + key));
    }

    // Replace fixed image URLs
    for (const img of FIXED_IMAGES) {
        let src;
        if (mode === "eml") {
            src = "cid:" + img.cid;
        } else if (imagesB64[img.cid]) {
            src = "data:image/png;base64," + imagesB64[img.cid];
        } else {
            src = img.localUrl; // fallback while images are loading
        }
        html = repAll(html, img.url, src);
    }

    // Replace company logo
    if (logoData.dataUri) {
        const src = mode === "eml" ? "cid:companylogo" : logoData.dataUri;
        const { width: w, height: h } = logoData;
        const newImg = `<img src="${src}" width="${w}" height="${h}" alt="Logo entreprise" style="display:inline-block; width:${w}px; height:${h}px;">`;
        html = repAll(html, LOGO_IMG_DEFAULT, newImg);
    } else {
        const placeholder = `<span style="display:inline-block; border:1px dashed rgba(255,255,255,0.3); padding:0 14px; font-family:'Cooper Hewitt','Archivo','Helvetica Neue',Arial,sans-serif; font-size:10px; font-weight:700; letter-spacing:2px; color:rgba(255,255,255,0.35); height:44px; line-height:44px; vertical-align:middle; border-radius:2px;">LOGO</span>`;
        html = repAll(html, LOGO_IMG_DEFAULT, placeholder);
    }

    return html;
}

const previewHtml = computed(() => buildHtml("web"));

const gmailSteps = computed(() => [
    t("kit.gen_step_gmail_1"),
    t("kit.gen_step_gmail_2"),
    t("kit.gen_step_gmail_3"),
    t("kit.gen_step_gmail_4"),
]);

const outlookSteps = computed(() => [
    t("kit.gen_step_outlook_1"),
    t("kit.gen_step_outlook_2"),
    t("kit.gen_step_outlook_3"),
    t("kit.gen_step_outlook_4"),
]);

// Validation
function validate() {
    const required = [
        "entreprise",
        "objet",
        "date",
        "horaires",
        "salle",
        "adresse",
        "urlElig",
        "urlResa",
    ];
    let valid = true;
    for (const key of required) {
        if (!form[key]?.trim()) {
            errors[key] = t("kit.gen_field_required");
            valid = false;
        } else {
            delete errors[key];
        }
    }
    if (!logoData.dataUri) {
        errors.logo = t("kit.gen_logo_error");
        valid = false;
    } else {
        delete errors.logo;
    }
    if (!valid) {
        // Scroll to first invalid field
        const firstErr = document.querySelector(
            ".input-error, .gen-file-error",
        );
        firstErr
            ?.closest("label")
            ?.scrollIntoView({ behavior: "smooth", block: "center" });
    }
    return valid;
}

function onLogoChange(event) {
    const file = event.target.files?.[0];
    if (!file) {
        logoData.dataUri = "";
        logoData.base64 = "";
        delete errors.logo;
        return;
    }
    const reader = new FileReader();
    reader.onload = () => {
        const dataUri = String(reader.result);
        logoData.dataUri = dataUri;
        logoData.mime = file.type || "image/png";
        logoData.base64 = dataUri.split(",")[1] || "";
        logoData.filename = file.name || "logo.png";
        delete errors.logo;

        const probe = new Image();
        probe.onload = () => {
            // SVG without explicit width/height returns naturalWidth=0; default to 300×100
            const dim = fitLogo(
                probe.naturalWidth || 300,
                probe.naturalHeight || 100,
            );
            logoData.width = dim.w;
            logoData.height = dim.h;
        };
        probe.onerror = () => {
            logoData.width = 132;
            logoData.height = 44;
        };
        probe.src = dataUri;
    };
    reader.readAsDataURL(file);
}

// Webmail copy
function copyRich() {
    const full = buildHtml("web");
    const doc = new DOMParser().parseFromString(full, "text/html");
    const bodyHtml = doc.body.innerHTML;
    const holder = clipHolder.value;
    holder.innerHTML = bodyHtml;

    const sel = window.getSelection();
    const range = document.createRange();
    range.selectNodeContents(holder);
    sel.removeAllRanges();
    sel.addRange(range);

    let ok = false;
    try {
        ok = document.execCommand("copy");
    } catch (_) {}
    sel.removeAllRanges();

    if (navigator.clipboard && window.ClipboardItem) {
        try {
            const item = new ClipboardItem({
                "text/html": new Blob([bodyHtml], { type: "text/html" }),
                "text/plain": new Blob([holder.innerText || ""], {
                    type: "text/plain",
                }),
            });
            navigator.clipboard.write([item]).then(() => {
                ok = true;
            });
        } catch (_) {}
    }
    return ok;
}

function goGmail() {
    if (!validate()) return;
    const ok = copyRich();
    gmailCopyOk.value = ok;
    modalGmail.value = true;
}

function recopy() {
    gmailCopyOk.value = copyRich();
}

// Outlook .eml
function buildEml() {
    const subject = form.objet || "Dossier #SANG-26";
    const encSubject = "=?UTF-8?B?" + b64utf8(subject) + "?=";
    const boundary = "=_SANG26_related_=";
    const fname = (logoData.filename || "logo.png").replace(/["\r\n]/g, "");

    const lines = [
        "X-Unsent: 1",
        "To: ",
        "Subject: " + encSubject,
        "From: HUG - Don du sang <noreply@donnez-votre-sang.ch>",
        "MIME-Version: 1.0",
        `Content-Type: multipart/related; boundary="${boundary}"`,
        "",
        "--" + boundary,
        'Content-Type: text/html; charset="utf-8"',
        "Content-Transfer-Encoding: base64",
        "",
        wrap76(b64utf8(buildHtml("eml"))),
        ...imgPart(
            boundary,
            "companylogo",
            fname,
            logoData.mime || "image/png",
            logoData.base64,
        ),
        ...FIXED_IMAGES.flatMap((img) =>
            imgPart(
                boundary,
                img.cid,
                img.cid + ".png",
                "image/png",
                imagesB64[img.cid] || "",
            ),
        ),
        "--" + boundary + "--",
        "",
    ];
    return lines.join("\r\n");
}

function goOutlook() {
    if (!validate()) return;
    download(buildEml(), slug() + "-sang26.eml", "message/rfc822");
    modalOutlook.value = true;
}
</script>

<template>
    <div class="min-h-screen bg-base-200 flex flex-col">
        <NavBar />

        <!-- Header band -->
        <div class="bg-site-ink text-white px-6 py-4">
            <div class="max-w-[1400px] mx-auto">
                <h1 class="font-bold text-base leading-tight tracking-wide">
                    {{ t("kit.gen_title") }}
                </h1>
            </div>
        </div>

        <!-- Main layout: form (left) + live preview (right) -->
        <div
            class="flex-1 flex flex-wrap gap-6 p-6 max-w-[1400px] mx-auto w-full items-start"
        >
            <!-- Form panel -->
            <div
                class="bg-white rounded-xl shadow-sm flex-none w-full sm:w-80 lg:w-96 self-start"
            >
                <div class="px-6 py-4 border-b border-base-100">
                    <h2
                        class="text-[10px] font-bold uppercase tracking-[0.2em] text-base-content/35"
                    >
                        {{ t("kit.gen_section_info") }}
                    </h2>
                </div>
                <div class="px-6 pb-6 pt-2 space-y-1">
                    <BaseInput
                        v-model="form.entreprise"
                        :label="t('kit.gen_label_entreprise')"
                        :error="errors.entreprise"
                        :placeholder="t('kit.gen_ph_entreprise')"
                        required
                        @update:model-value="delete errors.entreprise"
                    />
                    <BaseInput
                        v-model="form.objet"
                        :label="t('kit.gen_label_objet')"
                        :error="errors.objet"
                        :placeholder="t('kit.gen_ph_objet')"
                        required
                        @update:model-value="delete errors.objet"
                    />
                    <BaseInput
                        v-model="form.date"
                        :label="t('kit.gen_label_date')"
                        :error="errors.date"
                        :placeholder="t('kit.gen_ph_date')"
                        required
                        @update:model-value="delete errors.date"
                    />
                    <BaseInput
                        v-model="form.horaires"
                        :label="t('kit.gen_label_horaires')"
                        :error="errors.horaires"
                        :placeholder="t('kit.gen_ph_horaires')"
                        required
                        @update:model-value="delete errors.horaires"
                    />
                    <BaseInput
                        v-model="form.salle"
                        :label="t('kit.gen_label_lieu')"
                        :error="errors.salle"
                        :placeholder="t('kit.gen_ph_lieu')"
                        required
                        @update:model-value="delete errors.salle"
                    />
                    <BaseInput
                        v-model="form.adresse"
                        :label="t('kit.gen_label_adresse')"
                        :error="errors.adresse"
                        :placeholder="t('kit.gen_ph_adresse')"
                        required
                        @update:model-value="delete errors.adresse"
                    />

                    <!-- Logo upload (file input — BaseInput doesn't support type=file) -->
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-medium">
                                {{ t("kit.gen_label_logo")
                                }}<span class="text-error ml-0.5">*</span>
                            </span>
                        </div>
                        <input
                            ref="logoInputEl"
                            type="file"
                            accept="image/png,image/jpeg,image/gif,image/svg+xml"
                            class="input input-bordered w-full py-2 cursor-pointer file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-base-200 file:text-base-content/70 hover:file:bg-base-300"
                            :class="{
                                'input-error gen-file-error': errors.logo,
                            }"
                            @change="onLogoChange"
                        />
                        <div class="label pt-0">
                            <span
                                v-if="errors.logo"
                                class="label-text-alt text-error"
                                >{{ errors.logo }}</span
                            >
                            <span
                                v-else
                                class="label-text-alt text-base-content/50"
                                >{{ t("kit.gen_logo_hint") }}</span
                            >
                        </div>
                    </label>

                    <!-- Links -->
                    <div class="pt-2 border-t border-base-100">
                        <h2
                            class="text-[10px] font-bold uppercase tracking-[0.2em] text-base-content/35 mb-1 px-1"
                        >
                            {{ t("kit.gen_section_links") }}
                        </h2>
                        <BaseInput
                            v-model="form.urlElig"
                            :label="t('kit.gen_label_url_elig')"
                            :error="errors.urlElig"
                            :placeholder="t('kit.gen_ph_url_elig')"
                            required
                            @update:model-value="delete errors.urlElig"
                        />
                        <BaseInput
                            v-model="form.urlResa"
                            :label="t('kit.gen_label_url_resa')"
                            :error="errors.urlResa"
                            :placeholder="t('kit.gen_ph_url_resa')"
                            required
                            @update:model-value="delete errors.urlResa"
                        />
                    </div>

                    <!-- Send buttons -->
                    <div class="pt-3 border-t border-base-100">
                        <p
                            class="text-xs font-semibold text-base-content/60 mb-3"
                        >
                            {{ t("kit.gen_send_question") }}
                        </p>
                        <div class="flex gap-2">
                            <button
                                class="btn bg-brand hover:bg-brand-dark text-white border-none flex-1 flex-col gap-0 h-auto py-3"
                                @click="goOutlook"
                            >
                                <span class="text-xs font-bold leading-tight">{{
                                    t("kit.gen_btn_desktop")
                                }}</span>
                                <span
                                    class="text-[10px] opacity-70 font-normal"
                                    >{{ t("kit.gen_btn_desktop_ext") }}</span
                                >
                            </button>
                            <button
                                class="btn bg-site-ink hover:bg-site-ink/80 text-white border-none flex-1 flex-col gap-0 h-auto py-3"
                                @click="goGmail"
                            >
                                <span class="text-xs font-bold leading-tight">{{
                                    t("kit.gen_btn_webmail")
                                }}</span>
                                <span
                                    class="text-[10px] opacity-70 font-normal"
                                    >{{ t("kit.gen_btn_webmail_ext") }}</span
                                >
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview panel -->
            <div
                class="bg-white rounded-xl shadow-sm flex-1 min-w-0 overflow-hidden sticky top-6"
                style="min-width: min(100%, 560px)"
            >
                <div class="px-5 py-3 border-b border-base-100">
                    <h2
                        class="text-[10px] font-bold uppercase tracking-[0.2em] text-base-content/35"
                    >
                        {{ t("kit.gen_preview_title") }}
                    </h2>
                </div>
                <iframe
                    :srcdoc="previewHtml"
                    class="w-full block border-0 bg-base-200"
                    style="height: 760px"
                    :title="t('kit.gen_preview_title')"
                ></iframe>
            </div>
        </div>

        <!-- Modal Gmail -->
        <BaseModal v-model="modalGmail" :title="t('kit.gen_modal_gmail_title')">
            <div
                v-if="gmailCopyOk"
                class="bg-base-200 border-l-2 border-brand text-sm px-3 py-2 rounded mb-4 font-medium"
            >
                {{ t("kit.gen_modal_gmail_copy_ok") }}
            </div>
            <ol class="space-y-0 divide-y divide-base-100">
                <li
                    v-for="(step, i) in gmailSteps"
                    :key="i"
                    class="flex gap-3 py-2.5 text-sm"
                >
                    <span
                        class="shrink-0 w-6 h-6 rounded-full bg-site-ink text-white text-xs font-bold flex items-center justify-center mt-0.5"
                        >{{ i + 1 }}</span
                    >
                    <!-- eslint-disable-next-line vue/no-v-html -->
                    <span v-html="step"></span>
                </li>
            </ol>
            <!-- eslint-disable-next-line vue/no-v-html -->
            <p
                class="bg-base-200 border border-base-300 text-base-content/55 text-xs px-3 py-2 rounded mt-4 leading-relaxed"
                v-html="
                    t('kit.gen_modal_gmail_note', {
                        btn: t('kit.gen_modal_recopy'),
                    })
                "
            ></p>
            <template #footer>
                <button class="btn btn-sm" @click="recopy">
                    {{ t("kit.gen_modal_recopy") }}
                </button>
            </template>
        </BaseModal>

        <!-- Modal Outlook -->
        <BaseModal
            v-model="modalOutlook"
            :title="t('kit.gen_modal_outlook_title')"
        >
            <ol class="space-y-0 divide-y divide-base-100">
                <li
                    v-for="(step, i) in outlookSteps"
                    :key="i"
                    class="flex gap-3 py-2.5 text-sm"
                >
                    <span
                        class="shrink-0 w-6 h-6 rounded-full bg-brand text-white text-xs font-bold flex items-center justify-center mt-0.5"
                        >{{ i + 1 }}</span
                    >
                    <!-- eslint-disable-next-line vue/no-v-html -->
                    <span v-html="step"></span>
                </li>
            </ol>
            <template #footer>
                <button class="btn btn-sm" @click="goOutlook">
                    {{ t("kit.gen_modal_outlook_redownload") }}
                </button>
            </template>
        </BaseModal>

        <!-- Off-screen holder for clipboard rich-text copy -->
        <div
            ref="clipHolder"
            class="fixed"
            style="left: -99999px; top: 0; width: 640px; pointer-events: none"
            aria-hidden="true"
        ></div>

        <Footer />
    </div>
</template>

<style scoped>
:deep(.kbd-key) {
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-bottom-width: 2px;
    border-radius: 4px;
    padding: 1px 5px;
    font-size: 0.7rem;
}
</style>
