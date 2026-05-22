@extends('layouts.base')
@section('page', 'Home')
@php
$propsJson = json_encode([
    'stats' => [
        'donations_count'     => $stats->donations_count,
        'lives_saved'         => $stats->lives_saved,
        'hug_hospitals_count' => $stats->hug_hospitals_count,
    ],
    'eligible_count'    => $eligibleCount,
    'entreprises_count' => $entreprisesCount,
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
