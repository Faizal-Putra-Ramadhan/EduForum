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
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-white selection:bg-emerald-500/30 selection:text-emerald-200" x-data="{ globalGroupModalOpen: false }">
        <div class="min-h-screen bg-slate-950 flex overflow-hidden">
            <!-- Global Background Elements -->
            <div class="fixed inset-0 z-0 pointer-events-none">
                <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-600/5 blur-[120px] rounded-full -mr-64 -mt-64"></div>
                <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-emerald-900/5 blur-[120px] rounded-full -ml-64 -mb-64"></div>
                <div class="absolute inset-0 mesh-gradient opacity-40"></div>
            </div>

            <!-- Navigation Sidebar -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col relative z-10 overflow-hidden">
                @isset($header)
                    <header class="py-6 px-8 border-b border-white/5 bg-slate-950/20 backdrop-blur-md">
                        <div class="max-w-7xl mx-auto uppercase tracking-widest">
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
