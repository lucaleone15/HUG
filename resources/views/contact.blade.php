@extends('layouts.base')
@section('page', 'Contact')
@php
$propsJson = json_encode([
    'success' => session('success'),
    'errors'  => $errors->toArray(),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
