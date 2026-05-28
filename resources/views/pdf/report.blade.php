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
      background: #ffffff;
    }

    /* ── Header blanc logo ──────────────────────────────── */
    .hw { background: #ffffff; padding: 18px 44px 16px; border-bottom: 2px solid #E30613; }
    .hw table { width: 100%; }
    .hw-meta { text-align: right; vertical-align: middle; font-size: 9px; color: #aaaaaa; }

    /* ── Bande rouge titre ──────────────────────────────── */
    .hr { background: #E30613; padding: 28px 44px 32px; }
    .hr-eye {
      font-size: 8px; color: rgba(255,255,255,0.5);
      text-transform: uppercase; letter-spacing: 2.5px; font-weight: bold; margin-bottom: 8px;
    }
    .hr-h1 { font-size: 28px; font-weight: bold; color: #ffffff; line-height: 1.15; }
    .hr-sub { font-size: 11px; color: rgba(255,255,255,0.65); margin-top: 6px; }

    /* ── Corps ──────────────────────────────────────────── */
    .body { padding: 28px 44px 0; }

    /* ── Grid 2 colonnes ────────────────────────────────── */
    .grid { width: 100%; border-collapse: separate; border-spacing: 0; }
    .col  { width: 50%; vertical-align: top; }
    .col-l { padding-right: 9px; }
    .col-r { padding-left: 9px; }

    /* ── Section card ───────────────────────────────────── */
    .card { border: 1px solid #e4e4e4; margin-bottom: 18px; }
    .card-head {
      background: #f6f6f6;
      border-bottom: 1px solid #e4e4e4;
      padding: 8px 16px;
    }
    .card-label {
      font-size: 8px; font-weight: bold;
      text-transform: uppercase; letter-spacing: 2px; color: #666666;
    }
    .card-body { padding: 20px 20px; background: #ffffff; }

    /* ── Info rows ──────────────────────────────────────── */
    .it { width: 100%; border-collapse: collapse; }
    .il {
      font-size: 8.5px; color: #999999;
      text-transform: uppercase; letter-spacing: 0.8px; font-weight: bold;
      width: 38%; padding: 7px 10px 7px 0;
      vertical-align: top; border-bottom: 1px solid #f0f0f0;
    }
    .iv {
      font-size: 11px; color: #111111;
      padding: 7px 0; vertical-align: top; border-bottom: 1px solid #f0f0f0;
    }

    /* ── KPIs 2×2 ───────────────────────────────────────── */
    .kt { width: 100%; border-collapse: collapse; }
    .kc {
      text-align: center; width: 50%;
      padding: 24px 8px;
      border-right: 1px solid #f0f0f0;
    }
    .kc-last { border-right: none; }
    .kc-top  { border-bottom: 1px solid #f0f0f0; }
    .kn { font-size: 40px; font-weight: bold; display: block; line-height: 1; margin-bottom: 6px; }
    .kt2 { font-size: 7.5px; text-transform: uppercase; letter-spacing: 1.5px; color: #999999; font-weight: bold; }

    .c-vi { color: #7c3aed; }
    .c-am { color: #d97706; }
    .c-gr { color: #059669; }
    .c-re { color: #E30613; }

    /* ── Taux ───────────────────────────────────────────── */
    .rl { font-size: 10px; color: #555555; width: 65%; vertical-align: middle; padding-bottom: 3px; }
    .rv { font-size: 11px; font-weight: bold; color: #111111; text-align: right; vertical-align: middle; }
    .bw { height: 5px; background: #eeeeee; margin: 4px 0 18px; }
    .bf { height: 5px; background: #E30613; }

    /* ── Footer ─────────────────────────────────────────── */
    .foot { background: #111111; padding: 18px 44px; margin-top: 28px; }
    .ft { width: 100%; }
    .fo { font-size: 10px; color: #cccccc; font-weight: bold; }
    .fs { font-size: 9px; color: #666666; margin-top: 3px; }
    .fr { text-align: right; vertical-align: middle; }
    .fu { font-size: 10px; color: #E30613; font-weight: bold; }
    .fd { font-size: 8.5px; color: #555555; margin-top: 3px; }
  </style>
</head>
<body>

  {{-- Header blanc logo --}}
  <div class="hw">
    <table cellpadding="0" cellspacing="0">
      <tr>
        <td style="vertical-align:middle;">
          <img src="{{ $logoSrc }}" style="height:32px;width:auto;display:block;" alt="HUG">
        </td>
        <td class="hw-meta">donnez-votre-sang.ch</td>
      </tr>
    </table>
  </div>

  {{-- Bande rouge titre --}}
  <div class="hr">
    <div class="hr-eye">{{ $tr['generated_at'] }} {{ $generatedAt }}</div>
    <div class="hr-h1">{{ $tr['report_title'] }}</div>
    <div class="hr-sub">{{ $entreprise['name'] }}&ensp;—&ensp;{{ $tr['report_subtitle'] }}</div>
  </div>

  <div class="body">

    {{-- Ligne 1 : Entreprise | KPIs --}}
    <table class="grid" cellpadding="0" cellspacing="0">
      <tr>
        <td class="col col-l">
          <div class="card">
            <div class="card-head"><span class="card-label">{{ $tr['section_company'] }}</span></div>
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
                  <td class="iv">{{ number_format($entreprise['employee_count'], 0, '.', "\u{202F}") }} {{ $tr['employees'] }}</td>
                </tr>
                @endif
              </table>
            </div>
          </div>
        </td>

        <td class="col col-r">
          <div class="card">
            <div class="card-head"><span class="card-label">{{ $tr['section_participation'] }}</span></div>
            <div class="card-body" style="padding:0;">
              <table class="kt" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="kc kc-top">
                    <span class="kn c-vi">{{ number_format($participation['quiz_started'], 0, '.', "\u{202F}") }}</span>
                    <span class="kt2">{{ $tr['quiz_started'] }}</span>
                  </td>
                  <td class="kc kc-top kc-last">
                    <span class="kn c-am">{{ number_format($participation['quiz_completed'], 0, '.', "\u{202F}") }}</span>
                    <span class="kt2">{{ $tr['quiz_completed'] }}</span>
                  </td>
                </tr>
                <tr>
                  <td class="kc">
                    <span class="kn c-gr">{{ number_format($participation['eligible'], 0, '.', "\u{202F}") }}</span>
                    <span class="kt2">{{ $tr['eligible'] }}</span>
                  </td>
                  <td class="kc kc-last">
                    <span class="kn c-re">{{ number_format($participation['rdv_clicked'], 0, '.', "\u{202F}") }}</span>
                    <span class="kt2">{{ $tr['rdv_clicked'] }}</span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>
      </tr>
    </table>

    {{-- Ligne 2 : Taux | Comportement --}}
    <table class="grid" cellpadding="0" cellspacing="0">
      <tr>
        <td class="col col-l">
          <div class="card">
            <div class="card-head"><span class="card-label">{{ $tr['section_rates'] }}</span></div>
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
                <table cellpadding="0" cellspacing="0" style="width:100%;">
                  <tr>
                    <td class="rl">{{ $label }}</td>
                    <td class="rv">{{ $pct }}&thinsp;%</td>
                  </tr>
                </table>
                <div class="bw"><div class="bf" style="width:{{ min($pct, 100) }}%;"></div></div>
              @endforeach
            </div>
          </div>
        </td>

        <td class="col col-r">
          <div class="card">
            <div class="card-head"><span class="card-label">{{ $tr['section_behavior'] }}</span></div>
            <div class="card-body">
              <table class="it" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="il">{{ $tr['avg_duration'] }}</td>
                  <td class="iv">{{ $behavior['avg_duration_s'] ? $behavior['avg_duration_s'] . ' s' : '—' }}</td>
                </tr>
              </table>
              @if(!empty($behavior['abandon_by_question']))
                <div style="font-size:7.5px;text-transform:uppercase;letter-spacing:1.5px;color:#aaaaaa;font-weight:bold;margin:16px 0 8px;">
                  {{ $tr['abandon_by_question'] }}
                </div>
                @foreach($behavior['abandon_by_question'] as $q => $count)
                  <div style="margin-bottom:5px;font-size:10px;">
                    <span style="color:#999999;width:26px;display:inline-block;font-size:9px;">Q{{ (int)$q + 1 }}</span>
                    <span style="color:#444444;">{{ $count }} {{ $count > 1 ? $tr['abandons'] : $tr['abandon'] }}</span>
                  </div>
                @endforeach
              @endif
            </div>
          </div>
        </td>
      </tr>
    </table>

  </div>

  {{-- Footer --}}
  <div class="foot">
    <table class="ft" cellpadding="0" cellspacing="0">
      <tr>
        <td>
          <div class="fo">{{ $tr['footer_org'] }}</div>
          <div class="fs">donnez-votre-sang.ch</div>
        </td>
        <td class="fr">
          <div class="fu">{{ $tr['generated_at'] }}</div>
          <div class="fd">{{ $generatedAt }}</div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
