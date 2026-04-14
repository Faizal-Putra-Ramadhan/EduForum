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
