<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between" x-data="{ openModal: false }">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Forum & Chat') }}
            </h2>
            <div class="flex items-center space-x-3">
                <button @click="openModal = true" class="inline-flex items-center px-4 py-2 bg-white dark:bg-[#1f2937] border border-gray-200 dark:border-gray-700 rounded-full font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-50 dark:hover:bg-[#374151] active:scale-95 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    New Group
                </button>
                <a href="{{ route('forum.search') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-wider hover:bg-indigo-500 active:scale-95 transition-all shadow-md shadow-indigo-500/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Start Chat
                </a>
            </div>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#111827] overflow-hidden shadow-sm sm:rounded-3xl border border-gray-200/60 dark:border-white/5">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($conversations->isEmpty())
                        <div class="text-center py-20">
                            <div class="inline-flex items-center justify-center w-24 h-24 bg-indigo-600/10 rounded-3xl mb-6 transform -rotate-12">
                                <svg class="w-12 h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Mulai Jalin Komunikasi</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-3 max-w-sm mx-auto leading-relaxed">Kolaborasi dan diskusi akademik dimulai di sini. Gunakan tombol di atas untuk membuat chat private atau grup.</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-gray-800/60 space-y-2">
                            @foreach($conversations as $conversation)
                                @php
                                    if ($conversation->is_group) {
                                        $displayTitle = $conversation->displayName;
                                        $displaySub = $conversation->conversationUsers->count() . ' Anggota';
                                        $isGroup = true;
                                        $avatarChar = substr($displayTitle, 0, 1);
                                        $badgeColor = 'bg-purple-600/10 text-purple-400 border-purple-500/20';
                                        $otherUser = null;
                                    } else {
                                        $otherMapping = $conversation->conversationUsers->where('user_id', '!=', auth()->id())->first();
                                        $otherUser = $otherMapping->user ?? null;
                                        $displayTitle = $otherUser->name ?? 'User Terhapus';
                                        $displaySub = ($otherUser->role ?? 'N/A') . ' • ' . ($otherUser->prodi ?? 'N/A');
                                        $isGroup = false;
                                        $avatarChar = substr($displayTitle, 0, 1);
                                        $badgeColor = 'bg-indigo-600/10 text-indigo-400 border-indigo-500/20';
                                    }
                                @endphp
                                <a href="{{ route('conversation.show', $conversation->id) }}" class="flex items-center p-5 bg-transparent hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors duration-300 group rounded-2xl border border-transparent">
                                    <div class="relative">
                                        @if(!$isGroup && $otherUser && $otherUser->avatar)
                                            <img src="{{ asset('storage/' . $otherUser->avatar) }}" alt="{{ $displayTitle }}" class="w-14 h-14 rounded-2xl object-cover border-2 border-indigo-500/20 group-hover:scale-105 transition-transform">
                                        @else
                                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-black text-xl uppercase shadow-lg {{ $isGroup ? 'bg-gradient-to-br from-purple-600 to-indigo-700' : 'bg-indigo-600' }} group-hover:rotate-6 transition-transform">
                                                {{ $avatarChar }}
                                            </div>
                                        @endif
                                        @if(!$isGroup)
                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-[#161b22] rounded-full shadow-lg"></div>
                                        @endif
                                    </div>
                                    
                                    <div class="ml-5 flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-indigo-400 transition-colors truncate">
                                                {{ $displayTitle }}
                                                <span class="ml-2 px-2.5 py-0.5 border rounded-full text-[9px] uppercase tracking-widest font-black {{ $badgeColor }}">
                                                    {{ $isGroup ? 'GROUP' : ($otherUser->role ?? 'N/A') }}
                                                </span>
                                            </h4>
                                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-tight ml-4 flex-shrink-0">
                                                {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans(null, true) : $conversation->created_at->diffForHumans(null, true) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between mt-1">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate pr-4 italic">
                                                {{ $displaySub }}
                                            </p>
                                            @if($conversation->unread_count > 0)
                                                <div class="bg-indigo-600 text-white text-[10px] font-black w-5 h-5 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-500/40">
                                                    {{ $conversation->unread_count }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                <div class="group">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-1">Member Selection</label>
                                    <div class="max-h-60 overflow-y-auto space-y-2 pr-2 scrollbar-thin scrollbar-thumb-emerald-600/50 scrollbar-track-transparent">
                                        @foreach($allUsers as $u)
                                        <label class="flex items-center p-3 bg-white/5 rounded-2xl border border-transparent hover:border-emerald-500/30 hover:bg-emerald-500/5 cursor-pointer transition-all group/member">
                                            <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="w-5 h-5 rounded-lg border-white/10 bg-slate-900 text-emerald-600 focus:ring-emerald-500 transition-all">
                                            <div class="ml-4 flex items-center">
                                                <div class="w-10 h-10 rounded-xl bg-emerald-600/20 border border-emerald-500/20 flex items-center justify-center text-emerald-400 font-black text-xs uppercase group-hover/member:scale-110 transition-transform">
                                                    {{ substr($u->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4 text-left">
                                                    <p class="text-sm font-black text-white leading-none">{{ $u->name }}</p>
                                                    <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest mt-1.5">{{ $u->role }} • {{ $u->prodi }}</p>
                                                </div>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-10 py-8 bg-white/5 flex items-center justify-between border-t border-white/5">
                            <button type="button" @click="globalGroupModalOpen = false" class="text-xs font-black text-gray-500 hover:text-white transition-colors uppercase tracking-widest">Cancel</button>
                            <button type="submit" class="px-10 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl shadow-xl shadow-emerald-600/20 transform active:scale-95 transition-all text-[10px] uppercase tracking-widest">Initialize Group</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <div class="mt-8 relative z-0">
        @if($conversations->isEmpty())
            <div class="glass py-24 px-10 text-center rounded-[40px] border border-white/5 max-w-2xl mx-auto">
                <div class="inline-flex items-center justify-center w-28 h-28 bg-emerald-600/10 rounded-[40px] mb-10 transform -rotate-12 emerald-glow border border-emerald-500/20">
                    <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-4xl font-black text-white uppercase tracking-tight mb-4">Start Sharing <br><span class="text-emerald-500">Knowledge</span></h3>
                <p class="text-gray-400 font-medium leading-relaxed mb-10">Your academic forum is currently silent. Bridge the gap between students and lecturers in a secure workspace.</p>
                <div class="flex justify-center">
                    <a href="{{ route('forum.search') }}" class="px-12 py-5 bg-emerald-600 rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:bg-emerald-500 transition-all shadow-2xl shadow-emerald-600/20">Find Peers</a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Main Dashboard Column -->
                <div class="lg:col-span-8 space-y-12">
                    <!-- Section Grup -->
                    @php
                        $groupConvs = $conversations->filter(fn($c) => $c->type === 'group');
                    @endphp
                    @if($groupConvs->isNotEmpty())
                    <div class="animate-fade-in-up">
                        <div class="flex items-center justify-between mb-6 px-2">
                            <h5 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.4em]">Group Collaborations</h5>
                            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-black rounded-full">{{ $groupConvs->count() }} ACTIVE</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($groupConvs as $conversation)
                                <x-conversation-list-card :conversation="$conversation" />
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Section Mahasiswa -->
                    @php
                        $mahasiswaConvs = $conversations->filter(function($c) {
                            if ($c->type === 'group') return false;
                            $other = $c->conversationUsers->where('user_id', '!=', auth()->id())->first()?->user;
                            return $other && $other->role === 'mahasiswa';
                        });
                    @endphp
                    @if($mahasiswaConvs->isNotEmpty())
                    <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                        <div class="flex items-center justify-between mb-6 px-2">
                            <h5 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.4em]">Peer Discussions</h5>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($mahasiswaConvs as $conversation)
                                <x-conversation-list-card :conversation="$conversation" />
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar Desktop Column -->
                <div class="lg:col-span-4 space-y-10 animate-fade-in-up" style="animation-delay: 0.2s">
                    <!-- Academic Colleagues (Dosen) -->
                    @php
                        $dosenConvs = $conversations->filter(function($c) {
                            if ($c->type === 'group') return false;
                            $other = $c->conversationUsers->where('user_id', '!=', auth()->id())->first()?->user;
                            return $other && $other->role === 'dosen';
                        });
                    @endphp
                    @if($dosenConvs->isNotEmpty())
                    <div class="glass p-8 rounded-[40px] border border-white/5 ring-1 ring-white/5">
                        <h5 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] mb-6">Expert Consultations</h5>
                        <div class="space-y-4">
                            @foreach($dosenConvs as $conversation)
                                <x-conversation-list-card :conversation="$conversation" class="!bg-white/[0.03] hover:!bg-emerald-500/10 border-transparent hover:!border-emerald-500/20" />
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Quick Stats Component -->
                    <div class="glass p-8 rounded-[40px] border border-emerald-500/10 bg-emerald-500/[0.02]">
                        <h5 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-6">Academic Metrics</h5>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-white/5 rounded-2xl">
                                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Messages</p>
                                <p class="text-2xl font-black text-white">{{ Auth::user()->score->message_count ?? 0 }}</p>
                            </div>
                            <div class="p-4 bg-white/5 rounded-2xl">
                                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">XP Points</p>
                                <p class="text-2xl font-black text-emerald-500">{{ Auth::user()->score->total_xp ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>
</x-app-layout>
