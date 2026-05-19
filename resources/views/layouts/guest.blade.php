<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EduForum') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .auth-bg {
                background-image: url('{{ asset("images/auth-bg.png") }}');
                background-size: cover;
                background-position: center;
            }
            .glass-panel {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
            .fade-in {
                animation: fadeIn 0.8s ease-out forwards;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body class="h-full antialiased bg-[#0b1120] text-gray-100 selection:bg-indigo-500/30">
        <div class="h-full flex overflow-hidden">
            <!-- Left Side: Immersive Visuals (Desktop Only) -->
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-indigo-950 auth-bg">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-950/80 via-transparent to-black/60 z-10"></div>
                
                <!-- Branding Overlay -->
                <div class="relative z-20 flex flex-col justify-between h-full p-16">
                    <div class="flex items-center space-x-3 group cursor-pointer">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-2xl group-hover:rotate-12 transition-transform duration-500">
                             <x-application-logo class="w-8 h-8 fill-indigo-600" />
                        </div>
                        <span class="text-2xl font-black tracking-tighter text-white">EduForum</span>
                    </div>

                    <div class="max-w-md fade-in">
                        <h1 class="text-5xl font-extrabold text-white leading-tight mb-6">
                            The Future of <span class="text-indigo-400">Collaborative</span> Learning
                        </h1>
                        <p class="text-lg text-gray-300 leading-relaxed font-medium mb-8">
                            Dapatkan akses tak terbatas ke diskusi akademik, grup projek, dan bimbingan dosen langsung di ujung jari Anda.
                        </p>
                        
                        <div class="flex items-center space-x-4">
                            <div class="flex -space-x-3">
                                <div class="w-10 h-10 rounded-full border-2 border-indigo-900 bg-gray-500 overflow-hidden">
                                     <img src="https://i.pravatar.cc/100?u=1" alt="user">
                                </div>
                                <div class="w-10 h-10 rounded-full border-2 border-indigo-900 bg-gray-600 overflow-hidden">
                                     <img src="https://i.pravatar.cc/100?u=2" alt="user">
                                </div>
                                <div class="w-10 h-10 rounded-full border-2 border-indigo-900 bg-gray-700 overflow-hidden">
                                     <img src="https://i.pravatar.cc/100?u=3" alt="user">
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-400 uppercase tracking-widest">Joined by 2k+ Students</span>
                        </div>
                    </div>

                    <div class="text-sm font-bold text-gray-500 uppercase tracking-[0.2em]">
                        &copy; {{ date('Y') }} EduForum Pro Evolution.
                    </div>
                </div>

                <!-- Decorative elements -->
                <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-indigo-500/20 rounded-full blur-[100px]"></div>
            </div>

            <!-- Right Side: Interaction Area -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 relative overflow-y-auto bg-[#0b1120]">
                <!-- Mobile Background Decoration -->
                <div class="lg:hidden absolute inset-0 overflow-hidden pointer-events-none -z-10">
                    <div class="absolute top-[10%] left-[10%] w-[50%] h-[50%] rounded-full bg-indigo-500/10 blur-[120px]"></div>
                    <div class="absolute bottom-[10%] right-[10%] w-[50%] h-[50%] rounded-full bg-purple-500/10 blur-[120px]"></div>
                </div>

                <div class="w-full max-w-sm fade-in" style="animation-delay: 0.2s">
                    <!-- Mobile Logo Only -->
                    <div class="lg:hidden mb-10 flex justify-center">
                        <div class="w-16 h-16 bg-white rounded-3xl flex items-center justify-center shadow-2xl">
                             <x-application-logo class="w-10 h-10 fill-indigo-600" />
                        </div>
                    </div>
                    
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

