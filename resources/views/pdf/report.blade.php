<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: DejaVu Sans, Arial, sans-serif;
      font-size: 11px;
      color: #1a1a1a;
      background: #fff;
      padding: 32px 40px;
    }

    /* Header */
    .header {
      border-bottom: 3px solid #C41B1B;
      padding-bottom: 16px;
      margin-bottom: 24px;
    }
    .header-top {
      width: 100%;
    }
    .brand {
      font-size: 16px;
      font-weight: bold;
      color: #C41B1B;
    }
    .meta {
      font-size: 10px;
      color: #888;
      text-align: right;
    }
    h1 {
      font-size: 20px;
      font-weight: bold;
      color: #1a1a1a;
      margin-top: 8px;
    }
    .subtitle {
      font-size: 11px;
      color: #666;
      margin-top: 2px;
    }

    /* Sections */
    .section {
      margin-bottom: 20px;
      border: 1px solid #e5e5e5;
      border-radius: 6px;
      overflow: hidden;
    }
    .section-title {
      background: #f5f5f5;
      padding: 8px 14px;
      font-size: 11px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #555;
      border-bottom: 1px solid #e5e5e5;
    }
    .section-body {
      padding: 14px;
    }

    /* Info rows */
    .info-row {
      width: 100%;
      margin-bottom: 6px;
    }
    .info-label {
      color: #888;
      width: 40%;
      font-size: 10px;
      vertical-align: top;
      padding-right: 8px;
    }
    .info-value {
      font-weight: 600;
      color: #1a1a1a;
    }

    /* KPI grid */
    .kpi-table {
      width: 100%;
    }
    .kpi-cell {
      text-align: center;
      width: 25%;
      padding: 8px 4px;
    }
    .kpi-value {
      font-size: 22px;
      font-weight: bold;
      display: block;
    }
    .kpi-label {
      font-size: 9px;
      color: #888;
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }
    .kpi-started   { color: #7c3aed; }
    .kpi-completed { color: #d97706; }
    .kpi-eligible  { color: #059669; }
    .kpi-rdv       { color: #C41B1B; }

    /* Taux table */
    .rate-table { width: 100%; }
    .rate-row { margin-bottom: 10px; }
    .rate-label { color: #555; font-size: 10px; width: 60%; vertical-align: middle; }
    .rate-value { font-weight: bold; font-size: 11px; text-align: right; vertical-align: middle; }
    .bar-bg {
      background: #eee;
      border-radius: 3px;
      height: 5px;
      margin-top: 4px;
    }
    .bar-fill {
      background: #C41B1B;
      border-radius: 3px;
      height: 5px;
    }

    /* Footer */
    .footer {
      margin-top: 32px;
      border-top: 1px solid #e5e5e5;
      padding-top: 10px;
      font-size: 9px;
      color: #aaa;
    }
    .footer-table { width: 100%; }
  </style>
</head>
<body>

  {{-- Header --}}
  <div class="header">
    <table class="header-top" cellpadding="0" cellspacing="0">
      <tr>
        <td><span class="brand">♥ donnez-votre-sang.ch</span></td>
        <td class="meta">Généré le {{ $generatedAt }}</td>
      </tr>
    </table>
    <h1>Rapport de participation</h1>
    <div class="subtitle">{{ $entreprise['name'] }} — Campagne Don du Sang HUG × CTS</div>
  </div>

  {{-- Entreprise --}}
  <div class="section">
    <div class="section-title">Entreprise</div>
    <div class="section-body">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td class="info-label">Responsable</td>
          <td class="info-value">{{ $entreprise['contact_name'] ?? '—' }}</td>
        </tr>
        <tr>
          <td class="info-label">Email</td>
          <td class="info-value">{{ $entreprise['contact_email'] ?? '—' }}</td>
        </tr>
        <tr>
          <td class="info-label">Effectif</td>
          <td class="info-value">{{ $entreprise['employee_count'] ? number_format($entreprise['employee_count'], 0, '.', "'") . ' employés' : '—' }}</td>
        </tr>
      </table>
    </div>
  </div>

  {{-- KPIs --}}
  <div class="section">
    <div class="section-title">Participation</div>
    <div class="section-body">
      <table class="kpi-table" cellpadding="0" cellspacing="0">
        <tr>
          <td class="kpi-cell">
            <span class="kpi-value kpi-started">{{ number_format($participation['quiz_started'], 0, '.', "'") }}</span>
            <span class="kpi-label">Quiz démarrés</span>
          </td>
          <td class="kpi-cell">
            <span class="kpi-value kpi-completed">{{ number_format($participation['quiz_completed'], 0, '.', "'") }}</span>
            <span class="kpi-label">Quiz complétés</span>
          </td>
          <td class="kpi-cell">
            <span class="kpi-value kpi-eligible">{{ number_format($participation['eligible'], 0, '.', "'") }}</span>
            <span class="kpi-label">Éligibles</span>
          </td>
          <td class="kpi-cell">
            <span class="kpi-value kpi-rdv">{{ number_format($participation['rdv_clicked'], 0, '.', "'") }}</span>
            <span class="kpi-label">RDV cliqués</span>
          </td>
        </tr>
      </table>
    </div>
  </div>

  {{-- Taux --}}
  <div class="section">
    <div class="section-title">Taux clés</div>
    <div class="section-body">
      @php
        $rates = [
          ['Taux de participation',  $participation['quiz_started'],    $entreprise['employee_count']],
          ["Taux d'éligibilité",     $participation['eligible'],        $participation['total_submissions']],
          ['Taux de conversion',     $participation['rdv_clicked'],     $participation['eligible']],
        ];
      @endphp
      @foreach($rates as [$label, $a, $b])
        @php $pct = $b > 0 ? round($a / $b * 100, 1) : 0; @endphp
        <table cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px;">
          <tr>
            <td class="rate-label">{{ $label }}</td>
            <td class="rate-value">{{ $pct }}%</td>
          </tr>
          <tr>
            <td colspan="2">
              <div class="bar-bg">
                <div class="bar-fill" style="width:{{ min($pct, 100) }}%;"></div>
              </div>
            </td>
          </tr>
        </table>
      @endforeach
    </div>
  </div>

  {{-- Comportement --}}
  <div class="section">
    <div class="section-title">Comportement</div>
    <div class="section-body">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td class="info-label">Durée moyenne du quiz</td>
          <td class="info-value">{{ $behavior['avg_duration_s'] ? $behavior['avg_duration_s'] . ' s' : '—' }}</td>
        </tr>
      </table>
      @if(!empty($behavior['abandon_by_question']))
        <div style="margin-top:10px;">
          <div style="font-size:9px;color:#888;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:6px;">Abandons par question</div>
          <table cellpadding="0" cellspacing="4">
            @foreach($behavior['abandon_by_question'] as $q => $count)
              <tr>
                <td style="color:#888;font-size:10px;width:40px;">Q{{ (int)$q + 1 }}</td>
                <td style="font-size:10px;">{{ $count }} abandon{{ $count > 1 ? 's' : '' }}</td>
              </tr>
            @endforeach
          </table>
        </div>
      @endif
    </div>
  </div>

  {{-- Footer --}}
  <div class="footer">
    <table class="footer-table" cellpadding="0" cellspacing="0">
      <tr>
        <td>Fondation pour la Transfusion Sanguine — HUG × CTS Genève</td>
        <td style="text-align:right;">donnez-votre-sang.ch</td>
      </tr>
    </table>
  </div>

</body>
</html>
