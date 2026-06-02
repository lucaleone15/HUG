<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
@php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    $cssFile  = collect($manifest)->firstWhere(fn($v) => str_ends_with($v['file'] ?? '', '.css'));
    $cssPath  = $cssFile ? public_path('build/' . $cssFile['file']) : null;
@endphp
{!! $cssPath && file_exists($cssPath) ? file_get_contents($cssPath) : '' !!}
</style>
</head>
<body>
@yield('content')
</body>
</html>
