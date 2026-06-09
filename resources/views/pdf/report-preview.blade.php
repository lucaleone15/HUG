@php
    use Carbon\Carbon;
    $qs = $participation['quiz_started'] ?? 0;
    $qc = $participation['quiz_completed'] ?? 0;
    $el = $participation['eligible'] ?? 0;
    $rdv = $participation['rdv_clicked'] ?? 0;
    $sub = $participation['total_submissions'] ?? 0;
    $emp = $entreprise['employee_count'] ?? 0;
    $dur = $behavior['avg_duration_s'] ?? null;
    $dc = 'SANG-' . date('y');

    $pct = fn($a, $b) => $b > 0 ? round(($a / $b) * 1000) / 10 : 0;
    $fmt = fn($n) => number_format($n ?? 0, 0, '.', "\u{202F}");

    $logoColor = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(public_path('images/hug-logo.svg')));
    $logoBlanc =
        'data:image/svg+xml;base64,' . base64_encode(file_get_contents(public_path('images/hug-logo_blanc.svg')));

    $rawName = $entreprise['name'] ?? 'Entreprise';
    $shortName =
        mb_strlen($rawName) <= 9
            ? mb_strtoupper($rawName)
            : mb_strtoupper(
                implode(
                    '',
                    array_map(
                        fn($w) => mb_substr($w, 0, 1),
                        array_filter(explode(' ', $rawName), fn($w) => mb_strlen($w) > 2),
                    ),
                ),
            );

    $partRate = $pct($qs, $emp);
    $compRate = $pct($qc, $qs);
    $eligRate = $pct($el, $sub);
    $convRate = $pct($rdv, $el);
    $e2eRate  = $pct($rdv, $qs);
    $score    = round(($partRate + $eligRate + $convRate) / 3);
    $perf =
        $score >= 65
            ? $tr['perf_excellent']
            : ($score >= 45
                ? $tr['perf_bon']
                : ($score >= 25
                    ? $tr['perf_moyen']
                    : $tr['perf_faible']));

    $drop1 = $qs - $qc;
    $drop2 = $qc - $el;
    $drop3 = $el - $rdv;

    $abs = collect($behavior['abandon_by_question'] ?? [])
        ->map(fn($n, $k) => ['q' => (int) $k + 1, 'n' => (int) $n])
        ->sortByDesc('n')
        ->values();
    $maxAb   = $abs->max('n') ?: 1;
    $totalAb = $abs->sum('n');
    $topQ    = $abs->first();

    $potPart = round($emp * 0.25);
    $potElig = round(($potPart * $eligRate) / 100);
    $potRdv  = round(($potElig * $convRate) / 100);

    $lives = $rdv * 3;

    $durCtx = $dur
        ? ($dur < 120
            ? $tr['dur_very_fast']
            : ($dur < 180
                ? $tr['dur_concise']
                : ($dur < 300
                    ? $tr['dur_optimal']
                    : $tr['dur_long'])))
        : '-';

    $rateRows = [
        [
            'label' => $tr['participation_rate'],
            'val'   => $partRate,
            'note'  => $fmt($qs) . ' / ' . $fmt($emp) . ' ' . $tr['employees'],
            'reco'  => $partRate < 25 ? $tr['reco_part_low'] : ($partRate < 50 ? $tr['reco_part_med'] : $tr['reco_part_high']),
        ],
        [
            'label' => $tr['eligibility_rate'],
            'val'   => $eligRate,
            'note'  => $fmt($el) . ' ' . $tr['eligible'] . ' / ' . $fmt($sub) . ' ' . $tr['synth_soumissions'],
            'reco'  => $eligRate < 50 ? $tr['reco_elig_low'] : ($eligRate < 70 ? $tr['reco_elig_med'] : $tr['reco_elig_high']),
        ],
        [
            'label' => $tr['conversion_rate'],
            'val'   => $convRate,
            'note'  => $fmt($rdv) . ' ' . $tr['synth_rdv_taken'] . ' / ' . $fmt($el) . ' ' . $tr['eligible'],
            'reco'  => $convRate < 50 ? $tr['reco_conv_low'] : ($convRate < 75 ? $tr['reco_conv_med'] : $tr['reco_conv_high']),
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Rapport {{ $dc }} : {{ $entreprise['name'] }}</title>
    <style>
        @font-face {
            font-family: 'Cooper Hewitt';
            src: url('https://cdn.jsdelivr.net/npm/@fontsource/cooper-hewitt@5.2.5/files/cooper-hewitt-latin-300-normal.woff2') format('woff2');
            font-weight: 300;
            font-display: block
        }

        @font-face {
            font-family: 'Cooper Hewitt';
            src: url('https://cdn.jsdelivr.net/npm/@fontsource/cooper-hewitt@5.2.5/files/cooper-hewitt-latin-400-normal.woff2') format('woff2');
            font-weight: 400;
            font-display: block
        }

        @font-face {
            font-family: 'Cooper Hewitt';
            src: url('https://cdn.jsdelivr.net/npm/@fontsource/cooper-hewitt@5.2.5/files/cooper-hewitt-latin-700-normal.woff2') format('woff2');
            font-weight: 700;
            font-display: block
        }

        @font-face {
            font-family: 'Cooper Hewitt';
            src: url('https://cdn.jsdelivr.net/npm/@fontsource/cooper-hewitt@5.2.5/files/cooper-hewitt-latin-800-normal.woff2') format('woff2');
            font-weight: 800;
            font-display: block
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0
            }
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html,
        body {
            width: 794px;
            height: 1122px;
            overflow: hidden;
            background: #F0F0F0;
            font-family: 'Cooper Hewitt', sans-serif;
            font-size: 10.5px;
            color: #1A1A1A;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact
        }

        /* ─── HEADER ─────────────────────────────────────────── */
        .hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            background: #fff;
            border-bottom: 3px solid #E8001C;
            height: 46px
        }

        .hdr-l {
            display: flex;
            align-items: center;
            gap: 12px
        }

        .hdr-sep {
            width: 1px;
            height: 28px;
            background: #E5E5E5
        }

        .hdr-tag {
            font-size: 7.5px;
            font-weight: 300;
            color: #6B6B6B;
            margin-bottom: 2px
        }

        .hdr-name {
            font-size: 13px;
            font-weight: 800;
            color: #1A1A1A;
            letter-spacing: -.3px
        }

        .hdr-r {
            text-align: right
        }

        .hdr-dc {
            font-size: 11px;
            font-weight: 800;
            color: #fff;
            background: #E8001C;
            padding: 3px 10px;
            border-radius: 8px;
            display: inline-block
        }

        .hdr-date {
            font-size: 7.5px;
            font-weight: 300;
            color: #6B6B6B;
            margin-top: 3px
        }

        /* ─── BODY GRID ───────────────────────────────────────── */
        .body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 10px 12px;
            height: 1036px;
            align-items: start;
            overflow: hidden
        }

        .bcol {
            display: flex;
            flex-direction: column;
            gap: 14px
        }

        .sec-g {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .sec-lbl {
            font-size: 7px;
            font-weight: 600;
            color: #AAA;
            text-transform: uppercase;
            letter-spacing: .8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #E0E0E0
        }

        /* ─── CARDS ───────────────────────────────────────────── */
        .card {
            background: #fff;
            border: 1px solid #E8E8E8;
            border-radius: 6px;
            padding: 11px 13px
        }

        .ct {
            font-size: 9.5px;
            font-weight: 700;
            color: #1A1A1A;
            margin-bottom: 9px;
            letter-spacing: -.2px
        }

        /* ─── INFO ROWS ───────────────────────────────────────── */
        .ir {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #F4F4F4
        }

        .ir:last-child {
            border-bottom: none
        }

        .il {
            font-size: 8px;
            font-weight: 400;
            color: #888
        }

        .iv {
            font-size: 8.5px;
            font-weight: 700;
            color: #1A1A1A
        }

        /* ─── RATES ───────────────────────────────────────────── */
        .rate-wrap {
            margin-bottom: 8px
        }

        .rate-wrap:last-child {
            margin-bottom: 0
        }

        .rate-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px
        }

        .rate-lbl {
            font-size: 8px;
            font-weight: 400;
            color: #555
        }

        .rate-val {
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: -.3px
        }

        .rate-bg {
            height: 5px;
            background: #F0F0F0;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 4px
        }

        .rate-fill {
            height: 5px;
            border-radius: 3px
        }

        .rate-foot {
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .rate-note {
            font-size: 6.5px;
            font-weight: 300;
            color: #AAA;
            font-style: italic;
            flex: 1;
            margin-right: 6px
        }

        .rate-badge {
            font-size: 6px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 4px;
            flex-shrink: 0
        }

        /* ─── DIAGNOSTICS ─────────────────────────────────────── */
        .diag {
            display: flex;
            gap: 7px;
            padding: 6px 0;
            border-bottom: 1px solid #F4F4F4
        }

        .diag:last-child {
            border-bottom: none
        }

        .diag-ic {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 9px;
            margin-top: 1px
        }

        .diag-t {
            font-size: 8px;
            font-weight: 700;
            color: #1A1A1A;
            margin-bottom: 2px
        }

        .diag-x {
            font-size: 7px;
            font-weight: 400;
            color: #888;
            line-height: 1.5
        }

        /* ─── RECOMMENDATIONS ─────────────────────────────────── */
        .reco-row {
            display: flex;
            gap: 6px;
            padding: 5px 0;
            border-bottom: 1px solid #F4F4F4
        }

        .reco-row:last-child {
            border-bottom: none
        }

        .reco-n {
            font-size: 8px;
            font-weight: 800;
            color: #E8001C;
            width: 12px;
            flex-shrink: 0;
            margin-top: 1px
        }

        .reco-txt {
            font-size: 7.5px;
            font-weight: 400;
            color: #1A1A1A;
            line-height: 1.5
        }

        /* ─── TABLE ───────────────────────────────────────────── */
        .ctable {
            width: 100%;
            border-collapse: collapse
        }

        .ctable th,
        .ctable td {
            padding: 4px 5px;
            font-size: 7.5px;
            border-bottom: 1px solid #F4F4F4;
            text-align: right
        }

        .ctable th {
            font-weight: 600;
            color: #888;
            text-align: left;
            font-size: 7px
        }

        .ctable td:first-child {
            text-align: left;
            font-weight: 400;
            color: #888
        }

        .td-now {
            font-weight: 700;
            color: #1A1A1A
        }

        .td-obj {
            font-weight: 800;
            color: #16a34a
        }

        /* ─── ABANDON BARS ────────────────────────────────────── */
        .brow {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 3px 0
        }

        .bq {
            font-size: 7.5px;
            font-weight: 700;
            color: #1A1A1A;
            width: 22px;
            flex-shrink: 0
        }

        .btrack {
            flex: 1;
            height: 5px;
            background: #F0F0F0;
            border-radius: 3px;
            overflow: hidden
        }

        .bfill {
            height: 5px;
            border-radius: 3px
        }

        .bval {
            font-size: 8px;
            font-weight: 700;
            color: #1A1A1A;
            width: 20px;
            text-align: right;
            flex-shrink: 0
        }

        .bpct {
            font-size: 6.5px;
            font-weight: 400;
            color: #AAA;
            width: 28px;
            flex-shrink: 0
        }
    </style>
</head>

<body>

    <div class="hdr">
        <div class="hdr-l">
            <img src="{{ $logoColor }}" alt="HUG" style="height:34px;width:auto">
            <div class="hdr-sep"></div>
            <div>
                <div class="hdr-tag">{{ $tr['report_title'] }} · {{ $tr['report_subtitle'] }}</div>
                <div class="hdr-name">{{ $entreprise['name'] }}</div>
            </div>
            <div class="hdr-sep"></div>
            @if (!empty($entreprise['logo_url']))
                <div style="background:#fff;border:1px solid #EBEBEB;border-radius:5px;padding:3px 6px;display:inline-flex;align-items:center;justify-content:center;height:34px">
                    <img src="{{ $entreprise['logo_url'] }}" alt="{{ $entreprise['name'] }}" style="height:26px;max-width:80px;object-fit:contain">
                </div>
            @else
                <div style="width:34px;height:34px;border-radius:50%;background:{{ $entreprise['primary_color'] ?? '#E8001C' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;font-weight:800;color:white;letter-spacing:0">{{ mb_strtoupper(mb_substr($rawName, 0, 1)) }}</div>
            @endif
        </div>
        <div class="hdr-r">
            <div class="hdr-dc">{{ $dc }}</div>
            <div class="hdr-date">{{ $generatedAt }}</div>
        </div>
    </div>

    <div class="body">

        {{-- ════════════════ LEFT COLUMN ════════════════ --}}
        <div class="bcol">

            {{-- GROUP: Entreprise --}}
            <div class="sec-g">
            <div class="sec-lbl">{{ $tr['section_company'] }}</div>
            <div class="card">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                    @if (!empty($entreprise['logo_url']))
                        <div style="background:#F7F7F7;border:1px solid #EBEBEB;border-radius:5px;padding:3px 7px;flex-shrink:0">
                            <img src="{{ $entreprise['logo_url'] }}" alt="{{ $entreprise['name'] }}" style="height:22px;max-width:66px;object-fit:contain;display:block">
                        </div>
                    @else
                        <div style="width:30px;height:30px;border-radius:50%;background:{{ $entreprise['primary_color'] ?? '#E8001C' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;font-weight:800;color:white">{{ mb_strtoupper(mb_substr($rawName, 0, 1)) }}</div>
                    @endif
                    <div style="font-size:11px;font-weight:800;color:#1A1A1A;letter-spacing:-.3px;line-height:1.2">{{ $rawName }}</div>
                </div>
                @if ($entreprise['contact_name'])
                    <div class="ir">
                        <span class="il">{{ $tr['contact_name'] }}</span>
                        <span class="iv">{{ $entreprise['contact_name'] }}</span>
                    </div>
                @endif
                @if ($entreprise['contact_email'])
                    <div class="ir">
                        <span class="il">{{ $tr['contact_email'] }}</span>
                        <span class="iv" style="font-size:7.5px">{{ $entreprise['contact_email'] }}</span>
                    </div>
                @endif
                @if ($emp)
                    <div class="ir">
                        <span class="il">{{ $tr['employee_count'] }}</span>
                        <span class="iv">{{ $fmt($emp) }}<span style="font-weight:300;color:#AAA;font-size:7px"> {{ $tr['employees'] }}</span></span>
                    </div>
                @endif
                <div class="ir">
                    <span class="il">{{ $tr['generated_at'] }}</span>
                    <span class="iv">{{ $generatedAt }}</span>
                </div>
            </div>
            </div>{{-- /sec-g: Entreprise --}}

            {{-- GROUP: Performance --}}
            <div class="sec-g">
            <div class="sec-lbl">{{ $tr['group_performance'] }}</div>
            <div class="card">
                <div class="ct">{{ $tr['section_rates'] }}</div>
                @foreach ($rateRows as $r)
                    @php
                        $col   = $r['val'] >= 70 ? '#16a34a' : ($r['val'] >= 50 ? '#d97706' : '#E8001C');
                        $badge = $r['val'] >= 70 ? $tr['badge_excellent'] : ($r['val'] >= 50 ? $tr['badge_bon'] : ($r['val'] >= 30 ? $tr['badge_moyen'] : $tr['badge_faible']));
                    @endphp
                    <div class="rate-wrap">
                        <div class="rate-head">
                            <span class="rate-lbl">{{ $r['label'] }}</span>
                            <span class="rate-val" style="color:{{ $col }}">{{ $r['val'] }}%</span>
                        </div>
                        <div class="rate-bg">
                            <div class="rate-fill" style="width:{{ min($r['val'], 100) }}%;background:{{ $col }}"></div>
                        </div>
                        <div class="rate-foot">
                            <span class="rate-note">{{ $r['note'] }} · {{ $r['reco'] }}</span>
                            <span class="rate-badge" style="background:{{ $col }}18;color:{{ $col }}">{{ $badge }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Diagnostics --}}
            <div class="card">
                <div class="ct">{{ $tr['section_diagnostics'] }}</div>
                @if ($partRate < 20)
                    <div class="diag">
                        <div class="diag-ic" style="background:#FEE2E2">⚡</div>
                        <div>
                            <div class="diag-t">{{ str_replace(':pct', $partRate, $tr['diag_part_low_t']) }}</div>
                            <div class="diag-x">{{ str_replace([':started', ':emp'], [$fmt($qs), $fmt($emp)], $tr['diag_part_low_x']) }}</div>
                        </div>
                    </div>
                @else
                    <div class="diag">
                        <div class="diag-ic" style="background:#DCFCE7">✓</div>
                        <div>
                            <div class="diag-t">{{ str_replace(':pct', $partRate, $tr['diag_part_ok_t']) }}</div>
                            <div class="diag-x">{{ $tr['diag_part_ok_x'] }}</div>
                        </div>
                    </div>
                @endif
                @if ($eligRate >= 65)
                    <div class="diag">
                        <div class="diag-ic" style="background:#DCFCE7">✓</div>
                        <div>
                            <div class="diag-t">{{ str_replace(':pct', $eligRate, $tr['diag_elig_ok_t']) }}</div>
                            <div class="diag-x">{{ $tr['diag_elig_ok_x'] }}</div>
                        </div>
                    </div>
                @else
                    <div class="diag">
                        <div class="diag-ic" style="background:#FEF9C3">○</div>
                        <div>
                            <div class="diag-t">{{ str_replace(':pct', $eligRate, $tr['diag_elig_low_t']) }}</div>
                            <div class="diag-x">{{ str_replace(':nonElig', round(100 - $eligRate, 1), $tr['diag_elig_low_x']) }}</div>
                        </div>
                    </div>
                @endif
                @if ($convRate >= 70)
                    <div class="diag">
                        <div class="diag-ic" style="background:#DCFCE7">✓</div>
                        <div>
                            <div class="diag-t">{{ str_replace(':pct', $convRate, $tr['diag_conv_ok_t']) }}</div>
                            <div class="diag-x">{{ str_replace([':rdv', ':eligible'], [$fmt($rdv), $fmt($el)], $tr['diag_conv_ok_x']) }}</div>
                        </div>
                    </div>
                @else
                    <div class="diag">
                        <div class="diag-ic" style="background:#EEF2FF">◎</div>
                        <div>
                            <div class="diag-t">{{ str_replace(':score', $score, $tr['diag_score_t']) }}</div>
                            <div class="diag-x">{{ str_replace([':perf', ':part', ':elig', ':conv'], [$perf, $partRate, $eligRate, $convRate], $tr['diag_score_x']) }}</div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Recommendations --}}
            <div class="card">
                <div class="ct">{{ $tr['section_reco'] }}</div>
                @if ($partRate < 25)
                    <div class="reco-row"><span class="reco-n">1.</span><span class="reco-txt">{{ $tr['reco_1_low'] }}</span></div>
                @else
                    <div class="reco-row"><span class="reco-n">1.</span><span class="reco-txt">{{ $tr['reco_1_ok'] }}</span></div>
                @endif
                <div class="reco-row">
                    <span class="reco-n">2.</span>
                    <span class="reco-txt">{!! $topQ ? str_replace([':q', ':n'], ['Q' . $topQ['q'], $topQ['n']], $tr['reco_2']) : $tr['reco_2_default'] !!}</span>
                </div>
                <div class="reco-row">
                    <span class="reco-n">3.</span>
                    <span class="reco-txt">{!! str_replace([':part', ':elig', ':rdv'], [$fmt($potPart), $fmt($potElig), $fmt($potRdv)], $tr['reco_3']) !!}</span>
                </div>
            </div>
            </div>{{-- /sec-g: Performance --}}

            {{-- GROUP: Prévisions 2027 --}}
            <div class="sec-g">
            <div class="sec-lbl">{{ $tr['section_objectives'] }}</div>
            <div class="card">
                <div class="ct">{{ $tr['section_objectives'] }}</div>
                <table class="ctable" style="margin-bottom:5px">
                    <tr>
                        <th>{{ $tr['obj_indicator'] }}</th>
                        <th style="text-align:right">{{ $tr['obj_current'] }}</th>
                        <th style="text-align:right">{{ $tr['obj_target'] }}</th>
                    </tr>
                    <tr>
                        <td>{{ $tr['obj_participation'] }}</td>
                        <td class="td-now">{{ $partRate }}%</td>
                        <td class="td-obj">25%</td>
                    </tr>
                    <tr>
                        <td>{{ $tr['obj_participants'] }}</td>
                        <td class="td-now">{{ $fmt($qs) }}</td>
                        <td class="td-obj">{{ $fmt($potPart) }}</td>
                    </tr>
                    <tr>
                        <td>{{ $tr['obj_eligible_pl'] }}</td>
                        <td class="td-now">{{ $fmt($el) }}</td>
                        <td class="td-obj">{{ $fmt($potElig) }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom:none">{{ $tr['obj_rdv'] }}</td>
                        <td class="td-now" style="border-bottom:none">{{ $fmt($rdv) }}</td>
                        <td class="td-obj" style="border-bottom:none">{{ $fmt($potRdv) }}</td>
                    </tr>
                </table>
                <div style="font-size:6.5px;font-weight:300;color:#BBB;font-style:italic;line-height:1.4">{{ $tr['obj_note'] }}</div>
            </div>
            </div>{{-- /sec-g: Prévisions 2027 --}}

        </div>

        {{-- ════════════════ RIGHT COLUMN ════════════════ --}}
        <div class="bcol">

            {{-- GROUP: Résultats --}}
            <div class="sec-g">
            <div class="sec-lbl">{{ $tr['group_results'] }}</div>
            <div class="card">
                <div class="ct">{{ $tr['section_participation'] }}</div>
                <div class="ir">
                    <span class="il">{{ $tr['quiz_started'] }}</span>
                    <span class="iv">{{ $fmt($qs) }}</span>
                </div>
                <div class="ir">
                    <span class="il">{{ $tr['quiz_completed'] }}</span>
                    <span class="iv">{{ $fmt($qc) }}<span style="font-weight:300;color:#AAA;font-size:7px;margin-left:4px">{{ $compRate }}%</span></span>
                </div>
                <div class="ir">
                    <span class="il">{{ $tr['eligible'] }}</span>
                    <span class="iv" style="color:#E8001C">{{ $fmt($el) }}<span style="font-weight:300;color:#AAA;font-size:7px;margin-left:4px">{{ $eligRate }}%</span></span>
                </div>
                <div class="ir">
                    <span class="il">{{ $tr['rdv_clicked'] }}</span>
                    <span class="iv">{{ $fmt($rdv) }}<span style="font-weight:300;color:#AAA;font-size:7px;margin-left:4px">{{ $convRate }}%</span></span>
                </div>
                @if ($emp)
                    <div class="ir">
                        <span class="il">{{ $tr['employee_count'] }}</span>
                        <span class="iv">{{ $fmt($emp) }}<span style="font-weight:300;color:#AAA;font-size:7px;margin-left:4px">{{ $tr['employees'] }}</span></span>
                    </div>
                @endif
                <div style="margin-top:8px;padding-top:7px;border-top:1px solid #F4F4F4">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                        <span style="font-size:8px;color:#888">{{ $tr['performance'] }}</span>
                        <span style="font-size:9.5px;font-weight:800;color:#1A1A1A;letter-spacing:-.3px">{{ $perf }} <span style="color:#E8001C">{{ $score }}%</span></span>
                    </div>
                    <div style="height:4px;background:#F0F0F0;border-radius:2px;overflow:hidden">
                        <div style="height:4px;background:#E8001C;border-radius:2px;width:{{ $score }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Behaviour --}}
            <div class="card">
                <div class="ct">{{ $tr['section_behavior_dur'] }}</div>
                <div class="ir" style="padding-bottom:9px">
                    <span class="il">{{ $tr['avg_duration'] }}</span>
                    <span style="text-align:right">
                        <span style="font-size:26px;font-weight:800;color:#1A1A1A;letter-spacing:-1px;line-height:1">{{ $dur ?? '-' }}</span><span style="font-size:12px;font-weight:300;color:#888">s</span>
                        <div style="font-size:7px;font-weight:300;color:#AAA;font-style:italic;margin-top:1px;text-align:right">{{ $durCtx }}</div>
                    </span>
                </div>
                @if ($abs->count())
                    <div style="border-top:1px solid #F4F4F4;padding-top:7px">
                        <div style="font-size:8px;font-weight:600;color:#888;margin-bottom:3px">{{ $tr['abandon_by_question'] }}</div>
                        <div style="font-size:7px;font-weight:300;color:#AAA;margin-bottom:3px">{{ $fmt($totalAb) }} {{ $tr['abandons'] }} · {{ $abs->count() }} {{ $tr['abandon_questions'] }}@if ($topQ) · {{ str_replace(':q', 'Q' . $topQ['q'], $tr['abandon_most_blocked']) }}@endif</div>
                        @if ($topQ)
                            <div style="font-size:7px;color:#E8001C;font-style:italic;margin-bottom:5px">→ {{ $topQ['q'] <= 3 ? $tr['abandon_reco_early'] : ($topQ['q'] >= 10 ? $tr['abandon_reco_late'] : $tr['abandon_reco_mid']) }}</div>
                        @endif
                        @foreach ($abs->take(8) as $a)
                            @php
                                $w      = round(($a['n'] / $maxAb) * 100);
                                $pctTot = round(($a['n'] / max($totalAb, 1)) * 100, 1);
                                $c      = $a['n'] >= $maxAb * 0.7 ? '#E8001C' : ($a['n'] >= $maxAb * 0.4 ? '#d97706' : '#94A3B8');
                            @endphp
                            <div class="brow">
                                <span class="bq">Q{{ $a['q'] }}</span>
                                <div class="btrack"><div class="bfill" style="width:{{ $w }}%;background:{{ $c }}"></div></div>
                                <span class="bval">{{ $a['n'] }}</span>
                                <span class="bpct">{{ $pctTot }}%</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            </div>{{-- /sec-g: Résultats --}}

            {{-- GROUP: Benchmark --}}
            <div class="sec-g">
            <div class="sec-lbl">{{ $tr['section_benchmark'] }}</div>
            <div class="card">
                <table class="ctable" style="margin-bottom:5px">
                    <tr>
                        <th></th>
                        <th style="text-align:right">{{ $shortName }}</th>
                        <th style="text-align:right">{{ $tr['bench_sector_avg'] }}</th>
                        <th style="text-align:right">{{ $tr['bench_top'] }}</th>
                    </tr>
                    <tr>
                        <td>{{ $tr['participation_rate'] }}</td>
                        <td class="td-now">{{ $partRate }}%</td>
                        <td style="font-weight:600;color:#555">18%</td>
                        <td style="font-weight:600;color:#16a34a">42%</td>
                    </tr>
                    <tr>
                        <td>{{ $tr['eligibility_rate'] }}</td>
                        <td class="td-now">{{ $eligRate }}%</td>
                        <td style="font-weight:600;color:#555">63%</td>
                        <td style="font-weight:600;color:#16a34a">78%</td>
                    </tr>
                    <tr>
                        <td style="border-bottom:none">{{ $tr['conversion_rate'] }}</td>
                        <td class="td-now {{ $convRate >= 70 ? 'td-obj' : '' }}" style="border-bottom:none">{{ $convRate }}%</td>
                        <td style="font-weight:600;color:#555;border-bottom:none">58%</td>
                        <td style="font-weight:600;color:#16a34a;border-bottom:none">89%</td>
                    </tr>
                </table>
                <div style="font-size:6.5px;font-weight:300;color:#BBB;font-style:italic;line-height:1.4">{{ $tr['bench_note'] }}</div>
            </div>
            </div>{{-- /sec-g: Benchmark --}}

            {{-- GROUP: Simulation & Impact --}}
            <div class="sec-g">
            <div class="sec-lbl">{{ $tr['group_projections'] }}</div>
            @if ($emp)
            <div class="card">
                <div class="ct">{{ $tr['sim_title'] }}</div>
                <div style="margin-bottom:8px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                        <span style="font-size:7.5px;color:#888">{{ $tr['sim_participants'] }}</span>
                        <span>
                            <span style="font-size:15px;font-weight:800;color:#CCC;letter-spacing:-.5px">{{ $fmt($qs) }}</span>
                            <span style="color:#E8001C;font-weight:700;margin:0 5px">→</span>
                            <span style="font-size:15px;font-weight:800;color:#1A1A1A;letter-spacing:-.5px">{{ $fmt($potPart) }}</span>
                        </span>
                    </div>
                    <div style="height:5px;background:#F0F0F0;border-radius:3px;overflow:hidden;position:relative">
                        <div style="height:5px;background:#E8001C;opacity:.25;border-radius:3px;width:100%;position:absolute"></div>
                        <div style="height:5px;background:#E8001C;border-radius:3px;width:{{ round(($qs / max($potPart, 1)) * 100) }}%;position:absolute"></div>
                    </div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                        <span style="font-size:7.5px;color:#888">{{ $tr['sim_eligible_lbl'] }}</span>
                        <span>
                            <span style="font-size:15px;font-weight:800;color:#CCC;letter-spacing:-.5px">{{ $fmt($el) }}</span>
                            <span style="color:#E8001C;font-weight:700;margin:0 5px">→</span>
                            <span style="font-size:15px;font-weight:800;color:#E8001C;letter-spacing:-.5px">{{ $fmt($potElig) }}</span>
                        </span>
                    </div>
                    <div style="height:5px;background:#F0F0F0;border-radius:3px;overflow:hidden;position:relative">
                        <div style="height:5px;background:#E8001C;opacity:.25;border-radius:3px;width:100%;position:absolute"></div>
                        <div style="height:5px;background:#E8001C;border-radius:3px;width:{{ round(($el / max($potElig, 1)) * 100) }}%;position:absolute"></div>
                    </div>
                </div>
                @if ($potElig > $el)
                    <div style="font-size:7px;color:#E8001C;font-style:italic;margin-top:5px">{{ str_replace([':gain', ':rdvGain'], [$fmt($potElig - $el), $fmt($potRdv - $rdv)], $tr['sim_gain']) }}</div>
                @endif
                <div style="font-size:6.5px;color:#AAA;font-style:italic;margin-top:2px">{{ str_replace([':elig', ':conv'], [$eligRate, $convRate], $tr['sim_note']) }}</div>
            </div>
            @endif
            {{-- Impact + Contact --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                <div class="card">
                    <div class="ct">{{ $tr['impact_title'] }}</div>
                    <div style="font-size:28px;font-weight:800;color:#E8001C;letter-spacing:-1px;line-height:1;margin-bottom:3px">{{ $fmt($lives) }}</div>
                    <div style="font-size:7.5px;font-weight:400;color:#555;line-height:1.5;margin-bottom:3px">{{ str_replace(':rdv', $fmt($rdv), $tr['impact_sub']) }}</div>
                    <div style="font-size:6.5px;font-weight:300;color:#AAA;font-style:italic;line-height:1.4">{{ str_replace([':rdv', ':lives'], [$fmt($rdv), $fmt($lives)], $tr['impact_eq']) }}</div>
                </div>
                <div class="card">
                    <div class="ct">{{ $tr['contact_title'] }}</div>
                    <div style="font-size:8px;font-weight:700;color:#1A1A1A;margin-bottom:1px">{{ $tr['contact_org'] }}</div>
                    <div style="font-size:7px;font-weight:300;color:#888;line-height:1.4;padding-bottom:6px;margin-bottom:6px;border-bottom:1px solid #F4F4F4">{{ $tr['contact_responsible'] }}</div>
                    <div class="ir">
                        <span class="il">{{ $tr['contact_email_label'] }}</span>
                        <span style="font-size:7.5px;font-weight:700;color:#1A1A1A">{{ $tr['contact_email_val'] }}</span>
                    </div>
                    <div class="ir">
                        <span class="il">{{ $tr['contact_phone_label'] }}</span>
                        <span style="font-size:8.5px;font-weight:700;color:#1A1A1A">{{ $tr['contact_phone_val'] }}</span>
                    </div>
                </div>
            </div>
            </div>{{-- /sec-g: Simulation & Impact --}}

        </div>

    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;background:#160B0B;padding:0 20px;height:40px">
        <div style="display:flex;align-items:center;gap:10px">
            <img src="{{ $logoBlanc }}" alt="HUG" style="height:22px;width:auto;opacity:.6;flex-shrink:0">
            <div>
                <div style="font-size:7.5px;font-weight:600;color:rgba(255,255,255,.5)">{{ $tr['footer_hug'] }}</div>
                <div style="font-size:6.5px;font-weight:300;color:rgba(255,255,255,.25);margin-top:1px">{{ $tr['footer_site'] }}</div>
            </div>
        </div>
        <div style="text-align:center">
            <div style="font-size:7px;font-weight:300;color:rgba(255,255,255,.4)">{{ $tr['footer_site'] }}</div>
        </div>
        <div style="text-align:right">
            <div style="font-size:9px;font-weight:700;color:#E8001C">{{ $dc }}</div>
            <div style="font-size:6.5px;font-weight:300;color:rgba(255,255,255,.3);margin-top:1px">{{ $tr['generated_at'] }} {{ $generatedAt }}</div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 500);
        });
    </script>

</body>

</html>
