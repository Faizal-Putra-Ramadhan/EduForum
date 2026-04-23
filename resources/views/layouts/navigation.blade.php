<aside x-data="{ open: true, mobileOpen: false }" 
       class="relative z-50 flex flex-col bg-slate-950 border-r border-white/5 transition-all duration-300"
       :class="open ? 'w-80' : 'w-24'">
    
    <!-- Mobile Toggle -->
    <div class="lg:hidden fixed top-6 right-6 z-[100]">
        <button @click="mobileOpen = !mobileOpen" class="p-3 bg-emerald-600 rounded-xl shadow-lg shadow-emerald-600/20 text-white">
            <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Sidebar Brand -->
    <div class="px-8 flex items-center h-24 overflow-hidden shrink-0">
        <div class="flex items-center group cursor-pointer transition-transform duration-500 hover:scale-[1.02]">
            <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-600/20 group-hover:shadow-emerald-500/40 transition-all">
                <x-application-logo class="w-6 h-6 text-white" />
            </div>
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 -translate-x-4" 
                 x-transition:enter-end="opacity-100 translate-x-0" 
                 class="ml-4">
                <h1 class="text-[15px] font-black text-white tracking-[0.2em] uppercase italic leading-none">EduForum</h1>
                <p class="text-[7px] font-bold text-emerald-500/60 uppercase tracking-[0.3em] mt-1">Academic Node</p>
            </div>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 px-4 space-y-2 py-6 overflow-y-auto scrollbar-none">
        <div class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-4 px-4" x-show="open">Main Menu</div>
        
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="m10.29 3.86 8 8L10.29 19.86M20 20H4" open="open">
            Dashboard
        </x-sidebar-link>

        <x-sidebar-link :href="route('forum')" :active="request()->routeIs('forum*')" icon="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" open="open">
            Forum & Chat
        </x-sidebar-link>

        <div class="pt-8 text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-4 px-4" x-show="open">Academia</div>
        
        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile*')" icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" open="open">
            My Profile
        </x-sidebar-link>
    </nav>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-white/5 space-y-4 shrink-0 bg-white/5">
        <!-- XP Progress -->
        <div x-show="open" class="glass p-4 rounded-2xl relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Level Progression</span>
                <span class="text-[10px] font-black text-white italic tracking-tighter">{{ Auth::user()->lecturerScore->total_xp ?? 0 }} XP</span>
            </div>
            <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full emerald-glow shadow-[0_0_10px_theme('colors.emerald.400')] transition-all duration-1000" style="width: {{ min((Auth::user()->lecturerScore->total_xp ?? 0), 100) }}%"></div>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <p class="text-[8px] text-gray-500 font-bold uppercase tracking-[0.2em]">{{ Auth::user()->responsiveness_tag }}</p>
                <div class="flex space-x-0.5">
                    <div class="w-1 h-1 rounded-full bg-emerald-500/20 group-hover:bg-emerald-500 transition-colors"></div>
                    <div class="w-1 h-1 rounded-full bg-emerald-500/10"></div>
                </div>
            </div>
        </div>

        <div class="flex items-center" :class="open ? 'justify-between' : 'justify-center'">
            <div class="flex items-center" x-show="open">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/20 border border-emerald-500/20 flex items-center justify-center text-emerald-400 font-black text-sm uppercase">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="ml-3 overflow-hidden">
                    <p class="text-xs font-black text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tighter">{{ Auth::user()->role }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-3 text-gray-500 hover:text-emerald-500 hover:bg-emerald-500/10 rounded-xl transition-all group">
                    <svg class="w-6 h-6 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
