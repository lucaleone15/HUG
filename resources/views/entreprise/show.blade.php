@extends('layouts.base')
@section('title', $entreprise->name . ' — Don du Sang HUG × CTS')
@section('page', 'Entreprise')
@php
$propsJson = json_encode([
    'entreprise' => [
        'id'              => $entreprise->id,
        'name'            => $entreprise->name,
        'slug'            => $entreprise->slug,
        'logo_url'        => $entreprise->logo_url,
        'primary_color'   => $entreprise->primary_color,
        'secondary_color' => $entreprise->secondary_color,
        'employee_count'  => $entreprise->employee_count,
        'type'            => $entreprise->type,
    ],
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
