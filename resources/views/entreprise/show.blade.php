@extends('layouts.base')
@section('title', $entreprise->name . ' : Donnez Votre Sang')
@section('page', 'Entreprise')
@php
$propsJson = json_encode([
    'entreprise' => [
        'id'               => $entreprise->id,
        'name'             => $entreprise->name,
        'access_token'     => $entreprise->access_token,
        'logo_url'         => $entreprise->logo_url,
        'primary_color'    => $entreprise->primary_color,
        'secondary_color'  => $entreprise->secondary_color,
        'employee_count'   => $entreprise->employee_count,
        'type'             => $entreprise->type,
        'eligible_count'   => $entreprise->eligible_count,
        'submissions_count'=> $entreprise->submissions_count,
    ],
    'collectes' => $collectes->map(fn($c) => [
        'id'        => $c->id,
        'ondoc_url' => $c->ondoc_url,
        'rdv_date'  => $c->rdv_date?->format('Y-m-d'),
        'label'     => $c->label,
        'is_active' => $c->is_active,
    ])->values()->all(),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
