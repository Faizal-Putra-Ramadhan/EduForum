<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between" x-data="{ openModal: false }">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Forum & Chat') }}
            </h2>
            <div class="flex items-center space-x-3">
                <button @click="openModal = true" class="inline-flex items-center px-4 py-2 bg-indigo-600/10 border border-indigo-500/20 rounded-xl font-bold text-xs text-indigo-400 uppercase tracking-widest hover:bg-indigo-600/20 active:bg-indigo-700/30 transition shadow-sm font-sans">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Buat Grup
                </button>
                <a href="{{ route('forum.search') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 transition shadow-lg shadow-indigo-500/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Chat Baru
                </a>
            </div>

            <!-- Global Group Creation Modal -->
            <template x-if="openModal">
                <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div @click="openModal = false" class="fixed inset-0 transition-opacity bg-gray-900/80 backdrop-blur-sm" aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform glass sm:my-8 sm:align-middle sm:max-w-lg sm:w-full rounded-3xl border border-white/10 shadow-2xl">
                            <form action="{{ route('group.create') }}" method="POST">
                                @csrf
                                <div class="px-6 py-8">
                                    <h3 class="text-2xl font-black text-white mb-6 uppercase tracking-tight">Buat Grup Baru</h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 ml-1">Nama Grup</label>
                                            <input type="text" name="name" required class="block w-full px-4 py-3 bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-white placeholder-gray-500 transition-all font-sans" placeholder="Contoh: Projek Skripsi IT">
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 ml-1">Pilih Anggota</label>
                                            <div class="max-h-60 overflow-y-auto space-y-2 pr-2 scrollbar-thin scrollbar-thumb-indigo-500">
                                                @foreach($allUsers as $u)
                                                <label class="flex items-center p-3 bg-white/5 rounded-xl border border-transparent hover:border-indigo-500/30 cursor-pointer transition-all group">
                                                    <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="w-5 h-5 rounded-md border-gray-700 bg-gray-800 text-indigo-600 focus:ring-indigo-600 transition-all">
                                                    <div class="ml-3 flex items-center">
                                                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xs uppercase group-hover:scale-110 transition-transform">
                                                            {{ substr($u->name, 0, 1) }}
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-bold text-white leading-none">{{ $u->name }}</p>
                                                            <p class="text-[10px] text-gray-500 uppercase tracking-tighter mt-1">{{ $u->role }} • {{ $u->prodi }}</p>
                                                        </div>
                                                    </div>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-6 py-6 bg-white/5 flex items-center justify-between border-t border-white/5">
                                    <button type="button" @click="openModal = false" class="px-6 py-2.5 text-sm font-bold text-gray-400 hover:text-white transition-colors uppercase tracking-widest">Batal</button>
                                    <button type="submit" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-xl shadow-lg shadow-indigo-500/20 transform active:scale-95 transition-all text-xs uppercase tracking-widest">Buat Sekarang</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass overflow-hidden shadow-xl sm:rounded-3xl border border-white/10">
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
                        <div class="divide-y divide-white/5">
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
                                <a href="{{ route('conversation.show', $conversation->id) }}" class="flex items-center p-5 hover:bg-white/5 transition-all duration-300 group rounded-2xl mb-2 border border-transparent hover:border-white/5">
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

                                    <div class="ml-4 opacity-0 group-hover:opacity-100 transition-all translate-x-2 group-hover:translate-x-0 hidden md:block">
                                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
