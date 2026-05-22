@extends('layouts.base')
@section('title', 'Quiz — ' . $entreprise->name)
@section('page', 'QuizShow')
@php
$propsJson = json_encode([
    'entreprise' => [
        'id'            => $entreprise->id,
        'name'          => $entreprise->name,
        'slug'          => $entreprise->slug,
        'logo_url'      => $entreprise->logo_url,
        'primary_color' => $entreprise->primary_color,
    ],
    'questions'     => $questions,
    'session_token' => session('quiz_token'),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
