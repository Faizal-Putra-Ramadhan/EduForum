<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#111827] overflow-hidden shadow-sm sm:rounded-3xl border border-gray-200/60 dark:border-white/5">
                <div class="p-8 text-gray-900 dark:text-gray-100 flex items-center justify-center min-h-[200px]">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-indigo-500/10 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ __("You're logged in!") }}</h3>
                        <p class="text-gray-500 text-sm">Selamat datang di EduForum. Silakan buka menu Forum untuk mulai berdiskusi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
