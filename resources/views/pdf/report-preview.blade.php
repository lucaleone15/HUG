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
    $e2eRate = $pct($rdv, $qs);
    $score = round(($partRate + $eligRate + $convRate) / 3);
    $perf =
        $score >= 65
            ? $tr['perf_excellent']
            : ($score >= 45
                ? $tr['perf_bon']
                : ($score >= 25
                    ? $tr['perf_moyen']
                    : $tr['perf_faible']));

    $diagCount = 2 + ($convRate >= 70 ? 1 : 0);

    $drop1 = $qs - $qc;
    $drop2 = $qc - $el;
    $drop3 = $el - $rdv;

    $abs = collect($behavior['abandon_by_question'] ?? [])
        ->map(fn($n, $k) => ['q' => (int) $k + 1, 'n' => (int) $n])
        ->sortByDesc('n')
        ->values();
    $maxAb = $abs->max('n') ?: 1;
    $totalAb = $abs->sum('n');
    $topQ = $abs->first();

    $potPart = round($emp * 0.25);
    $potElig = round(($potPart * $eligRate) / 100);
    $potRdv = round(($potElig * $convRate) / 100);

    $obj2027Part = 25;
    $obj2027Elig = $fmt(round(($emp * 0.25 * $eligRate) / 100));
    $obj2027Rdv = $fmt(round(((($emp * 0.25 * $eligRate) / 100) * $convRate) / 100));

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
    $durPct = $dur ? min(round(($dur / 600) * 100), 100) : 0;
    $durCirc = round(($durPct / 100) * 163.4, 1); // 2π×26 ≈ 163.4

    $gaV = fn($v) => round((min($v, 100) / 100) * 125.7, 1); // π×40 = 125.7
    $gaC = fn($v) => $v >= 70 ? '#16a34a' : ($v >= 50 ? '#d97706' : '#E8001C');
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
            background: #fff;
            font-family: 'Cooper Hewitt', sans-serif;
            font-size: 10.5px;
            color: #1A1A1A;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact
        }

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

        .kband {
            display: flex;
            background: #1A1A1A;
            height: 100px
        }

        .kpi {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8px 4px;
            border-right: 1px solid #2A2A2A
        }

        .kpi:last-child {
            border-right: none
        }

        .kpi-icon {
            margin-bottom: 3px;
            opacity: .6
        }

        .kpi-n {
            font-size: 50px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            letter-spacing: -2px;
            margin-bottom: 2px
        }

        .kpi-l {
            font-size: 7px;
            font-weight: 300;
            color: #AAA;
            margin-bottom: 1px
        }

        .kpi-sub {
            font-size: 7.5px;
            font-weight: 400;
            color: #888
        }

        .kpi-alt {
            background: #222
        }

        .kpi-alt .kpi-n {
            font-size: 36px;
            color: #E8001C
        }

        .body {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            height: 932px;
            align-items: stretch
        }

        .col {
            display: flex;
            flex-direction: column;
            border-right: 1px solid #E5E5E5;
            overflow: hidden
        }

        .col:last-child {
            border-right: none;
            background: #1A1A1A;
            color: #fff
        }

        .blk {
            flex-shrink: 0
        }

        .blk-grow {
            flex: 1
        }

        .sh {
            padding: 5px 14px;
            font-size: 7.5px;
            font-weight: 600;
            color: #6B6B6B;
            background: #F9F9F9;
            border-bottom: 1px solid #E5E5E5;
            border-top: 1px solid #E5E5E5
        }

        .sb {
            padding: 8px 14px
        }

        .hsep {
            height: 1px;
            background: #E5E5E5;
            flex-shrink: 0
        }

        .ir {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #F0F0F0
        }

        .ir:last-child {
            border-bottom: none
        }

        .il {
            font-size: 7.5px;
            font-weight: 400;
            color: #6B6B6B
        }

        .iv {
            font-size: 10.5px;
            font-weight: 600;
            color: #1A1A1A
        }

        .iv em {
            font-size: 8.5px;
            font-weight: 300;
            color: #6B6B6B;
            font-style: normal;
            margin-left: 2px
        }

        .gauge-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 7px 0;
            border-bottom: 1px solid #F0F0F0
        }

        .gauge-item:last-child {
            border-bottom: none
        }

        .gauge-svg {
            flex-shrink: 0
        }

        .gauge-meta {
            flex: 1;
            min-width: 0
        }

        .gauge-lbl {
            font-size: 9px;
            font-weight: 600;
            color: #1A1A1A;
            margin-bottom: 2px
        }

        .gauge-note {
            font-size: 7px;
            font-weight: 400;
            color: #6B6B6B;
            line-height: 1.4;
            margin-bottom: 2px
        }

        .gauge-reco {
            font-size: 6.5px;
            font-weight: 400;
            color: #6B6B6B;
            font-style: italic;
            line-height: 1.4
        }

        .gauge-right {
            flex-shrink: 0;
            text-align: right
        }

        .gval {
            font-size: 19px;
            font-weight: 800;
            color: #1A1A1A;
            letter-spacing: -.5px;
            line-height: 1
        }

        .gval s {
            font-size: 11px;
            opacity: .6;
            text-decoration: none
        }

        .gbadge {
            font-size: 6px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 3px
        }

        .diag {
            display: flex;
            gap: 6px;
            margin-bottom: 6px
        }

        .diag-ic {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 10px;
            margin-top: 1px
        }

        .diag-body {
            flex: 1
        }

        .diag-t {
            font-size: 8.5px;
            font-weight: 700;
            color: #1A1A1A;
            margin-bottom: 2px
        }

        .diag-x {
            font-size: 7.5px;
            font-weight: 400;
            color: #6B6B6B;
            line-height: 1.5
        }

        .reco {
            background: #FFF8F8;
            border: 1px solid #E5E5E5;
            border-left: 3px solid #E8001C;
            padding: 8px 12px
        }

        .reco-t {
            font-size: 7.5px;
            font-weight: 700;
            color: #E8001C;
            margin-bottom: 6px
        }

        .reco-row {
            display: flex;
            gap: 6px;
            margin-bottom: 4px
        }

        .reco-n {
            font-size: 8px;
            font-weight: 800;
            color: #E8001C;
            width: 12px;
            flex-shrink: 0
        }

        .reco-txt {
            font-size: 7.5px;
            font-weight: 400;
            color: #1A1A1A;
            line-height: 1.5
        }

        /* ─── TABLE COMP ──────────────────────────────────────── */
        .ctable {
            width: 100%;
            border-collapse: collapse
        }

        .ctable th,
        .ctable td {
            padding: 4px 6px;
            font-size: 7.5px;
            border-bottom: 1px solid #F0F0F0;
            text-align: right
        }

        .ctable th {
            font-weight: 600;
            color: #6B6B6B;
            text-align: left
        }

        .ctable td:first-child {
            text-align: left;
            font-weight: 600;
            color: #6B6B6B
        }

        .ctable .td-now {
            font-weight: 700;
            color: #1A1A1A
        }

        .ctable .td-obj {
            font-weight: 800;
            color: #16a34a
        }

        .funnel-wrap {
            padding: 6px 14px;
            flex: 1;
            display: flex;
            flex-direction: column
        }

        .dur-block {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 14px
        }

        .dur-text {
            flex: 1
        }

        .dur-lbl {
            font-size: 7.5px;
            font-weight: 400;
            color: #6B6B6B;
            margin-bottom: 2px
        }

        .dur-val {
            font-size: 38px;
            font-weight: 800;
            color: #1A1A1A;
            letter-spacing: -1.5px;
            line-height: 1;
            margin-bottom: 2px
        }

        .dur-u {
            font-size: 16px;
            font-weight: 400;
            color: #6B6B6B
        }

        .dur-ctx {
            font-size: 7.5px;
            font-weight: 400;
            color: #6B6B6B;
            font-style: italic;
            line-height: 1.5
        }

        .bchart {
            padding: 6px 14px;
            flex: 1;
            display: flex;
            flex-direction: column
        }

        .bc-meta {
            font-size: 7.5px;
            font-weight: 400;
            color: #6B6B6B;
            margin-bottom: 3px
        }

        .bc-reco {
            font-size: 7px;
            font-weight: 400;
            color: #E8001C;
            font-style: italic;
            margin-bottom: 5px
        }

        .bars {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 5px;
            justify-content: flex-start
        }

        .brow {
            display: flex;
            align-items: center;
            gap: 5px
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
            height: 7px;
            background: #E5E5E5;
            border-radius: 3px;
            overflow: hidden
        }

        .bfill {
            height: 7px;
            border-radius: 3px
        }

        .bval {
            font-size: 8.5px;
            font-weight: 800;
            color: #1A1A1A;
            width: 18px;
            text-align: right;
            flex-shrink: 0
        }

        .bpct {
            font-size: 6.5px;
            font-weight: 400;
            color: #6B6B6B;
            width: 32px;
            flex-shrink: 0
        }

        .mini-box {
            background: #F9F9F9;
            border: 1px solid #E5E5E5;
            border-left: 3px solid #E8001C;
            padding: 8px 10px
        }

        .mini-t {
            font-size: 7.5px;
            font-weight: 600;
            color: #1A1A1A;
            margin-bottom: 5px
        }

        .mini-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 0;
            border-bottom: 1px solid #F0F0F0
        }

        .mini-row:last-child {
            border-bottom: none
        }

        .mini-l {
            font-size: 7.5px;
            font-weight: 400;
            color: #6B6B6B
        }

        .mini-v {
            font-size: 9px;
            font-weight: 700;
            color: #1A1A1A
        }

        .mini-vm {
            font-size: 9px;
            font-weight: 700;
            color: #16a34a
        }

        .d3e {
            font-size: 7px;
            font-weight: 400;
            color: #E8001C;
            margin-bottom: 3px
        }

        .d3t {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            line-height: 1.05;
            letter-spacing: -.4px;
            margin-bottom: 3px
        }

        .d3p {
            font-size: 7.5px;
            font-weight: 400;
            color: #AAA;
            margin-bottom: 5px
        }

        .d3p strong {
            font-weight: 700;
            color: #fff
        }

        .d3sc {
            font-size: 10px;
            font-weight: 800;
            color: #E8001C;
            margin-left: 3px
        }

        .sbar {
            height: 3px;
            background: rgba(255, 255, 255, .08);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 8px
        }

        .sfill {
            height: 3px;
            background: #E8001C;
            border-radius: 2px
        }

        .d3sep {
            height: 1px;
            background: #2D2D2D;
            margin: 7px 0;
            flex-shrink: 0
        }

        /* Vbars */
        .vbs {
            display: flex;
            gap: 5px;
            height: 82px;
            align-items: flex-end
        }

        .vb {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%
        }

        .vbt {
            flex: 1;
            width: 100%;
            background: rgba(255, 255, 255, .06);
            border-radius: 2px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            margin-bottom: 3px
        }

        .vbf {
            background: #E8001C;
            border-radius: 2px 2px 0 0;
            min-height: 2px
        }

        .vbfe {
            background: rgba(232, 0, 28, .3);
            border-radius: 2px 2px 0 0;
            min-height: 2px
        }

        .vbv {
            font-size: 11px;
            font-weight: 800;
            color: #fff;
            white-space: nowrap;
            letter-spacing: -.3px
        }

        .vbv s {
            font-size: 7px;
            opacity: .55;
            text-decoration: none
        }

        .vbve {
            color: rgba(255, 255, 255, .45)
        }

        .vbl {
            font-size: 6px;
            font-weight: 300;
            color: #888;
            text-align: center;
            margin-top: 2px;
            line-height: 1.3
        }

        .f4 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 7px 10px
        }

        .f4i {
            display: flex;
            flex-direction: column
        }

        .f4n {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.5px;
            line-height: 1
        }

        .f4l {
            font-size: 7px;
            font-weight: 300;
            color: #AAA;
            margin-top: 2px
        }

        .f4x {
            font-size: 6.5px;
            font-weight: 400;
            color: #888;
            font-style: italic;
            margin-top: 1px
        }

        .sim {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .1);
            border-left: 3px solid #E8001C;
            border-radius: 2px;
            padding: 8px 10px
        }

        .sim-t {
            font-size: 7px;
            font-weight: 600;
            color: #E8001C;
            margin-bottom: 6px
        }

        .sim-lbl {
            font-size: 7px;
            font-weight: 300;
            color: #AAA;
            margin-bottom: 2px;
            display: block
        }

        .sim-bar-wrap {
            height: 7px;
            background: rgba(255, 255, 255, .05);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 4px;
            position: relative
        }

        .sim-bar-cur {
            height: 7px;
            background: rgba(255, 255, 255, .2);
            border-radius: 3px;
            position: absolute;
            left: 0;
            top: 0
        }

        .sim-bar-pot {
            height: 7px;
            background: #E8001C;
            border-radius: 3px;
            position: absolute;
            left: 0;
            top: 0
        }

        .sim-nums {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 4px
        }

        .sim-now {
            font-size: 16px;
            font-weight: 800;
            color: #CCC;
            letter-spacing: -.5px
        }

        .sim-arr {
            font-size: 12px;
            color: #E8001C;
            font-weight: 700
        }

        .sim-pot {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.5px
        }

        .sim-gain {
            font-size: 7px;
            font-weight: 400;
            color: #E8001C;
            font-style: italic;
            margin-top: 3px
        }

        .sim-note {
            font-size: 6.5px;
            font-weight: 300;
            color: #888;
            font-style: italic;
            margin-top: 3px
        }

        .impact {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .1);
            border-left: 3px solid #E8001C;
            padding: 8px 10px
        }

        .impact-t {
            font-size: 7px;
            font-weight: 600;
            color: #E8001C;
            margin-bottom: 5px
        }

        .impact-big {
            font-size: 30px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
            line-height: 1;
            margin-bottom: 2px
        }

        .impact-sub {
            font-size: 7.5px;
            font-weight: 400;
            color: #CCC;
            line-height: 1.5
        }

        .impact-eq {
            font-size: 7px;
            font-weight: 300;
            color: #888;
            margin-top: 3px;
            font-style: italic
        }

        /* Ratio */
        .ratio-l {
            font-size: 7.5px;
            font-weight: 600;
            color: #AAA;
            margin-bottom: 4px
        }

        .ratio-b {
            height: 8px;
            background: #333;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 4px
        }

        .ratio-f {
            height: 8px;
            background: #E8001C;
            border-radius: 3px
        }

        .ratio-n {
            display: flex;
            justify-content: space-between;
            font-size: 7px;
            font-weight: 400;
            color: #AAA
        }

        .ratio-p {
            font-weight: 800;
            color: #fff
        }

        .d3ft {
            margin-top: auto;
            padding-top: 8px;
            border-top: 1px solid #2D2D2D;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .d3logo {
            height: 18px;
            width: auto;
            opacity: .5
        }

        .d3url {
            font-size: 7px;
            font-weight: 300;
            color: #888
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
        </div>
        <div class="hdr-r">
            <div class="hdr-dc">{{ $dc }}</div>
            <div class="hdr-date">{{ $generatedAt }}</div>
        </div>
    </div>

    <div class="kband">
        <div class="kpi">
            <div class="kpi-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E8001C"
                    stroke-width="2.5" stroke-linecap="round">
                    <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                </svg></div>
            <div class="kpi-n">{{ $fmt($qs) }}</div>
            <div class="kpi-l">{{ $tr['quiz_started'] }}</div>
            <div class="kpi-sub">{{ $tr['kpi_start_point'] }}</div>
        </div>
        <div class="kpi">
            <div class="kpi-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E8001C"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg></div>
            <div class="kpi-n">{{ $fmt($qc) }}</div>
            <div class="kpi-l">{{ $tr['quiz_completed'] }}</div>
            <div class="kpi-sub">{{ $compRate }}% {{ $tr['kpi_completion'] }}</div>
        </div>
        <div class="kpi">
            <div class="kpi-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="#E8001C">
                    <path
                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg></div>
            <div class="kpi-n">{{ $fmt($el) }}</div>
            <div class="kpi-l">{{ $tr['eligible'] }}</div>
            <div class="kpi-sub">{{ $eligRate }}% {{ $tr['kpi_eligibility_sub'] }}</div>
        </div>
        <div class="kpi">
            <div class="kpi-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E8001C"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg></div>
            <div class="kpi-n">{{ $fmt($rdv) }}</div>
            <div class="kpi-l">{{ $tr['rdv_clicked'] }}</div>
            <div class="kpi-sub">{{ $convRate }}% {{ $tr['kpi_conversion_sub'] }}</div>
        </div>
        <div class="kpi kpi-alt">
            <div class="kpi-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E8001C"
                    stroke-width="2.5" stroke-linecap="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg></div>
            <div class="kpi-n">{{ $e2eRate }}<span style="font-size:24px;opacity:.6">%</span></div>
            <div class="kpi-l">{{ $tr['kpi_e2e'] }}</div>
            <div class="kpi-sub">{{ $fmt($qs) }} → {{ $fmt($rdv) }}</div>
        </div>
    </div>

    <div class="body">

        <div class="col">

            <div class="blk">
                <div class="sh">{{ $tr['section_company'] }}</div>
                <div class="sb">
                    @if ($entreprise['contact_name'])
                        <div class="ir"><span class="il">{{ $tr['contact_name'] }}</span><span
                                class="iv">{{ $entreprise['contact_name'] }}</span></div>
                    @endif
                    @if ($entreprise['contact_email'])
                        <div class="ir"><span class="il">{{ $tr['contact_email'] }}</span><span
                                class="iv" style="font-size:9px">{{ $entreprise['contact_email'] }}</span></div>
                    @endif
                    @if ($emp)
                        <div class="ir"><span class="il">{{ $tr['employee_count'] }}</span><span
                                class="iv">{{ $fmt($emp) }}<em> {{ $tr['employees'] }}</em></span></div>
                    @endif
                    <div class="ir"><span class="il">{{ $tr['generated_at'] }}</span><span
                            class="iv">{{ $generatedAt }}</span></div>
                </div>
            </div>
            <div class="hsep"></div>

            @php
                $rateRows = [
                    [
                        'label' => $tr['participation_rate'],
                        'val' => $partRate,
                        'note' => $fmt($qs) . ' / ' . $fmt($emp) . ' ' . $tr['employees'],
                        'reco' =>
                            $partRate < 25
                                ? $tr['reco_part_low']
                                : ($partRate < 50
                                    ? $tr['reco_part_med']
                                    : $tr['reco_part_high']),
                    ],
                    [
                        'label' => $tr['eligibility_rate'],
                        'val' => $eligRate,
                        'note' =>
                            $fmt($el) . ' ' . $tr['eligible'] . ' / ' . $fmt($sub) . ' ' . $tr['synth_soumissions'],
                        'reco' =>
                            $eligRate < 50
                                ? $tr['reco_elig_low']
                                : ($eligRate < 70
                                    ? $tr['reco_elig_med']
                                    : $tr['reco_elig_high']),
                    ],
                    [
                        'label' => $tr['conversion_rate'],
                        'val' => $convRate,
                        'note' => $fmt($rdv) . ' ' . $tr['synth_rdv_taken'] . ' / ' . $fmt($el) . ' ' . $tr['eligible'],
                        'reco' =>
                            $convRate < 50
                                ? $tr['reco_conv_low']
                                : ($convRate < 75
                                    ? $tr['reco_conv_med']
                                    : $tr['reco_conv_high']),
                    ],
                ];
            @endphp
            <div class="blk">
                <div class="sh">{{ $tr['section_rates'] }}</div>
                <div style="padding:4px 12px">
                    @foreach ($rateRows as $r)
                        @php
                            $arc = round((min($r['val'], 100) / 100) * 125.7, 1);
                            $col = $r['val'] >= 70 ? '#16a34a' : ($r['val'] >= 50 ? '#d97706' : '#E8001C');
                        @endphp
                        <div class="gauge-item">
                            <svg class="gauge-svg" width="48" height="30" viewBox="0 0 48 30">
                                <path d="M 5 26 A 19 19 0 0 1 43 26" stroke="#E5E5E5" stroke-width="5" fill="none"
                                    stroke-linecap="round" />
                                <path d="M 5 26 A 19 19 0 0 1 43 26" stroke="{{ $col }}" stroke-width="5"
                                    fill="none" stroke-linecap="round"
                                    stroke-dasharray="{{ $arc }} 59.7"
                                    transform="rotate(180 24 26) translate(0 0)" />
                                <text x="24" y="24" text-anchor="middle" font-size="8.5" font-weight="800"
                                    fill="{{ $col }}"
                                    font-family="Cooper Hewitt,sans-serif">{{ $r['val'] }}</text>
                            </svg>
                            <div class="gauge-meta">
                                <div class="gauge-lbl">{{ $r['label'] }}</div>
                                <div class="gauge-note">{{ $r['note'] }}</div>
                                <div class="gauge-reco">→ {{ $r['reco'] }}</div>
                            </div>
                            <div class="gauge-right">
                                <div class="gval">{{ $r['val'] }}<s>%</s></div>
                                <div class="gbadge"
                                    style="background:{{ $col }}1A;color:{{ $col }}">
                                    {{ $r['val'] >= 70 ? $tr['badge_excellent'] : ($r['val'] >= 50 ? $tr['badge_bon'] : ($r['val'] >= 30 ? $tr['badge_moyen'] : $tr['badge_faible'])) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="hsep"></div>

            <div class="blk">
                <div class="sh">{{ $tr['section_diagnostics'] }}</div>
                <div style="padding:5px 12px;display:flex;flex-direction:column;gap:5px">
                    @if ($partRate < 20)
                        <div class="diag">
                            <div class="diag-ic" style="background:#FEE2E2">⚡</div>
                            <div class="diag-body">
                                <div class="diag-t">{{ str_replace(':pct', $partRate, $tr['diag_part_low_t']) }}</div>
                                <div class="diag-x">
                                    {{ str_replace([':started', ':emp'], [$fmt($qs), $fmt($emp)], $tr['diag_part_low_x']) }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="diag">
                            <div class="diag-ic" style="background:#DCFCE7">✓</div>
                            <div class="diag-body">
                                <div class="diag-t">{{ str_replace(':pct', $partRate, $tr['diag_part_ok_t']) }}</div>
                                <div class="diag-x">{{ $tr['diag_part_ok_x'] }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($eligRate >= 65)
                        <div class="diag">
                            <div class="diag-ic" style="background:#DCFCE7">✓</div>
                            <div class="diag-body">
                                <div class="diag-t">{{ str_replace(':pct', $eligRate, $tr['diag_elig_ok_t']) }}</div>
                                <div class="diag-x">{{ $tr['diag_elig_ok_x'] }}</div>
                            </div>
                        </div>
                    @else
                        <div class="diag">
                            <div class="diag-ic" style="background:#FEF9C3">○</div>
                            <div class="diag-body">
                                <div class="diag-t">{{ str_replace(':pct', $eligRate, $tr['diag_elig_low_t']) }}</div>
                                <div class="diag-x">
                                    {{ str_replace(':nonElig', round(100 - $eligRate, 1), $tr['diag_elig_low_x']) }}
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($convRate >= 70)
                        <div class="diag">
                            <div class="diag-ic" style="background:#DCFCE7">✓</div>
                            <div class="diag-body">
                                <div class="diag-t">{{ str_replace(':pct', $convRate, $tr['diag_conv_ok_t']) }}</div>
                                <div class="diag-x">
                                    {{ str_replace([':rdv', ':eligible'], [$fmt($rdv), $fmt($el)], $tr['diag_conv_ok_x']) }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="diag">
                            <div class="diag-ic" style="background:#EEF2FF">◎</div>
                            <div class="diag-body">
                                <div class="diag-t">{{ str_replace(':score', $score, $tr['diag_score_t']) }}</div>
                                <div class="diag-x">
                                    {{ str_replace([':perf', ':part', ':elig', ':conv'], [$perf, $partRate, $eligRate, $convRate], $tr['diag_score_x']) }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="hsep"></div>

            <div class="blk">
                <div class="sh">{{ $tr['section_reco'] }}</div>
                <div style="padding:5px 12px">
                    <div class="reco">
                        <div class="reco-t">{{ $tr['reco_session_title'] }}</div>
                        @if ($partRate < 25)
                            <div class="reco-row"><span class="reco-n">1.</span><span
                                    class="reco-txt">{{ $tr['reco_1_low'] }}</span></div>
                        @else
                            <div class="reco-row"><span class="reco-n">1.</span><span
                                    class="reco-txt">{{ $tr['reco_1_ok'] }}</span></div>
                        @endif
                        <div class="reco-row"><span class="reco-n">2.</span><span
                                class="reco-txt">{!! $topQ ? str_replace([':q', ':n'], ['Q' . $topQ['q'], $topQ['n']], $tr['reco_2']) : $tr['reco_2_default'] !!}</span></div>
                        <div class="reco-row"><span class="reco-n">3.</span><span
                                class="reco-txt">{!! str_replace([':part', ':elig', ':rdv'], [$fmt($potPart), $fmt($potElig), $fmt($potRdv)], $tr['reco_3']) !!}</span></div>
                    </div>
                </div>
            </div>
            <div class="hsep"></div>

            <div class="blk blk-grow">
                <div class="sh">{{ $tr['section_objectives'] }}</div>
                <div style="padding:6px 12px;flex:1">
                    <table class="ctable" style="margin-bottom:6px">
                        <tr>
                            <th>{{ $tr['obj_indicator'] }}</th>
                            <th>{{ $tr['obj_current'] }}</th>
                            <th>{{ $tr['obj_target'] }}</th>
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
                            <td>{{ $tr['obj_rdv'] }}</td>
                            <td class="td-now">{{ $fmt($rdv) }}</td>
                            <td class="td-obj">{{ $fmt($potRdv) }}</td>
                        </tr>
                    </table>
                    <div style="font-size:7px;font-weight:300;color:#bbb;font-style:italic;line-height:1.4">
                        {{ $tr['obj_note'] }}</div>
                </div>
            </div>

        </div>

        <div class="col">

            <div class="blk">
                <div class="sh">{{ $tr['section_participation'] }}</div>
                <div class="funnel-wrap" style="padding:6px 12px;min-height:0">
                    @php
                        $fw = 240;
                        $fh = 260;
                        $fsteps = [
                            [
                                'val' => $qs,
                                'label' => $tr['quiz_started'],
                                'pct' => 100,
                                'drop' => $drop1,
                                'convPct' => null,
                                'convLbl' => null,
                            ],
                            [
                                'val' => $qc,
                                'label' => $tr['quiz_completed'],
                                'pct' => round(($qc / max($qs, 1)) * 100),
                                'drop' => $drop2,
                                'convPct' => $compRate,
                                'convLbl' => $tr['funnel_completion'],
                            ],
                            [
                                'val' => $el,
                                'label' => $tr['eligible'],
                                'pct' => round(($el / max($qs, 1)) * 100),
                                'drop' => $drop3,
                                'convPct' => $eligRate,
                                'convLbl' => $tr['funnel_eligibility'],
                            ],
                            [
                                'val' => $rdv,
                                'label' => $tr['rdv_clicked'],
                                'pct' => round(($rdv / max($qs, 1)) * 100),
                                'drop' => null,
                                'convPct' => $convRate,
                                'convLbl' => $tr['funnel_conversion'],
                            ],
                        ];
                        $sH = 48;
                        $sGap = 18;
                    @endphp
                    <svg width="{{ $fw }}" height="{{ $fh }}"
                        viewBox="0 0 {{ $fw }} {{ $fh }}" style="width:100%;height:auto">
                        @foreach ($fsteps as $i => $st)
                            @php
                                $y = $i * ($sH + $sGap);
                                $w = round($fw * ($st['pct'] / 100));
                                $x = ($fw - $w) / 2;
                                $op = 1 - $i * 0.16;
                            @endphp
                            <rect x="{{ $x }}" y="{{ $y }}" width="{{ $w }}"
                                height="{{ $sH }}" rx="2" fill="#E8001C"
                                opacity="{{ $op }}" />
                            <text x="{{ $fw / 2 }}" y="{{ $y + 13 }}" text-anchor="middle"
                                fill="rgba(255,255,255,.8)" font-size="7" font-weight="300"
                                font-family="Cooper Hewitt,sans-serif"
                                text-decoration="none">{{ sprintf('%02d', $i + 1) }} · {{ $st['label'] }}</text>
                            <text x="{{ $fw / 2 }}" y="{{ $y + 32 }}" text-anchor="middle"
                                fill="white" font-size="19" font-weight="800"
                                font-family="Cooper Hewitt,sans-serif"
                                letter-spacing="-0.5">{{ $fmt($st['val']) }}</text>
                            <text x="{{ $fw / 2 }}" y="{{ $y + 45 }}" text-anchor="middle"
                                fill="rgba(255,255,255,.65)" font-size="6.5"
                                font-family="Cooper Hewitt,sans-serif">{{ $st['pct'] }}{{ $tr['funnel_pct_of_total'] }}</text>
                            @if ($st['drop'] !== null && $st['drop'] > 0)
                                @php $ay=$y+$sH; @endphp
                                <line x1="{{ $fw / 2 }}" y1="{{ $ay }}"
                                    x2="{{ $fw / 2 }}" y2="{{ $ay + $sGap - 3 }}" stroke="#DEDEDE"
                                    stroke-width="1" stroke-dasharray="3 2" />
                                <polygon
                                    points="{{ $fw / 2 }},{{ $ay + $sGap - 3 }} {{ $fw / 2 - 3 }},{{ $ay + $sGap - 8 }} {{ $fw / 2 + 3 }},{{ $ay + $sGap - 8 }}"
                                    fill="#DEDEDE" />
                                <text x="{{ $fw / 2 + 8 }}" y="{{ $ay + $sGap / 2 + 3 }}" fill="#E8001C"
                                    font-size="7" font-weight="700"
                                    font-family="Cooper Hewitt,sans-serif">−{{ $fmt($st['drop']) }}</text>
                                @if ($st['convPct'] !== null)
                                    <text x="{{ $fw / 2 - 8 }}" y="{{ $ay + $sGap / 2 + 3 }}" fill="#999"
                                        font-size="6" font-weight="300" text-anchor="end"
                                        font-family="Cooper Hewitt,sans-serif">{{ $st['convPct'] }}%</text>
                                @endif
                            @endif
                        @endforeach
                    </svg>
                </div>
            </div>
            <div class="hsep"></div>

            <div class="blk">
                <div class="sh">{{ $tr['section_behavior_dur'] }}</div>
                <div class="dur-block">
                    <svg width="70" height="70" viewBox="0 0 70 70" flex-shrink="0">
                        <circle cx="35" cy="35" r="26" fill="none" stroke="#F0F0F0"
                            stroke-width="7" />
                        <circle cx="35" cy="35" r="26" fill="none" stroke="#E8001C"
                            stroke-width="7" stroke-dasharray="{{ $durCirc }} 163.4" stroke-linecap="round"
                            transform="rotate(-90 35 35)" />
                        <text x="35" y="32" text-anchor="middle" font-size="13" font-weight="800" fill="#111"
                            font-family="Cooper Hewitt,sans-serif">{{ $dur ?? '-' }}</text>
                        <text x="35" y="45" text-anchor="middle" font-size="8" font-weight="300" fill="#999"
                            font-family="Cooper Hewitt,sans-serif">secondes</text>
                    </svg>
                    <div class="dur-text">
                        <div class="dur-lbl">{{ $tr['avg_duration'] }}</div>
                        <div
                            style="font-size:36px;font-weight:800;color:#111;letter-spacing:-1.5px;line-height:1;margin-bottom:1px">
                            {{ $dur ?? '-' }}<span style="font-size:16px;font-weight:400;color:#999">s</span></div>
                        <div class="dur-ctx">{{ $durCtx }}</div>
                    </div>
                </div>
            </div>
            <div class="hsep"></div>

            <div class="bchart" style="min-height:0;padding:5px 12px">
                <div class="sh" style="margin:0 -12px 5px;padding:4px 12px">{{ $tr['abandon_by_question'] }}
                </div>
                <div class="bc-meta">{{ $fmt($totalAb) }} {{ $tr['abandons'] }} · {{ $abs->count() }}
                    {{ $tr['abandon_questions'] }} ·
                    {{ $topQ ? str_replace(':q', 'Q' . $topQ['q'], $tr['abandon_most_blocked']) : '' }}</div>
                @if ($topQ)
                    <div class="bc-reco">→
                        {{ $topQ['q'] <= 3 ? $tr['abandon_reco_early'] : ($topQ['q'] >= 10 ? $tr['abandon_reco_late'] : $tr['abandon_reco_mid']) }}
                    </div>
                @endif
                <div class="bars">
                    @foreach ($abs->take(8) as $a)
                        @php
                            $w = round(($a['n'] / $maxAb) * 100);
                            $pctTot = round(($a['n'] / max($totalAb, 1)) * 100, 1);
                            $c =
                                $a['n'] >= $maxAb * 0.7 ? '#E8001C' : ($a['n'] >= $maxAb * 0.4 ? '#d97706' : '#94A3B8');
                        @endphp
                        <div class="brow">
                            <span class="bq">Q{{ $a['q'] }}</span>
                            <div class="btrack">
                                <div class="bfill"
                                    style="width:{{ $w }}%;background:{{ $c }}"></div>
                            </div>
                            <span class="bval">{{ $a['n'] }}</span>
                            <span class="bpct">{{ $pctTot }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="hsep"></div>

            <div class="blk">
                <div class="sh">{{ $tr['section_behavior_analysis'] }}</div>
                <div style="padding:5px 12px">
                    <div class="mini-box">
                        <div class="mini-t">{{ $tr['behavior_interp_title'] }}</div>
                        @if ($topQ && $topQ['q'] <= 3)
                            <div style="font-size:7.5px;font-weight:400;color:#333;line-height:1.6;margin-bottom:4px">
                                {{ str_replace([':q', ':n', ':total', ':pct'], ['Q' . $topQ['q'], $topQ['n'], $totalAb, round(($topQ['n'] / $totalAb) * 100)], $tr['behavior_interp_early']) }}
                            </div>
                            <div style="font-size:7px;font-weight:400;color:#888;font-style:italic;line-height:1.5">
                                {{ $tr['behavior_interp_hint_early'] }}</div>
                        @else
                            <div style="font-size:7.5px;font-weight:400;color:#333;line-height:1.6;margin-bottom:4px">
                                {{ str_replace(':q', $abs->count(), $tr['behavior_interp_other']) }}
                            </div>
                            <div style="font-size:7px;font-weight:400;color:#888;font-style:italic;line-height:1.5">
                                {{ $tr['behavior_interp_hint_other'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="hsep"></div>

            <div class="blk blk-grow">
                <div class="sh">{{ $tr['section_benchmark'] }}</div>
                <div style="padding:5px 12px;flex:1">
                    <table class="ctable">
                        <tr>
                            <th>{{ $tr['obj_indicator'] }}</th>
                            <th>{{ $shortName }}</th>
                            <th>{{ $tr['bench_sector_avg'] }}</th>
                            <th>{{ $tr['bench_top'] }}</th>
                        </tr>
                        <tr>
                            <td>{{ $tr['participation_rate'] }}</td>
                            <td class="td-now">{{ $partRate }}%</td>
                            <td style="font-size:9px;font-weight:600;color:#555">18%</td>
                            <td style="font-size:9px;font-weight:600;color:#16a34a">42%</td>
                        </tr>
                        <tr>
                            <td>{{ $tr['eligible'] }}</td>
                            <td class="td-now">{{ $eligRate }}%</td>
                            <td style="font-size:9px;font-weight:600;color:#555">63%</td>
                            <td style="font-size:9px;font-weight:600;color:#16a34a">78%</td>
                        </tr>
                        <tr>
                            <td>{{ $tr['conversion_rate'] }}</td>
                            <td class="td-now {{ $convRate >= 70 ? 'td-obj' : '' }}">{{ $convRate }}%</td>
                            <td style="font-size:9px;font-weight:600;color:#555">58%</td>
                            <td style="font-size:9px;font-weight:600;color:#16a34a">89%</td>
                        </tr>
                    </table>
                    <div
                        style="font-size:6.5px;font-weight:300;color:#bbb;font-style:italic;margin-top:5px;line-height:1.4">
                        {{ $tr['bench_note'] }}</div>
                    @if ($abs->count() < 4 || $qs < 50)
                        <div style="margin-top:8px;background:#F7F7F7;border-left:3px solid #E8001C;padding:7px 10px">
                            <div style="font-size:7.5px;font-weight:600;color:#E8001C;margin-bottom:4px">
                                {{ $tr['methodology_title'] }}</div>
                            <div style="font-size:7.5px;font-weight:400;color:#444;line-height:1.6;margin-bottom:3px">
                                {{ $tr['methodology_text'] }}</div>
                            <div style="font-size:7px;font-weight:400;color:#888;line-height:1.5">
                                {{ str_replace([':sub', ':name'], [$fmt($sub), $entreprise['name']], $tr['methodology_note']) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <div class="col">
            <div style="padding:10px 12px;display:flex;flex-direction:column;height:100%;overflow:hidden">

                <div class="d3e">{{ $dc }} · {{ date('Y') }}</div>
                <div class="d3t">{{ $tr['synth_campaign'] }}</div>
                <div class="d3p">{{ $tr['performance'] }} <strong>{{ $perf }}</strong> · <span
                        class="d3sc">{{ $score }}%</span></div>
                <div class="sbar">
                    <div class="sfill" style="width:{{ $score }}%"></div>
                </div>

                <div class="vbs">
                    <div class="vb">
                        <div class="vbt">
                            <div class="vbf" style="height:{{ min($partRate, 100) }}%"></div>
                        </div>
                        <div class="vbv">{{ $partRate }}<s>%</s></div>
                        <div class="vbl">{{ $tr['vbl_part'] }}</div>
                    </div>
                    <div class="vb">
                        <div class="vbt">
                            <div class="vbf" style="height:{{ min($eligRate, 100) }}%"></div>
                        </div>
                        <div class="vbv">{{ $eligRate }}<s>%</s></div>
                        <div class="vbl">{{ $tr['vbl_elig'] }}</div>
                    </div>
                    <div class="vb">
                        <div class="vbt">
                            <div class="vbf" style="height:{{ min($convRate, 100) }}%"></div>
                        </div>
                        <div class="vbv">{{ $convRate }}<s>%</s></div>
                        <div class="vbl">{{ $tr['vbl_conv'] }}</div>
                    </div>
                    <div class="vb">
                        <div class="vbt">
                            <div class="vbfe" style="height:{{ min($e2eRate, 100) }}%"></div>
                        </div>
                        <div class="vbv vbve">{{ $e2eRate }}<s>%</s></div>
                        <div class="vbl">{{ $tr['vbl_e2e'] }}</div>
                    </div>
                </div>
                <div class="d3sep"></div>

                <div class="f4">
                    <div class="f4i"><span class="f4n">{{ $fmt($sub) }}</span><span
                            class="f4l">{{ $tr['synth_soumissions'] }}</span><span
                            class="f4x">{{ $compRate }}% {{ $tr['kpi_completion'] }}</span></div>
                    <div class="f4i"><span class="f4n">{{ $fmt($el) }}</span><span
                            class="f4l">{{ $tr['eligible'] }}</span><span class="f4x">{{ $eligRate }}%
                            {{ $tr['kpi_eligibility_sub'] }}</span></div>
                    @if ($emp)
                        <div class="f4i"><span class="f4n">{{ $fmt($emp) }}</span><span
                                class="f4l">{{ $tr['employees'] }}</span><span
                                class="f4x">{{ $partRate }}% {{ $tr['participation_rate'] }}</span></div>
                    @endif
                    <div class="f4i"><span class="f4n">{{ $fmt($rdv) }}</span><span
                            class="f4l">{{ $tr['synth_rdv_taken'] }}</span><span
                            class="f4x">{{ $e2eRate }}% du total</span></div>
                </div>
                <div class="d3sep"></div>

                @if ($emp)
                    <div class="sim">
                        <div class="sim-t">{{ $tr['sim_title'] }}</div>

                        <div style="margin-bottom:5px">
                            <div class="sim-nums">
                                <span class="sim-now">{{ $fmt($qs) }}</span>
                                <span class="sim-arr">→</span>
                                <span class="sim-pot">{{ $fmt($potPart) }}</span>
                                <span
                                    style="font-size:7px;color:#AAA;margin-left:4px">{{ $tr['sim_participants'] }}</span>
                            </div>
                            <svg width="100%" height="12" style="display:block">
                                <rect x="0" y="0" width="100%" height="6" rx="3"
                                    fill="rgba(255,255,255,.07)" />
                                <rect x="0" y="0" width="{{ round(($qs / max($potPart, 1)) * 100) }}%"
                                    height="6" rx="3" fill="rgba(255,255,255,.25)" />
                                <rect x="0" y="6" width="100%" height="6" rx="3"
                                    fill="rgba(255,255,255,.07)" />
                                <rect x="0" y="6" width="100%" height="6" rx="3" fill="#E8001C"
                                    opacity=".7" />
                            </svg>
                        </div>

                        <div>
                            <div class="sim-nums">
                                <span class="sim-now">{{ $fmt($el) }}</span>
                                <span class="sim-arr">→</span>
                                <span class="sim-pot" style="color:#E8001C">{{ $fmt($potElig) }}</span>
                                <span
                                    style="font-size:7px;color:#AAA;margin-left:4px">{{ $tr['sim_eligible_lbl'] }}</span>
                            </div>
                            <svg width="100%" height="12" style="display:block">
                                <rect x="0" y="0" width="100%" height="6" rx="3"
                                    fill="rgba(255,255,255,.07)" />
                                <rect x="0" y="0" width="{{ round(($el / max($potElig, 1)) * 100) }}%"
                                    height="6" rx="3" fill="rgba(255,255,255,.25)" />
                                <rect x="0" y="6" width="100%" height="6" rx="3"
                                    fill="rgba(255,255,255,.07)" />
                                <rect x="0" y="6" width="100%" height="6" rx="3" fill="#E8001C" />
                            </svg>
                        </div>

                        @if ($potElig > $el)
                            <div class="sim-gain" style="margin-top:5px">
                                {{ str_replace([':gain', ':rdvGain'], [$fmt($potElig - $el), $fmt($potRdv - $rdv)], $tr['sim_gain']) }}
                            </div>
                        @endif
                        <div class="sim-note">
                            {{ str_replace([':elig', ':conv'], [$eligRate, $convRate], $tr['sim_note']) }}</div>
                    </div>
                    <div class="d3sep"></div>
                @endif

                <div class="impact">
                    <div class="impact-t">{{ $tr['impact_title'] }}</div>
                    <div class="impact-big">{{ $fmt($lives) }}</div>
                    <div class="impact-sub">{{ str_replace(':rdv', $fmt($rdv), $tr['impact_sub']) }}</div>
                    <div class="impact-eq">
                        {{ str_replace([':rdv', ':lives'], [$fmt($rdv), $fmt($lives)], $tr['impact_eq']) }}</div>
                </div>
                <div class="d3sep"></div>

                <div>
                    <div class="ratio-l">{{ $tr['ratio_title'] }}</div>
                    <div class="ratio-b">
                        <div class="ratio-f" style="width:{{ $eligRate }}%"></div>
                    </div>
                    <div class="ratio-n"><span>{{ $fmt($el) }} {{ $tr['ratio_eligible'] }}</span><span
                            class="ratio-p">{{ $eligRate }}%</span><span>{{ $fmt($sub) }}
                            {{ $tr['ratio_submitted'] }}</span></div>
                </div>

                <div class="d3sep"></div>

                <div
                    style="background:rgba(232,0,28,.08);border:1px solid rgba(232,0,28,.2);border-left:3px solid #E8001C;padding:7px 8px;margin-bottom:6px">
                    <div style="font-size:7.5px;font-weight:700;color:#E8001C;margin-bottom:4px">
                        {{ $tr['didyouknow_title'] }}</div>
                    <div style="font-size:7.5px;font-weight:400;color:#CCC;line-height:1.6;margin-bottom:3px">
                        {{ $tr['didyouknow_1'] }}</div>
                    <div style="font-size:7px;font-weight:300;color:#999;line-height:1.5">{{ $tr['didyouknow_2'] }}
                    </div>
                </div>

                <div
                    style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);padding:7px 8px;margin-bottom:6px">
                    <div style="font-size:7.5px;font-weight:600;color:#AAA;margin-bottom:4px">
                        {{ $tr['contact_title'] }}</div>
                    <div style="font-size:7.5px;font-weight:400;color:#CCC;margin-bottom:2px">
                        {{ $tr['contact_org'] }}</div>
                    <div style="display:flex;gap:14px">
                        <div>
                            <div style="font-size:7px;font-weight:400;color:#AAA;margin-bottom:1px">
                                {{ $tr['contact_email_label'] }}</div>
                            <div style="font-size:8px;font-weight:700;color:#FFF">{{ $tr['contact_email_val'] }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size:7px;font-weight:400;color:#AAA;margin-bottom:1px">
                                {{ $tr['contact_phone_label'] }}</div>
                            <div style="font-size:8px;font-weight:700;color:#FFF">{{ $tr['contact_phone_val'] }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d3ft">
                    <img src="{{ $logoBlanc }}" alt="HUG" style="height:18px;width:auto;opacity:.5">
                    <div class="d3url">{{ $tr['footer_site'] }}</div>
                </div>

            </div>
        </div>

    </div>

    <div class="rp-footer"
        style="display:flex;align-items:center;justify-content:space-between;background:#160B0B;padding:0 20px;height:40px">
        <div style="display:flex;align-items:center;gap:10px">
            <img src="{{ $logoBlanc }}" alt="HUG" style="height:22px;width:auto;opacity:.6;flex-shrink:0">
            <div>
                <div style="font-size:7.5px;font-weight:600;color:rgba(255,255,255,.5)">
                    {{ $tr['footer_hug'] }}</div>
                <div style="font-size:6.5px;font-weight:300;color:rgba(255,255,255,.25);margin-top:1px">
                    {{ $tr['footer_site'] }}</div>
            </div>
        </div>
        <div style="text-align:center">
            <div style="font-size:7px;font-weight:300;color:rgba(255,255,255,.4)">{{ $tr['footer_site'] }}</div>
        </div>
        <div style="text-align:right">
            <div style="font-size:9px;font-weight:700;color:#E8001C">{{ $dc }}</div>
            <div style="font-size:6.5px;font-weight:300;color:rgba(255,255,255,.3);margin-top:1px">
                {{ $tr['generated_at'] }} {{ $generatedAt }}</div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 500);
        });
    </script>

</body>

</html>
