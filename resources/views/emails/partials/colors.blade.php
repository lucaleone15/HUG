@php
/**
 * Couleurs email centralisées.
 * Les valeurs correspondent aux variables --color-email-* définies dans app.css.
 * Les clients mail ne supportant pas les variables CSS, on utilise des constantes PHP.
 */
$EC = [
    // Structure
    'brand'          => '#E30613',            // --color-email-brand
    'brand_dark'     => '#C5000E',            // --color-email-brand-dark
    'brand_border'   => '#A8000C',            // --color-email-brand-border
    'hero'           => '#0D0D0D',            // --color-email-hero
    'dark'           => '#111111',            // --color-email-dark
    'page_bg'        => '#F0EDE8',            // --color-email-bg
    'recap_bg'       => '#F0EBE0',            // --color-email-recap-bg
    'recap_border'   => '#C4B080',            // --color-email-recap-border

    // Typographie
    'white'          => '#ffffff',
    'card_bg'        => '#fafafa',
    'dark_border'    => '#2A2A2A',
    'recap_text'     => '#8A7A60',
    'recap_ink'      => '#1A1A1A',
    'label'          => '#aaaaaa',
    'body_text'      => '#555555',
    'text'           => '#333333',
    'step_text'      => '#666666',
    'muted'          => '#888888',
    'ink'            => '#111111',

    // Opacités sur fond sombre
    'on_dark_lo'     => 'rgba(255,255,255,0.35)',
    'on_dark_mid'    => 'rgba(255,255,255,0.4)',
    'on_dark_hi'     => 'rgba(255,255,255,0.65)',
    'on_dark_full'   => '#ffffff',
    'on_brand_lo'    => 'rgba(255,255,255,0.45)',
    'on_brand_mid'   => 'rgba(255,255,255,0.5)',
    'on_brand_hi'    => 'rgba(255,255,255,0.65)',
    'status_text'    => 'rgba(255,255,255,0.6)',
    'status_date'    => 'rgba(255,255,255,0.75)',
    'on_dark_sep'    => 'rgba(255,255,255,0.15)',
    'cross'          => 'rgba(255,255,255,0.35)',

    // Fond global email
    'body_font'      => "'Cooper Hewitt','Helvetica Neue',Helvetica,Arial,sans-serif",
];
@endphp
