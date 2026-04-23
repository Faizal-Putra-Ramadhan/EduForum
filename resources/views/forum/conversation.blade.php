<x-app-layout>
    <div class="flex h-full overflow-hidden">
        
        <!-- Sidebar - Conversation List (Hidden on Mobile) -->
        <div class="hidden md:flex flex-col w-1/3 lg:w-1/4 border-r border-white/5 bg-slate-950/20 backdrop-blur-3xl shrink-0" x-data="{ openMenu: false, openGroupModal: false }">
            <div class="p-8 border-b border-white/5 flex items-center justify-between relative">
                <h3 class="font-black text-xs text-gray-500 uppercase tracking-[0.3em]">Direct Messages</h3>
                
                <div class="relative">
                    <button @click="openMenu = !openMenu" class="p-2.5 bg-emerald-600 rounded-xl text-white hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-500/20 active:scale-95 group">
                        <svg class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-45': openMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>

                    <!-- Enhanced Dropdown Menu -->
                    <div x-show="openMenu" 
                         @click.away="openMenu = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         class="absolute right-0 mt-3 w-56 rounded-3xl bg-slate-900 border border-white/10 shadow-2xl z-[110] overflow-hidden p-2">
                        <button type="button" @click.prevent="globalGroupModalOpen = true; openMenu = false" class="w-full text-left p-3 text-[11px] font-black text-gray-400 hover:bg-white/5 hover:text-white rounded-2xl transition-all flex items-center group">
                            <div class="w-8 h-8 rounded-xl bg-emerald-600/10 text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center mr-3 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            NEW GROUP CHAT
                        </button>
                        <a href="{{ route('forum.search') }}" class="w-full text-left p-3 text-[11px] font-black text-gray-400 hover:bg-white/5 hover:text-white rounded-2xl transition-all flex items-center group">
                            <div class="w-8 h-8 rounded-xl bg-emerald-600/10 text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center mr-3 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            FIND ACADEME
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto px-4 py-8 space-y-8 scrollbar-thin scrollbar-thumb-emerald-600/10 scrollbar-track-transparent" x-data="{ openMahasiswa: true, openDosen: true, openGrup: true }">
                <!-- Group List Section -->
                @php
                    $groupConvs = $conversations->filter(fn($c) => $c->type === 'group');
                    $mahasiswaConvs = $conversations->filter(function($c) {
                        if ($c->type === 'group') return false;
                        $other = $c->conversationUsers->where('user_id', '!=', auth()->id())->first()?->user;
                        return $other && $other->role === 'mahasiswa';
                    });
                    $dosenConvs = $conversations->filter(function($c) {
                        if ($c->type === 'group') return false;
                        $other = $c->conversationUsers->where('user_id', '!=', auth()->id())->first()?->user;
                        return $other && $other->role === 'dosen';
                    });
                @endphp

                @if($groupConvs->isNotEmpty())
                <div>
                    <button @click="openGrup = !openGrup" class="flex items-center justify-between w-full px-4 mb-3 text-[9px] font-black text-gray-600 uppercase tracking-[0.3em] hover:text-emerald-500 transition-colors">
                        <span>Group Spaces</span>
                        <svg class="w-3 h-3 transform transition-transform" :class="openGrup ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"></path></svg>
                    </button>
                    <div x-show="openGrup" x-collapse class="space-y-1">
                        @foreach($groupConvs as $conv)
                            <x-conversation-item :conv="$conv" :active="$conv->id == $conversation->id" />
                        @endforeach
                    </div>
                </div>
                @endif

                @if($dosenConvs->isNotEmpty())
                <div>
                    <button @click="openDosen = !openDosen" class="flex items-center justify-between w-full px-4 mb-3 text-[9px] font-black text-gray-600 uppercase tracking-[0.3em] hover:text-emerald-500 transition-colors">
                        <span>Expert Consults</span>
                        <svg class="w-3 h-3 transform transition-transform" :class="openDosen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"></path></svg>
                    </button>
                    <div x-show="openDosen" x-collapse class="space-y-1">
                        @foreach($dosenConvs as $conv)
                            <x-conversation-item :conv="$conv" :active="$conv->id == $conversation->id" />
                        @endforeach
                    </div>
                </div>
                @endif

                @if($mahasiswaConvs->isNotEmpty())
                <div>
                    <button @click="openMahasiswa = !openMahasiswa" class="flex items-center justify-between w-full px-4 mb-3 text-[9px] font-black text-gray-600 uppercase tracking-[0.3em] hover:text-emerald-500 transition-colors">
                        <span>Peer Exchange</span>
                        <svg class="w-3 h-3 transform transition-transform" :class="openMahasiswa ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"></path></svg>
                    </button>
                    <div x-show="openMahasiswa" x-collapse class="space-y-1">
                        @foreach($mahasiswaConvs as $conv)
                            <x-conversation-item :conv="$conv" :active="$conv->id == $conversation->id" />
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col relative" x-data="{ openGroupMembersModal: false }">
            <!-- Glassy Header -->
            @php
                $isGroup = $conversation->type === 'group';
                if (!$isGroup) {
                    $otherMapping = $conversation->conversationUsers->where('user_id', '!=', auth()->id())->first();
                    $currentOther = $otherMapping->user ?? null;
                }
            @endphp
            <div class="px-8 h-24 border-b border-white/5 bg-slate-950/20 backdrop-blur-2xl flex items-center justify-between z-10">
                <div class="flex items-center">
                    <a href="{{ route('forum') }}" class="mr-6 md:hidden text-gray-400 hover:text-emerald-400 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    
                    <div class="relative group cursor-pointer">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-black text-lg uppercase shadow-xl transition-transform group-hover:scale-110 duration-500 {{ $isGroup ? 'bg-emerald-600' : 'bg-emerald-600/20 border border-emerald-500/30' }}">
                            {{ substr($isGroup ? $conversation->name : ($currentOther->name ?? 'C'), 0, 1) }}
                        </div>
                        @if(!$isGroup && $currentOther)
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-slate-950 emerald-glow"></div>
                        @endif
                    </div>

                    <div class="ml-5">
                        <h3 class="font-black text-lg text-white leading-tight flex items-center uppercase tracking-tight">
                            {{ $isGroup ? $conversation->name : ($currentOther->name ?? 'Anonymous') }}
                        </h3>
                        <div class="flex items-center mt-1">
                            @if($isGroup)
                                <button @click="openGroupMembersModal = true" class="text-[9px] text-gray-500 hover:text-emerald-400 font-bold uppercase tracking-[0.2em] transition-colors flex items-center">
                                    {{ $conversation->conversationUsers->count() }} MEMBERS SYNCED
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            @else
                                <p class="text-[9px] text-gray-500 font-black uppercase tracking-[0.2em]">{{ $currentOther->role ?? 'UNDEFINED' }} • {{ $currentOther->prodi ?? 'GENERAL' }}</p>
                                <span class="ml-3 px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-[8px] font-black text-emerald-400 uppercase tracking-widest">
                                    {{ $currentOther->responsiveness_tag ?? 'NEW USER' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 border border-white/5 text-gray-400 hover:text-white hover:border-emerald-500/30 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 border border-white/5 text-gray-400 hover:text-white hover:border-emerald-500/30 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 overflow-y-auto p-8 space-y-8 scrollbar-thin scrollbar-thumb-emerald-600/10 scrollbar-track-transparent" id="messages-container">
                @if($conversation->messages->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center opacity-30 group">
                        <div class="w-20 h-20 bg-emerald-600/10 rounded-[32px] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-700">
                            <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-black uppercase tracking-[0.4em] text-gray-500">Initialize Discussion</p>
                    </div>
                @else
                    @foreach($conversation->messages as $message)
                        @php $isMe = $message->sender_id === auth()->id(); @endphp
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} animate-message group" style="animation-delay: {{ $loop->index * 50 }}ms">
                            <div class="max-w-[80%] md:max-w-lg">
                                @if($isGroup && !$isMe)
                                    <div class="text-[9px] text-emerald-500 font-black uppercase tracking-widest ml-4 mb-2">{{ $message->sender->name ?? 'User' }}</div>
                                @endif
                                <div class="relative group/bubble">
                                    <div class="px-6 py-4 rounded-[28px] shadow-2xl {{ $isMe 
                                        ? 'bg-emerald-600 text-white rounded-br-lg shadow-emerald-900/40 border border-emerald-400/20' 
                                        : 'bg-white/5 backdrop-blur-md text-white rounded-bl-lg border border-white/10' }}">
                                        <p class="text-[14px] leading-relaxed font-medium">{{ $message->content }}</p>
                                    </div>
                                    
                                    <div class="mt-2 flex items-center {{ $isMe ? 'justify-end' : 'justify-start' }} space-x-2 px-2">
                                        <span class="text-[9px] text-gray-600 font-black tracking-widest">{{ $message->created_at->format('H:i') }}</span>
                                        @if($isMe)
                                            <div class="flex space-x-0.5">
                                                <div class="w-1.5 h-1.5 rounded-full {{ $message->is_read ? 'bg-emerald-400 animate-pulse' : 'bg-gray-700' }}"></div>
                                                <div class="w-1.5 h-1.5 rounded-full {{ $message->is_read ? 'bg-emerald-400 animate-pulse' : 'bg-gray-700' }}"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Enhanced Input Area / Block State -->
            <div class="p-8 z-10">
                @if($isBlocked)
                    <div class="glass border border-emerald-500/20 rounded-[32px] p-8 flex items-center justify-between relative overflow-hidden group">
                        <div class="absolute inset-0 bg-emerald-600/[0.03] animate-pulse"></div>
                        <div class="relative z-10 flex items-center space-x-6">
                            <div class="w-14 h-14 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 shrink-0 border border-emerald-500/30">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-black text-white uppercase tracking-widest">Protocol Sync in Progress</p>
                                <p class="text-xs text-gray-500 mt-1 font-medium max-w-md leading-relaxed">The 3x24h academic window is currently active. Your query is in the expert's queue. Bridge will auto-resume upon reply.</p>
                            </div>
                        </div>
                        <div class="relative z-10 hidden lg:block">
                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] bg-emerald-500/10 px-4 py-2 rounded-xl">Status: Processing</span>
                        </div>
                    </div>
                @else
                    <form id="message-form" action="{{ route('message.store', $conversation->id) }}" method="POST" class="relative group flex items-end space-x-3">
                        @csrf
                        <div class="flex-1 glass bg-white/5 border border-white/5 rounded-[32px] p-2 flex items-center transition-all focus-within:border-emerald-500/30 focus-within:bg-white/[0.08] shadow-2xl">
                            <button type="button" class="p-4 text-gray-500 hover:text-emerald-400 transition-colors shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </button>
                            <input id="message-input" type="text" name="content" required autocomplete="off"
                                class="flex-1 bg-transparent border-none focus:ring-0 text-white placeholder-gray-600 py-4 px-2 font-medium text-sm" 
                                placeholder="Quantum search for knowledge...">
                        </div>
                        <button type="submit" class="p-5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-3xl shadow-2xl shadow-emerald-600/40 transform active:scale-90 transition-all shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14M12 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
            
            <!-- Group Members Modal Redesign -->
            <template x-if="openGroupMembersModal">
                <div class="fixed inset-0 z-[120] overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div @click="openGroupMembersModal = false" class="fixed inset-0 transition-opacity bg-slate-950/90 backdrop-blur-md"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform glass sm:my-8 sm:align-middle sm:max-w-md sm:w-full rounded-[40px] border border-white/10 shadow-2xl">
                            <div class="px-8 py-8 border-b border-white/5 flex justify-between items-center bg-white/5">
                                <h3 class="text-xl font-black text-white uppercase tracking-tight">Active Nodes <span class="text-emerald-500">Synced</span></h3>
                                <button @click="openGroupMembersModal = false" class="p-2 text-gray-500 hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div class="px-8 py-6 max-h-[50vh] overflow-y-auto space-y-4 scrollbar-thin scrollbar-thumb-emerald-600/30">
                                @if(isset($conversation) && $conversation->type === 'group')
                                    @foreach($conversation->conversationUsers as $member)
                                    <div class="flex items-center p-3 rounded-2xl bg-white/5 border border-transparent hover:border-emerald-500/20 hover:bg-emerald-500/5 transition-all group/member">
                                        <div class="w-12 h-12 rounded-xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-black text-sm uppercase group-hover/member:scale-110 transition-transform shadow-inner">
                                            {{ substr($member->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="text-sm font-black text-white leading-tight uppercase tracking-tight">{{ $member->user->name ?? 'User Null' }}</p>
                                            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[0.2em] mt-1">{{ $member->user->role ?? 'N/A' }} • {{ ucfirst($member->role) }}</p>
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

    <!-- Enhanced XP Reward Achievement Modal -->
    @if((isset($showXpModal) && $showXpModal) || session('showXpModal'))
    <div x-data="{ open: true }" 
         x-show="open" 
         x-transition:enter="transition ease-out duration-700"
         x-transition:enter-start="opacity-0 translate-y-16 blur-xl"
         x-transition:enter-end="opacity-100 translate-y-0 blur-0"
         x-init="setTimeout(() => open = false, 8000)"
         class="fixed inset-x-0 bottom-12 z-[100] flex justify-center px-4 pointer-events-none">
        <div class="glass max-w-sm w-full p-6 flex items-center space-x-6 shadow-[0_20px_80px_-15px_rgba(16,185,129,0.3)] border-emerald-500/40 pointer-events-auto relative overflow-hidden rounded-3xl group">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/10 to-transparent"></div>
            <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/40 relative z-10 animate-bounce">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div class="relative z-10">
                <h4 class="text-white font-black text-xs uppercase tracking-[0.2em]">Achievement Unlocked</h4>
                <p class="text-emerald-400 font-black text-lg mt-1">+5 SYNC XP</p>
                <p class="text-[10px] text-gray-400 mt-2 font-bold uppercase tracking-tight">Rapid Response Bonus Applied</p>
            </div>
            <div class="absolute top-4 right-4 pointer-events-auto">
                <button @click="open = false" class="text-gray-500 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Keep Real-time Scripts with minor class adjustments if needed -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const scrollToBottom = () => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            };
            scrollToBottom();

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
                            if (response.data.is_blocked) {
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error sending message:', error);
                            if (error.response && error.response.status === 422) {
                                alert(error.response.data.message || 'Processing Protocol Active. Wait for expert sync.');
                                if (error.response.data.is_blocked) {
                                    window.location.reload();
                                }
                            } else {
                                alert('Sync failed. Terminal reconnection required.');
                            }
                        });
                    } else {
                        messageForm.submit();
                    }
                });
            }

            const initEcho = () => {
                if (window.Echo) {
                    const channel = window.Echo.private('conversation.{{ $conversation->id }}');
                    channel.listen('MessageSent', (e) => {
                        if (e.sender_id !== {{ auth()->id() }}) {
                            appendMessage(e, false);
                        }
                    });
                } else {
                    setTimeout(initEcho, 500);
                }
            };
            initEcho();

            function appendMessage(message, isMe) {
                const container = document.getElementById('messages-container');
                if (!container) return;

                const emptyState = container.querySelector('.opacity-30');
                if (emptyState) emptyState.remove();

                const messageDiv = document.createElement('div');
                messageDiv.className = `flex ${isMe ? 'justify-end' : 'justify-start'} animate-message group`;
                
                const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });

                const senderNameElement = ({{ $isGroup ? 'true' : 'false' }} && !isMe && message.sender) 
                    ? `<div class="text-[9px] text-emerald-500 font-black uppercase tracking-widest ml-4 mb-2">${message.sender.name}</div>` 
                    : '';

                messageDiv.innerHTML = `
                    <div class="max-w-[80%] md:max-w-lg">
                        ${senderNameElement}
                        <div class="relative">
                            <div class="px-6 py-4 rounded-[28px] shadow-2xl ${isMe ? 'bg-emerald-600 text-white rounded-br-lg border border-emerald-400/20' : 'bg-white/5 backdrop-blur-md text-white rounded-bl-lg border border-white/10'}">
                                <p class="text-[14px] leading-relaxed font-medium">${message.content}</p>
                            </div>
                            <div class="mt-2 flex items-center ${isMe ? 'justify-end' : 'justify-start'} space-x-2 px-2">
                                <span class="text-[9px] text-gray-600 font-black tracking-widest">${time}</span>
                                ${isMe ? `
                                    <div class="flex space-x-0.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-700"></div>
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-700"></div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;

                container.appendChild(messageDiv);
                scrollToBottom();
            }
        });
    </script>
    <!-- Modern Group Creation Modal Root -->
    <div x-show="globalGroupModalOpen" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="globalGroupModalOpen = false" class="fixed inset-0 transition-opacity bg-slate-950/90 backdrop-blur-md" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform glass sm:my-8 sm:align-middle sm:max-w-lg sm:w-full rounded-[40px] border border-white/10 shadow-2xl animate-fade-in-up">
                <form action="{{ route('group.create') }}" method="POST">
                    @csrf
                    <div class="px-10 py-12">
                        <h3 class="text-3xl font-black text-white mb-8 uppercase tracking-tight">Create <span class="text-emerald-500">Group</span></h3>
                        
                        <div class="space-y-8">
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-1 group-focus-within:text-emerald-500 transition-colors">Group Identity</label>
                                <input type="text" name="name" required class="block w-full px-6 py-4 bg-white/5 border-white/5 focus:border-emerald-500 focus:ring-emerald-500 rounded-2xl text-white placeholder-gray-600 transition-all font-sans text-sm font-medium" placeholder="e.g., Final Project IT-2024">
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
</x-app-layout>
