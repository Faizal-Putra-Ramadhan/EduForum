<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased selection:bg-indigo-500 selection:text-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 mesh-gradient">
            <div class="mb-8 transform hover:scale-105 transition-transform duration-300">
                <a href="/">
                    <x-application-logo class="w-auto h-12" />
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 glass shadow-2xl overflow-hidden sm:rounded-2xl transition-all duration-500 hover:shadow-indigo-500/10">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} EduForum. Built for modern learning.
                </p>
            </div>
        </div>
    </body>
</html>
