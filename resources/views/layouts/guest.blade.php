<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <body class="font-sans text-gray-900 antialiased selection:bg-emerald-500 selection:text-white overflow-hidden">
        <div class="min-h-screen flex">
            <!-- Left Side: Immersive Visual -->
            <div class="hidden lg:flex lg:w-1/2 relative bg-emerald-950 overflow-hidden">
                <div class="absolute inset-0 z-10 bg-gradient-to-tr from-emerald-950 via-emerald-900/50 to-transparent"></div>
                <img src="{{ asset('images/login_backdrop.png') }}" alt="Academic Backdrop" class="absolute inset-0 w-full h-full object-cover transform scale-110 animate-slow-zoom">
                
                <div class="relative z-20 flex flex-col justify-between p-16 w-full">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
                            <x-application-logo class="w-6 h-6 text-emerald-600" />
                        </div>
                        <span class="text-2xl font-black text-white tracking-tight italic">EduForum</span>
                    </div>

                    <div class="max-w-xl">
                        <h1 class="text-6xl font-black text-white leading-tight mb-6">
                            Portal Komunikasi <span class="text-emerald-400">Akademik</span> UAD.
                        </h1>
                        <p class="text-lg text-emerald-100/70 leading-relaxed font-medium">
                            Wadah interaktif bagi Dahlan Muda dan Bapak/Ibu Dosen untuk berdiskusi, bertukar pikiran, dan berkolaborasi secara real-time demi kelancaran akademik.
                        </p>
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="flex -space-x-3">
                            @for($i=0; $i<4; $i++)
                                <div class="w-10 h-10 rounded-full border-2 border-emerald-900 bg-emerald-800 flex items-center justify-center text-[10px] font-bold text-emerald-100 uppercase tracking-tighter">
                                    {{ chr(65 + rand(0, 25)) }}
                                </div>
                            @endfor
                        </div>
                        <p class="text-sm text-emerald-200/50 font-semibold tracking-wide">Bergabunglah bersama sivitas akademika Universitas Ahmad Dahlan.</p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-slate-950 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-600/10 blur-[120px] rounded-full -mr-48 -mt-48 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-900/10 blur-[120px] rounded-full -ml-48 -mb-48 animate-pulse"></div>

                <div class="w-full max-w-md relative z-10">
                    <div class="lg:hidden mb-12 flex justify-center">
                        <div class="w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-emerald-600/20">
                            <x-application-logo class="w-10 h-10 text-white" />
                        </div>
                    </div>
                    
                    <div class="glass p-10 rounded-4xl border border-emerald-900/10">
                        {{ $slot }}
                    </div>
                    
                    <div class="mt-10 text-center">
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-[0.2em] opacity-0">
                            <!-- spacer -->
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <style>
            @keyframes slow-zoom {
                0% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
            .animate-slow-zoom {
                animation: slow-zoom 20s ease-in-out infinite alternate;
            }
        </style>
    </body>
</html>
