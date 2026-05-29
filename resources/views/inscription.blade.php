@extends('layouts.base')
@section('page', 'Inscription')
@php
$propsJson = json_encode([
    'success' => session('success') === true,
    'errors'  => $errors->toArray(),
    'old'     => [
        'name'            => old('name'),
        'type'            => old('type'),
        'employee_count'  => old('employee_count'),
        'contact_name'    => old('contact_name'),
        'contact_email'   => old('contact_email'),
        'primary_color'   => old('primary_color'),
        'secondary_color' => old('secondary_color', ''),
        'logo_url'        => old('logo_url'),
        'wants_trophy'    => old('wants_trophy'),
    ],
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
