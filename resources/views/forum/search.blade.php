<x-app-layout>
    <div class="p-8 md:p-12 min-h-screen">
        <div class="max-w-6xl mx-auto">
            <!-- Back Action -->
            <div class="mb-12 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-white uppercase tracking-tight">Sync <span class="text-emerald-500">Node</span></h1>
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-[0.3em] mt-1">Academic Peer & Expert Discovery</p>
                </div>
                <a href="{{ route('forum') }}" class="group flex items-center space-x-3 px-6 py-3 bg-white/5 border border-white/5 rounded-2xl text-[10px] font-black text-gray-400 uppercase tracking-widest hover:bg-emerald-600 hover:text-white hover:border-emerald-500 hover:shadow-2xl hover:shadow-emerald-600/20 transition-all active:scale-95">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Terminal</span>
                </a>
            </div>

            <!-- Search Console -->
            <div class="glass p-2 rounded-[32px] mb-16 shadow-2xl border-white/5">
                <form action="{{ route('forum.search') }}" method="GET" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none text-gray-500 group-focus-within:text-emerald-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ $query ?? '' }}" 
                           class="block w-full pl-20 pr-8 py-6 bg-white/5 border-none focus:ring-0 rounded-[28px] text-xl font-bold placeholder-gray-600 text-white transition-all" 
                           placeholder="Scan for name, ID, or department..." autofocus>
                </form>
            </div>

            <!-- Results Engine -->
            <div>
                @if($query)
                    <div class="flex items-center space-x-4 mb-10">
                        <div class="h-px flex-1 bg-gradient-to-r from-transparent via-emerald-500/20 to-transparent"></div>
                        <h3 class="text-[10px] font-black text-emerald-500/60 uppercase tracking-[0.5em]">Transmissions Found</h3>
                        <div class="h-px flex-1 bg-gradient-to-r from-transparent via-emerald-500/20 to-transparent"></div>
                    </div>
                    
                    @if(count($users) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($users as $user)
                                <div class="glass p-8 rounded-[40px] border-white/5 text-center group hover:border-emerald-500/30 hover:bg-emerald-600/[0.02] transition-all duration-700 relative overflow-hidden flex flex-col items-center">
                                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/0 via-transparent to-emerald-600/0 group-hover:from-emerald-600/10 group-hover:to-emerald-600/5 transition-all duration-700"></div>
                                    
                                        <div class="relative mb-6">
                                            <div class="w-24 h-24 rounded-[32px] bg-emerald-600/20 border-2 border-emerald-500/20 flex items-center justify-center text-emerald-400 font-black text-4xl uppercase group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-2xl">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-slate-950 rounded-full flex items-center justify-center border border-white/10 group-hover:scale-125 transition-transform">
                                                <div class="w-3 h-3 rounded-full {{ $user->role === 'dosen' ? "bg-emerald-500 shadow-[0_0_10px_theme('colors.emerald.400')]" : 'bg-blue-500' }}"></div>
                                            </div>
                                        </div>
                                    
                                    <h4 class="text-xl font-black text-white uppercase tracking-tight group-hover:text-emerald-400 transition-colors leading-tight">{{ $user->name }}</h4>
                                    <p class="text-[9px] font-black text-emerald-500/60 uppercase tracking-[0.3em] mt-2">{{ $user->role }} profile</p>
                                    
                                    <div class="mt-4 px-4 py-1.5 bg-white/5 rounded-xl border border-white/5 group-hover:border-emerald-500/10 transition-all">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest truncate">{{ $user->prodi ?? 'General Academics' }}</p>
                                    </div>
                                    
                                    <div class="mt-10 w-full relative z-10">
                                        <form action="{{ route('conversation.start') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-black rounded-2xl shadow-xl shadow-emerald-900/20 hover:bg-emerald-500 hover:-translate-y-1 transition-all active:scale-95 text-[11px] uppercase tracking-[0.2em]">
                                                Initialize Sync
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-24 glass rounded-[40px] border-white/5">
                            <div class="w-20 h-20 bg-white/5 border border-white/5 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-600">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <p class="text-gray-500 font-bold uppercase tracking-widest">No matching frequency found.</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-32 opacity-20 group">
                        <div class="inline-flex items-center justify-center w-32 h-32 bg-white/5 border border-white/5 rounded-[40px] mb-8 group-hover:scale-110 group-hover:rotate-12 transition-transform duration-1000">
                            <svg class="w-16 h-16 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-gray-500 uppercase tracking-[0.4em]">Ready for Sync</h3>
                        <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-4">Enter a node identity to begin data exchange.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
