<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</title>
        <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --primary-color: {{ \App\Models\Setting::getVal('primary_color', '#1F6F54') }};
            }
            .bg-primary { background-color: var(--primary-color); }
            .text-primary { color: var(--primary-color); }
            .border-primary { border-color: var(--primary-color); }
            .focus-ring-primary:focus-within { outline: 2px solid var(--primary-color); outline-offset: 2px; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <footer class="text-center py-6 text-sm text-gray-500">
                Developed by <a href="http://www.neurasoft.top" target="_blank" class="text-primary hover:underline font-medium transition-colors">@NeuraSoft</a>
            </footer>
        </div>
    </body>
</html>
