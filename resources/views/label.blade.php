@extends('layouts.base')
@section('page', 'Label')
@php
$propsJson = json_encode([
    'entreprises' => $entreprises->map(fn($e) => [
        'id'             => $e->id,
        'name'           => $e->name,
        'access_token'   => $e->access_token,
        'logo_url'       => $e->logo_url,
        'primary_color'  => $e->primary_color,
        'type'           => $e->type,
        'employee_count' => $e->employee_count,
    ])->values()->all(),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
