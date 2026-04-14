<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Cari Teman & Dosen') }}
            </h2>
            <a href="{{ route('forum') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-800 border border-transparent rounded-xl font-bold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass overflow-hidden shadow-xl sm:rounded-2xl border border-white/10">
                <div class="p-8">
                    <form action="{{ route('forum.search') }}" method="GET" class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="q" value="{{ $query ?? '' }}" 
                               class="block w-full pl-12 pr-4 py-4 bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl text-lg placeholder-gray-500 dark:text-white transition-all duration-300" 
                               placeholder="Cari nama mahasiswa, dosen, atau NIDN/NIM..." autofocus>
                    </form>

                    <div class="mt-10">
                        @if($query)
                            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-6">Hasil Pencarian untuk "{{ $query }}"</h3>
                            
                            @if(count($users) > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach($users as $user)
                                        <div class="p-6 bg-white/5 border border-white/5 rounded-2xl flex flex-col items-center text-center group hover:bg-white/10 hover:border-indigo-500/30 transition-all duration-500">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover mb-4 ring-4 ring-indigo-500/10 group-hover:ring-indigo-500/30 transition-all">
                                            @else
                                                <div class="w-20 h-20 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-bold text-3xl uppercase mb-4 shadow-lg shadow-indigo-500/20">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            
                                            <h4 class="text-lg font-bold text-gray-900 dark:text-white line-clamp-1 capitalize">{{ $user->name }}</h4>
                                            <p class="text-xs font-bold text-indigo-500 uppercase tracking-widest mt-1">{{ $user->role }}</p>
                                            <p class="text-sm text-gray-500 mt-2 line-clamp-1">{{ $user->prodi ?? 'Program Studi Tidak Diketahui' }}</p>
                                            
                                            <div class="mt-6 w-full">
                                                <form action="{{ route('conversation.start') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    <button type="submit" class="w-full py-3 bg-indigo-600/10 border border-indigo-500/30 text-indigo-500 font-bold rounded-xl hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all duration-300">
                                                        Kirim Pesan
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <p class="text-gray-500">Tidak menemukan hasil untuk "{{ $query }}".</p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-20 opacity-50">
                                <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-200 dark:bg-gray-800 rounded-full mb-6">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-medium text-gray-500">Ketik nama di kolom pencarian <br> untuk mulai mengobrol.</h3>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
