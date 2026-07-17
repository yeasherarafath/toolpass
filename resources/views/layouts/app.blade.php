<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $branding['site_name'] ?? config('app.name', 'ToolPass'))</title>
    @if (!empty($branding['site_description']))
        <meta name="description" content="{{ $branding['site_description'] }}">
    @endif
    @if (!empty($branding['site_keywords']))
        <meta name="keywords" content="{{ $branding['site_keywords'] }}">
    @endif
    @if (!empty($branding['favicon_path']))
        <link rel="icon" href="{{ Storage::url($branding['favicon_path']) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="layout-fluid">
    <div class="page">
        <div class="page-wrapper">
            @hasSection('header')
                <div class="page-header d-print-none">
                    <div class="container-xl">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                @yield('header')
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>

            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            {{ $branding['footer_text'] ?? config('app.name', 'ToolPass') }} &middot; {{ date('Y') }}
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
