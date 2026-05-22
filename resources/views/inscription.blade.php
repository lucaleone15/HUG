@extends('layouts.base')
@section('page', 'Inscription')
@php
$propsJson = json_encode([
    'success' => session('success') === true,
    'errors'  => $errors->toArray(),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
