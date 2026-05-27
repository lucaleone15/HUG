<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Don du Sang — HUG × CTS')</title>
    <link rel="icon" href="/images/hug-favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/images/hug-favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app" data-page="@yield('page', '')"></div>
    <script id="page-props" type="application/json">{!! $propsJson ?? '{}' !!}</script>
</body>
</html>