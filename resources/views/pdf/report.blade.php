<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: DejaVu Sans, Arial, sans-serif;
      font-size: 11px;
      color: #111111;
      background: #F2F4F7;
    }

    /* ═══════════════════════════════════════════════════════
       HEADER — barre blanche logo
    ═══════════════════════════════════════════════════════ */
    .h-white {
      background: #FFFFFF;
      padding: 13px 40px;
      border-bottom: 3px solid #E30613;
    }
    .h-white table { width: 100%; border-collapse: collapse; }
    .h-meta {
      text-align: right;
      vertical-align: middle;
      font-size: 8px;
      color: #9CA3AF;
      letter-spacing: 0.6px;
    }

    /* ═══════════════════════════════════════════════════════
       HEADER — bande rouge
    ═══════════════════════════════════════════════════════ */
    .h-red {
      background: #E30613;
      padding: 24px 40px 28px;
    }
    .h-inner { width: 100%; border-collapse: collapse; }
    .h-content { vertical-align: top; }
    .h-logo-cell { width: 90px; vertical-align: top; text-align: right; padding-left: 20px; }

    .h-badge {
      display: inline-block;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.3);
      color: rgba(255,255,255,0.9);
      font-size: 7px;
      font-weight: bold;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      padding: 3px 10px;
      border-radius: 20px;
      margin-bottom: 11px;
    }
    .h-title {
      font-size: 25px;
      font-weight: bold;
      color: #FFFFFF;
      line-height: 1.15;
      letter-spacing: -0.3px;
      margin-bottom: 7px;
    }
    .h-company {
      font-size: 12px;
      color: rgba(255,255,255,0.8);
      font-weight: 600;
    }
    .h-sub {
      font-size: 10px;
      color: rgba(255,255,255,0.5);
      margin-top: 3px;
    }
    .h-logo-box {
      background: rgba(255,255,255,0.12);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 8px;
      padding: 10px;
      text-align: center;
    }

    /* ═══════════════════════════════════════════════════════
       CORPS
    ═══════════════════════════════════════════════════════ */
    .body { padding: 20px 32px 0; }

    /* ── Grille 2 colonnes ── */
    .row { width: 100%; border-collapse: separate; border-spacing: 0; }
    .col-l { width: 50%; vertical-align: top; padding-right: 8px; }
    .col-r { width: 50%; vertical-align: top; padding-left: 8px; }

    /* ═══════════════════════════════════════════════════════
       CARD
    ═══════════════════════════════════════════════════════ */
    .card {
      background: #FFFFFF;
      border: 1px solid #E5E7EB;
      border-radius: 10px;
      margin-bottom: 16px;
      overflow: hidden;
    }
    .card-head {
      padding: 9px 16px;
      border-bottom: 1px solid #F3F4F6;
      background: #FAFAFA;
    }
    .card-label {
      font-size: 7.5px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: #9CA3AF;
    }
    .card-body { padding: 16px 18px; }

    /* ── Variante accent gauche ── */
    .card-accent { border-left: 3px solid #E30613; }

    /* ═══════════════════════════════════════════════════════
       INFO ROWS (carte entreprise)
    ═══════════════════════════════════════════════════════ */
    .it { width: 100%; border-collapse: collapse; }
    .il {
      font-size: 7.5px;
      color: #9CA3AF;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: bold;
      width: 36%;
      padding: 7px 10px 7px 0;
      vertical-align: top;
      border-bottom: 1px solid #F3F4F6;
    }
    .iv {
      font-size: 11px;
      color: #111111;
      font-weight: 500;
      padding: 7px 0;
      vertical-align: top;
      border-bottom: 1px solid #F3F4F6;
    }

    /* ═══════════════════════════════════════════════════════
       KPI GRID 2×2
    ═══════════════════════════════════════════════════════ */
    .kt { width: 100%; border-collapse: collapse; }
    .kc {
      text-align: center;
      width: 50%;
      padding: 18px 6px;
      border-right: 1px solid #F3F4F6;
    }
    .kc-last { border-right: none; }
    .kc-top  { border-top: 1px solid #F3F4F6; }

    .kpi-badge {
      display: inline-block;
      width: 24px;
      height: 24px;
      border-radius: 6px;
      margin-bottom: 8px;
    }
    .kpi-badge-violet { background: rgba(124,58,237,0.12); }
    .kpi-badge-amber  { background: rgba(217,119,6,0.12);  }
    .kpi-badge-green  { background: rgba(5,150,105,0.12);  }
    .kpi-badge-brand  { background: rgba(227,6,19,0.12);   }

    .kn {
      font-size: 32px;
      font-weight: bold;
      display: block;
      line-height: 1;
      margin-bottom: 5px;
      letter-spacing: -1px;
    }
    .kt2 {
      font-size: 7px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: #9CA3AF;
      font-weight: bold;
    }

    .c-violet { color: #7C3AED; }
    .c-amber  { color: #D97706; }
    .c-green  { color: #059669; }
    .c-brand  { color: #E30613; }

    /* ═══════════════════════════════════════════════════════
       TAUX + BARRES
    ═══════════════════════════════════════════════════════ */
    .rt { width: 100%; border-collapse: collapse; }
    .rl { font-size: 10px; color: #6B7280; width: 64%; vertical-align: middle; padding-bottom: 2px; }
    .rv { font-size: 11px; font-weight: bold; color: #111111; text-align: right; vertical-align: middle; }
    .track { height: 4px; background: #F3F4F6; border-radius: 10px; margin: 4px 0 14px; }
    .fill  { height: 4px; background: #E30613; border-radius: 10px; }

    /* ═══════════════════════════════════════════════════════
       COMPORTEMENT
    ═══════════════════════════════════════════════════════ */
    .stat-box {
      background: #F9FAFB;
      border: 1px solid #F3F4F6;
      border-radius: 8px;
      padding: 12px 14px;
      margin-bottom: 14px;
    }
    .stat-label {
      font-size: 7.5px;
      color: #9CA3AF;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-weight: bold;
      margin-bottom: 4px;
    }
    .stat-value {
      font-size: 24px;
      font-weight: bold;
      color: #111111;
      letter-spacing: -0.5px;
    }
    .stat-unit {
      font-size: 11px;
      color: #6B7280;
      font-weight: normal;
    }

    .abandon-head {
      font-size: 7.5px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: #9CA3AF;
      font-weight: bold;
      margin-bottom: 8px;
    }
    .ab-row { margin-bottom: 4px; font-size: 10px; }
    .ab-q   { color: #E30613; font-weight: bold; font-size: 9px; display: inline-block; width: 28px; }
    .ab-n   { color: #374151; }

    /* ═══════════════════════════════════════════════════════
       FOOTER
    ═══════════════════════════════════════════════════════ */
    .footer {
      background: #111111;
      padding: 15px 40px;
      margin-top: 20px;
    }
    .ft  { width: 100%; border-collapse: collapse; }
    .fo  { font-size: 10px; color: #FFFFFF; font-weight: bold; }
    .fs  { font-size: 8.5px; color: #6B7280; margin-top: 3px; }
    .fr  { text-align: right; vertical-align: middle; }
    .flu { font-size: 10px; color: #E30613; font-weight: bold; }
    .fld { font-size: 8px; color: #6B7280; margin-top: 3px; }
  </style>
</head>
<body>

  {{-- ╔══════════════════════════════════════════════╗ --}}
  {{-- ║  HEADER BLANC — logo + métadonnée           ║ --}}
  {{-- ╚══════════════════════════════════════════════╝ --}}
  <div class="h-white">
    <table cellpadding="0" cellspacing="0">
      <tr>
        <td style="vertical-align:middle;">
          <img src="{{ $logoSrc }}" style="height:28px;width:auto;display:block;" alt="HUG">
        </td>
        <td class="h-meta">donnez-votre-sang.ch</td>
      </tr>
    </table>
  </div>

  {{-- ╔══════════════════════════════════════════════╗ --}}
  {{-- ║  HEADER ROUGE — titre                       ║ --}}
  {{-- ╚══════════════════════════════════════════════╝ --}}
  <div class="h-red">
    <table class="h-inner" cellpadding="0" cellspacing="0">
      <tr>
        <td class="h-content">
          <div class="h-badge">{{ $tr['generated_at'] }} {{ $generatedAt }}</div>
          <div class="h-title">{{ $tr['report_title'] }}</div>
          <div class="h-company">{{ $entreprise['name'] }}</div>
          <div class="h-sub">{{ $tr['report_subtitle'] }}</div>
        </td>
        @if(!empty($entreprise['logo_data_uri']))
        <td class="h-logo-cell">
          <div class="h-logo-box">
            <img src="{{ $entreprise['logo_data_uri'] }}" style="max-height:44px;max-width:72px;display:block;margin:0 auto;" alt="{{ $entreprise['name'] }}">
          </div>
        </td>
        @endif
      </tr>
    </table>
  </div>

  <div class="body">

    {{-- ╔══════════════════════════════════════════════╗ --}}
    {{-- ║  LIGNE 1 — Entreprise | KPIs               ║ --}}
    {{-- ╚══════════════════════════════════════════════╝ --}}
    <table class="row" cellpadding="0" cellspacing="0">
      <tr>

        {{-- Entreprise --}}
        <td class="col-l">
          <div class="card card-accent">
            <div class="card-head">
              <span class="card-label">{{ $tr['section_company'] }}</span>
            </div>
            <div class="card-body">
              <table class="it" cellpadding="0" cellspacing="0">
                @if($entreprise['contact_name'])
                <tr>
                  <td class="il">{{ $tr['contact_name'] }}</td>
                  <td class="iv">{{ $entreprise['contact_name'] }}</td>
                </tr>
                @endif
                @if($entreprise['contact_email'])
                <tr>
                  <td class="il">{{ $tr['contact_email'] }}</td>
                  <td class="iv">{{ $entreprise['contact_email'] }}</td>
                </tr>
                @endif
                @if($entreprise['employee_count'])
                <tr>
                  <td class="il">{{ $tr['employee_count'] }}</td>
                  <td class="iv">
                    {{ number_format($entreprise['employee_count'], 0, '.', "\u{202F}") }}
                    <span style="color:#9CA3AF;font-size:9px;">{{ $tr['employees'] }}</span>
                  </td>
                </tr>
                @endif
              </table>
            </div>
          </div>
        </td>

        {{-- KPIs participation --}}
        <td class="col-r">
          <div class="card">
            <div class="card-head">
              <span class="card-label">{{ $tr['section_participation'] }}</span>
            </div>
            <div class="card-body" style="padding:0;">
              <table class="kt" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="kc">
                    <span class="kn c-violet">{{ number_format($participation['quiz_started'], 0, '.', "\u{202F}") }}</span>
                    <span class="kt2">{{ $tr['quiz_started'] }}</span>
                  </td>
                  <td class="kc kc-last">
                    <span class="kn c-amber">{{ number_format($participation['quiz_completed'], 0, '.', "\u{202F}") }}</span>
                    <span class="kt2">{{ $tr['quiz_completed'] }}</span>
                  </td>
                </tr>
                <tr>
                  <td class="kc kc-top">
                    <span class="kn c-green">{{ number_format($participation['eligible'], 0, '.', "\u{202F}") }}</span>
                    <span class="kt2">{{ $tr['eligible'] }}</span>
                  </td>
                  <td class="kc kc-top kc-last">
                    <span class="kn c-brand">{{ number_format($participation['rdv_clicked'], 0, '.', "\u{202F}") }}</span>
                    <span class="kt2">{{ $tr['rdv_clicked'] }}</span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>

      </tr>
    </table>

    {{-- ╔══════════════════════════════════════════════╗ --}}
    {{-- ║  LIGNE 2 — Taux | Comportement             ║ --}}
    {{-- ╚══════════════════════════════════════════════╝ --}}
    <table class="row" cellpadding="0" cellspacing="0">
      <tr>

        {{-- Taux clés --}}
        <td class="col-l">
          <div class="card card-accent">
            <div class="card-head">
              <span class="card-label">{{ $tr['section_rates'] }}</span>
            </div>
            <div class="card-body">
              @php
                $rates = [
                  [$tr['participation_rate'], $participation['quiz_started'],  $entreprise['employee_count'] ?: 0],
                  [$tr['eligibility_rate'],   $participation['eligible'],      $participation['total_submissions']],
                  [$tr['conversion_rate'],    $participation['rdv_clicked'],   $participation['eligible']],
                ];
              @endphp
              @foreach($rates as [$label, $a, $b])
                @php $pct = $b > 0 ? round($a / $b * 100, 1) : 0; @endphp
                <table class="rt" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="rl">{{ $label }}</td>
                    <td class="rv">{{ $pct }}&thinsp;%</td>
                  </tr>
                </table>
                <div class="track"><div class="fill" style="width:{{ min($pct, 100) }}%;"></div></div>
              @endforeach
            </div>
          </div>
        </td>

        {{-- Comportement --}}
        <td class="col-r">
          <div class="card">
            <div class="card-head">
              <span class="card-label">{{ $tr['section_behavior'] }}</span>
            </div>
            <div class="card-body">

              <div class="stat-box">
                <div class="stat-label">{{ $tr['avg_duration'] }}</div>
                <div class="stat-value">
                  @if($behavior['avg_duration_s'])
                    {{ $behavior['avg_duration_s'] }}<span class="stat-unit">&thinsp;s</span>
                  @else
                    <span style="color:#D1D5DB;">—</span>
                  @endif
                </div>
              </div>

              @if(!empty($behavior['abandon_by_question']))
                <div class="abandon-head">{{ $tr['abandon_by_question'] }}</div>
                @foreach($behavior['abandon_by_question'] as $q => $count)
                  <div class="ab-row">
                    <span class="ab-q">Q{{ (int)$q + 1 }}</span>
                    <span class="ab-n">{{ $count }} {{ $count > 1 ? $tr['abandons'] : $tr['abandon'] }}</span>
                  </div>
                @endforeach
              @else
                <div style="font-size:10px;color:#D1D5DB;text-align:center;padding:16px 0;">—</div>
              @endif

            </div>
          </div>
        </td>

      </tr>
    </table>

  </div>

  {{-- ╔══════════════════════════════════════════════╗ --}}
  {{-- ║  FOOTER                                     ║ --}}
  {{-- ╚══════════════════════════════════════════════╝ --}}
  <div class="footer">
    <table class="ft" cellpadding="0" cellspacing="0">
      <tr>
        <td style="vertical-align:middle;">
          <div class="fo">{{ $tr['footer_org'] }}</div>
          <div class="fs">donnez-votre-sang.ch</div>
        </td>
        <td class="fr">
          <div class="flu">{{ $tr['generated_at'] }}</div>
          <div class="fld">{{ $generatedAt }}</div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
