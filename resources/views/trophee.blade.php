@extends('layouts.base')
@section('page', 'Trophee')
@php
$propsJson = json_encode([
    'winners' => $winners->map(fn($e) => [
        'id'            => $e->id,
        'name'          => $e->name,
        'access_token'  => $e->access_token,
        'logo_url'      => $e->logo_url,
        'primary_color' => $e->primary_color,
        'trophy_rank'   => $e->trophy_rank,
        'type'          => $e->type,
    ])->values()->all(),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
