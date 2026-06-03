@extends('layouts.base')
@section('title', 'Résultat : ' . $entreprise->name)
@section('page', 'QuizResult')
@php
$propsJson = json_encode([
    'entreprise' => [
        'id'            => $entreprise->id,
        'name'          => $entreprise->name,
        'access_token'  => $entreprise->access_token,
        'primary_color' => $entreprise->primary_color,
        'rdv_url'       => $entreprise->rdv_url,
        'rdv_date'      => $entreprise->rdv_date?->format('Y-m-d'),
    ],
    'submission' => [
        'is_eligible'              => $submission->is_eligible,
        'needs_evaluation'         => $needsEvaluation,
        'disqualification_reasons' => $disqualificationReasons,
        'completed_at'             => $submission->completed_at?->toIso8601String(),
    ],
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
