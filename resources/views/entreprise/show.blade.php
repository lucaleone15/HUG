@extends('layouts.base')
@section('title', $entreprise->name . ' : Donnez Votre Sang')
@section('page', 'Entreprise')
@php
$propsJson = json_encode([
    'entreprise' => [
        'id'              => $entreprise->id,
        'name'            => $entreprise->name,
        'access_token'    => $entreprise->access_token,
        'logo_url'        => $entreprise->logo_url,
        'primary_color'   => $entreprise->primary_color,
        'secondary_color' => $entreprise->secondary_color,
        'employee_count'  => $entreprise->employee_count,
        'type'            => $entreprise->type,
        'rdv_url'          => $entreprise->rdv_url,
        'rdv_date'         => $entreprise->rdv_date?->format('Y-m-d'),
        'eligible_count'   => $entreprise->eligible_count,
        'submissions_count'=> $entreprise->submissions_count,
    ],
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
