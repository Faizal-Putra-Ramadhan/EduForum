<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EduForum') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-[#f8fafc] dark:bg-[#0b1120] selection:bg-indigo-500/30">
        <div class="min-h-screen relative">
            <!-- Subtle Background Gradients -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none -z-10">
                <div class="absolute -top-[40%] -left-[20%] w-[70%] h-[70%] rounded-full bg-indigo-500/10 blur-[120px] mix-blend-screen dark:bg-indigo-600/10"></div>
                <div class="absolute -bottom-[20%] -right-[10%] w-[60%] h-[60%] rounded-full bg-blue-500/10 blur-[100px] mix-blend-screen dark:bg-blue-600/10"></div>
            </div>
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/70 dark:bg-[#111827]/70 backdrop-blur-md sticky top-0 z-40 border-b border-gray-200/50 dark:border-white/5 transition-all shadow-sm">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

                <main class="flex-1 relative overflow-hidden">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
