<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $branding['site_name'] ?? config('app.name', 'ToolPass'))</title>
    @if (!empty($branding['favicon_path']))
        <link rel="icon" href="{{ Storage::url($branding['favicon_path']) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="card card-md">
                <div class="card-body">
                    @yield('content')
                </div>
            </div>
            <div class="text-center text-secondary mt-3">
                {{ $branding['footer_text'] ?? config('app.name', 'ToolPass') }} &middot; {{ date('Y') }}
            </div>
        </div>
    </div>
</body>
</html>
