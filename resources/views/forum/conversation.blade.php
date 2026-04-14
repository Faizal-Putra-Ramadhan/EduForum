<x-app-layout>
    <div class="flex h-[calc(100vh-64px)] overflow-hidden bg-gray-50 dark:bg-[#0b0d17]">
        <!-- Sidebar - Conversation List -->
        <div class="hidden md:flex flex-col w-1/3 lg:w-1/4 border-r border-white/5 bg-white/5 backdrop-blur-xl" x-data="{ openMenu: false, openGroupModal: false }">
            <div class="p-6 border-b border-white/5 flex items-center justify-between relative">
                <h3 class="font-bold text-xl text-gray-900 dark:text-white uppercase tracking-tight">Messages</h3>
                
                <div class="relative">
                    <button @click="openMenu = !openMenu" class="p-2 bg-indigo-600 rounded-lg text-white hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-500/30 active:scale-95">
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-45': openMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="openMenu" 
                         @click.away="openMenu = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 rounded-2xl bg-[#161b22] border border-white/10 shadow-2xl z-[110] overflow-hidden py-1">
                        <button @click="openGroupModal = true; openMenu = false" class="w-full text-left px-4 py-3 text-xs font-bold text-gray-300 hover:bg-white/5 hover:text-white transition-colors flex items-center group">
                            <div class="w-8 h-8 rounded-lg bg-purple-600/10 text-purple-400 group-hover:bg-purple-600 group-hover:text-white flex items-center justify-center mr-3 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            Buat Grup Baru
                        </button>
                        <a href="{{ route('forum.search') }}" class="w-full text-left px-4 py-3 text-xs font-bold text-gray-300 hover:bg-white/5 hover:text-white transition-colors flex items-center group">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600/10 text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center mr-3 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            Cari Nama User
                        </a>
                    </div>
                </div>

                <!-- Group Creation Modal -->
                <template x-if="openGroupModal">
                    <div class="fixed inset-0 z-[120] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div @click="openGroupModal = false" class="fixed inset-0 transition-opacity bg-gray-900/80 backdrop-blur-sm" aria-hidden="true"></div>
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
                                        <button type="button" @click="openGroupModal = false" class="px-6 py-2.5 text-sm font-bold text-gray-400 hover:text-white transition-colors uppercase tracking-widest">Batal</button>
                                        <button type="submit" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-xl shadow-lg shadow-indigo-500/20 transform active:scale-95 transition-all text-xs uppercase tracking-widest">Buat Sekarang</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="flex-1 overflow-y-auto space-y-1 p-3">
                @foreach($conversations as $conv)
                    @php
                        $isGroup = $conv->type === 'group';
                        $isActive = $conv->id == $conversation->id;
                        if ($isGroup) {
                            $displayTitle = $conv->name ?? 'Grup Tanpa Nama';
                            $avatarChar = substr($displayTitle, 0, 1);
                        } else {
                            $otherMapping = $conv->conversationUsers->where('user_id', '!=', auth()->id())->first();
                            $other = $otherMapping->user ?? null;
                            $displayTitle = $other->name ?? 'User Terhapus';
                            $avatarChar = substr($displayTitle, 0, 1);
                        }
                    @endphp
                    <a href="{{ route('conversation.show', $conv->id) }}" 
                       class="flex items-center p-3 rounded-2xl transition-all duration-300 {{ $isActive ? 'bg-indigo-600/20 border border-indigo-500/30' : 'hover:bg-white/5 border border-transparent' }}">
                        <div class="relative flex-shrink-0">
                            @if(!$isGroup && $other && $other->avatar)
                                <img src="{{ asset('storage/' . $other->avatar) }}" class="w-12 h-12 rounded-xl object-cover">
                            @else
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg uppercase shadow-lg {{ $isGroup ? 'bg-purple-600 shadow-purple-500/20' : 'bg-indigo-600 shadow-indigo-500/20' }}">
                                    {{ $avatarChar }}
                                </div>
                            @endif
                            @if(!$isGroup)
                                <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-[#161b22] rounded-full"></div>
                            @endif
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-sm text-gray-900 dark:text-gray-100 truncate {{ $isActive ? 'text-indigo-400' : '' }}">{{ $displayTitle }}</span>
                                <span class="text-[10px] text-gray-500">{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : '' }}</span>
                            </div>
                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ $isGroup ? $conv->conversationUsers->count() . ' Anggota' : 'Diskusi Akademik...' }}</p>
                        </div>
                        @if($conv->unread_count > 0 && !$isActive)
                            <div class="ml-2 w-5 h-5 bg-indigo-600 rounded-full flex items-center justify-center text-[10px] font-bold text-white">
                                {{ $conv->unread_count }}
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col relative mesh-gradient" x-data="{ openGroupMembersModal: false }">
            <!-- Chat Header -->
            @php
                $isGroup = $conversation->is_group;
                if (!$isGroup) {
                    $otherMapping = $conversation->conversationUsers->where('user_id', '!=', auth()->id())->first();
                    $currentOther = $otherMapping->user ?? null;
                }
            @endphp
            <div class="p-4 md:px-6 md:py-4 border-b border-white/5 glass flex items-center justify-between z-10">
                <div class="flex items-center">
                    <a href="{{ route('forum') }}" class="mr-4 md:hidden text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div class="relative">
                        @if(!$isGroup && $currentOther && $currentOther->avatar)
                            <img src="{{ asset('storage/' . $currentOther->avatar) }}" class="w-10 h-10 rounded-xl object-cover border border-white/10">
                        @else
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold shadow-lg {{ $isGroup ? 'bg-purple-600 shadow-purple-500/20' : 'bg-indigo-600 shadow-indigo-500/20' }}">
                                {{ substr($isGroup ? $conversation->name : ($currentOther->name ?? 'C'), 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="ml-3">
                        <h3 class="font-bold text-gray-900 dark:text-white capitalize leading-tight">
                            {{ $isGroup ? $conversation->name : ($currentOther->name ?? 'User Terhapus') }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            @if($isGroup)
                                <button @click="openGroupMembersModal = true" class="text-[10px] text-gray-500 hover:text-indigo-400 font-bold uppercase tracking-widest transition-colors flex items-center">
                                    {{ $conversation->conversationUsers->count() }} Anggota Di Grup
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            @else
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">{{ $currentOther->role ?? 'N/A' }} • {{ $currentOther->prodi ?? 'N/A' }}</p>
                                <span class="px-2 py-0.5 bg-indigo-500/10 border border-indigo-500/20 rounded-full text-[9px] font-bold text-indigo-400 uppercase tracking-tighter">
                                    {{ $currentOther->responsiveness_tag ?? 'Belum ada Tag' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-6" id="messages-container">
                @if($conversation->messages->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center opacity-40">
                        <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">Ketik pesan pertama Anda...</p>
                    </div>
                @else
                    @foreach($conversation->messages as $message)
                        @php $isMe = $message->sender_id === auth()->id(); @endphp
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} animate-message" style="animation-delay: {{ $loop->index * 50 }}ms">
                            <div class="max-w-[85%] md:max-w-md">
                                @if($isGroup && !$isMe)
                                    <div class="text-[10px] text-gray-400 font-bold ml-1 mb-1">{{ $message->sender->name ?? 'User' }}</div>
                                @endif
                                <div class="px-4 py-3 rounded-2xl shadow-sm {{ $isMe ? 'bg-indigo-600 text-white rounded-br-none shadow-indigo-500/20' : 'bg-white/10 dark:bg-gray-800 text-gray-900 dark:text-white rounded-bl-none border border-white/5 backdrop-blur-sm' }}">
                                    <p class="text-sm leading-relaxed">{{ $message->content }}</p>
                                </div>
                                <div class="mt-1 flex items-center {{ $isMe ? 'justify-end' : 'justify-start' }} space-x-1">
                                    <span class="text-[9px] text-gray-500 font-medium">{{ $message->created_at->format('H:i') }}</span>
                                    @if($isMe)
                                        <svg class="w-3 h-3 {{ $message->is_read ? 'text-indigo-400' : 'text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="p-4 md:p-6 glass border-t border-white/5 z-10">
                <form id="message-form" action="{{ route('message.store', $conversation->id) }}" method="POST" class="relative group">
                    @csrf
                    <div class="flex items-center space-x-3">
                        <button type="button" class="p-3 bg-white/5 hover:bg-white/10 text-gray-400 rounded-xl transition-all active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                        </button>
                        <div class="flex-1 relative">
                            <input id="message-input" type="text" name="content" required 
                                   class="block w-full py-3.5 px-4 bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-gray-100 placeholder-gray-500 transition-all" 
                                   placeholder="Ketik pesan di sini..." autocomplete="off">
                        </div>
                        <button type="submit" class="p-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-lg shadow-indigo-500/20 transform active:scale-95 transition-all">
                            <svg class="w-6 h-6 rotate-90" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- View Members Modal -->
            <template x-if="openGroupMembersModal">
                <div class="fixed inset-0 z-[120] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div @click="openGroupMembersModal = false" class="fixed inset-0 transition-opacity bg-gray-900/80 backdrop-blur-sm" aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform glass sm:my-8 sm:align-middle sm:max-w-md sm:w-full rounded-2xl border border-white/10 shadow-2xl">
                            <div class="px-6 py-6 border-b border-white/5 flex justify-between items-center bg-white/5">
                                <h3 class="text-xl font-black text-white uppercase tracking-tight">Anggota Grup</h3>
                                <button @click="openGroupMembersModal = false" class="text-gray-400 hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div class="px-6 py-4 max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-indigo-500 space-y-3">
                                @if(isset($conversation) && $conversation->is_group)
                                    @foreach($conversation->conversationUsers as $member)
                                    <div class="flex items-center p-2 rounded-xl hover:bg-white/5 transition-colors">
                                        <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-sm uppercase">
                                            {{ substr($member->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-bold text-white">{{ $member->user->name ?? 'User Terhapus' }}</p>
                                            <p class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ $member->user->role ?? 'N/A' }} • {{ ucfirst($member->role) }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Modal Reward XP (Muncul 1x per Sesi) -->
    @if((isset($showXpModal) && $showXpModal) || session('showXpModal'))
    <div x-data="{ open: true }" 
         x-show="open" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-12"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-x-0 bottom-8 z-[100] flex justify-center px-4 pointer-events-none">
        <div class="glass max-w-sm w-full p-5 flex items-center space-x-4 shadow-2xl shadow-indigo-500/20 border-indigo-500/30 pointer-events-auto relative group">
            <div class="absolute -top-1 -right-1">
                <button @click="open = false" class="bg-gray-800 text-gray-400 hover:text-white rounded-full p-1 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center flex-shrink-0 animate-bounce shadow-lg shadow-indigo-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <h4 class="text-white font-bold text-sm">Horee! +5 Poin Aktif</h4>
                <p class="text-xs text-gray-400 mt-0.5">Pertahankan respon cepat Anda untuk mendapatkan Tag Spesial!</p>
            </div>
            <div class="absolute -z-10 inset-0 bg-gradient-to-r from-indigo-600/5 to-purple-600/5 blur-xl group-hover:blur-2xl transition-all"></div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-scroll to bottom of chat
            const scrollToBottom = () => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            };

            // Initial scroll
            scrollToBottom();

            // Listen for message submission
            const messageForm = document.getElementById('message-form');
            const messageInput = document.getElementById('message-input');

            if (messageForm) {
                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const content = messageInput.value;
                    if (!content.trim()) return;

                    const url = messageForm.getAttribute('action');
                    
                    if (window.axios) {
                        axios.post(url, {
                            content: content
                        })
                        .then(response => {
                            messageInput.value = '';
                            appendMessage(response.data.message, true);
                        })
                        .catch(error => {
                            console.error('Error sending message:', error);
                            let errorMsg = 'Gagal mengirim pesan real-time. ';
                            if (error.response && error.response.status === 419) {
                                errorMsg += 'Sesi Anda telah kedaluwarsa (Error 419). Silakan refresh halaman (Ctrl+F5).';
                            } else {
                                errorMsg += (error.response ? `Status: ${error.response.status}` : error.message);
                            }
                            alert(errorMsg);
                        });
                    } else {
                        console.error('Axios not found, falling back to traditional submit');
                        messageForm.submit();
                    }
                });
            }

            // Listen for real-time messages with a slight delay or check
            const initEcho = () => {
                if (window.Echo) {
                    console.log('Echo initialized for conversation: {{ $conversation->id }}');
                    
                    const channel = window.Echo.private('conversation.{{ $conversation->id }}');
                    
                    channel.on('pusher:subscription_succeeded', () => {
                        console.log('Successfully subscribed to private-conversation.{{ $conversation->id }}');
                    });

                    channel.on('pusher:subscription_error', (status) => {
                        console.error('Subscription error (403 or connection issue):', status);
                        // If it's a 403, our authorization logic in channels.php might be failing
                    });

                    channel.listen('MessageSent', (e) => {
                        console.log('Message received via Echo:', e);
                        if (e.sender_id !== {{ auth()->id() }}) {
                            appendMessage(e, false);
                        }
                    });
                } else {
                    console.warn('Echo not found yet, retrying in 500ms...');
                    setTimeout(initEcho, 500);
                }
            };

            initEcho();

            function appendMessage(message, isMe) {
                const container = document.getElementById('messages-container');
                if (!container) return;

                // Remove empty state if present
                const emptyState = container.querySelector('.opacity-40');
                if (emptyState) emptyState.remove();

                const messageDiv = document.createElement('div');
                messageDiv.className = `flex ${isMe ? 'justify-end' : 'justify-start'} animate-message`;
                
                const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });

                const senderNameElement = ({{ $isGroup ? 'true' : 'false' }} && !isMe && message.sender) 
                    ? `<div class="text-[10px] text-gray-400 font-bold ml-1 mb-1">${message.sender.name}</div>` 
                    : '';

                messageDiv.innerHTML = `
                    <div class="max-w-[85%] md:max-w-md">
                        ${senderNameElement}
                        <div class="px-4 py-3 rounded-2xl shadow-sm ${isMe ? 'bg-indigo-600 text-white rounded-br-none shadow-indigo-500/20' : 'bg-white/10 dark:bg-gray-800 text-gray-900 dark:text-white rounded-bl-none border border-white/5 backdrop-blur-sm'}">
                            <p class="text-sm leading-relaxed">${message.content}</p>
                        </div>
                        <div class="mt-1 flex items-center ${isMe ? 'justify-end' : 'justify-start'} space-x-1">
                            <span class="text-[9px] text-gray-500 font-medium">${time}</span>
                            ${isMe ? `
                                <svg class="w-3 h-3 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                                </svg>
                            ` : ''}
                        </div>
                    </div>
                `;

                container.appendChild(messageDiv);
                scrollToBottom();
            }
        });
    </script>
</x-app-layout>
