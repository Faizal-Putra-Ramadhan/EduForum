<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Responsiveness Stats Card -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-2xl border border-indigo-500/10 overflow-hidden relative">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-600/10 blur-[80px] rounded-full"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center space-x-5">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                            <span class="text-2xl text-white font-black">{{ $user->lecturerScore?->total_xp ?? 0 }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">Total XP Keaktifan</h3>
                            <div class="flex items-center mt-1">
                                <span class="px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-full text-xs font-bold text-indigo-400 uppercase tracking-wider">
                                    {{ $user->responsiveness_tag }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="hidden sm:block text-right">
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Status Keaktifan</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 italic max-w-[200px]">Balas pesan secepat mungkin untuk menaikkan skor Anda!</p>
                    </div>
                </div>
            </div>
            
            <!-- Google Calendar Connection Card -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-2xl border border-blue-500/10 overflow-hidden relative">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-600/10 blur-[80px] rounded-full"></div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between relative z-10 gap-4">
                    <div class="flex items-center space-x-5">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">Google Calendar</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                @if(auth()->user()->google_token)
                                    <span class="text-green-500 font-semibold flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        Terhubung
                                    </span>
                                @else
                                    Hubungkan untuk mendapatkan pengingat balas pesan.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div>
                        @if(auth()->user()->google_token)
                            <a href="{{ route('google.auth') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-transparent rounded-xl font-bold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition ease-in-out duration-150">
                                Reconnect
                            </a>
                        @else
                            <a href="{{ route('google.auth') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-blue-700 hover:to-cyan-700 active:from-blue-800 active:to-cyan-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-lg shadow-blue-500/25">
                                Hubungkan Google Calendar
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
