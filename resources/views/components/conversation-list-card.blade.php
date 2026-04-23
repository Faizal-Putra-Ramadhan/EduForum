@props(['conversation'])

@php
    $isGroup = $conversation->type === 'group';
    if ($isGroup) {
        $name = $conversation->name ?? 'Quantum Group';
        $otherUser = null;
    } else {
        $otherUser = $conversation->conversationUsers->where('user_id', '!=', auth()->id())->first()?->user;
        $name = $otherUser?->name ?? 'Terminal Node';
    }
    $initial = substr($name, 0, 1);
    $lastMessage = $conversation->messages->last();
    $unreadCount = $conversation->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count();
@endphp

<a href="{{ route('conversation.show', $conversation->id) }}" 
   {{ $attributes->merge(['class' => 'group block p-5 glass rounded-3xl border border-white/5 hover:border-emerald-500/30 hover:bg-emerald-500/5 transition-all duration-500 transform hover:scale-[1.02] relative overflow-hidden animate-fade-in-up']) }}>
    
    <!-- Background Glow on Hover -->
    <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/0 via-transparent to-emerald-600/0 group-hover:from-emerald-600/5 group-hover:to-emerald-600/5 transition-all duration-700"></div>

    <div class="flex items-center relative z-10">
        <!-- Avatar -->
        <div class="relative shrink-0">
            <div class="w-14 h-14 rounded-2xl {{ $isGroup ? 'bg-purple-600/20 text-purple-400 border-purple-500/20' : 'bg-emerald-600/20 text-emerald-400 border-emerald-500/20' }} border flex items-center justify-center text-xl font-black uppercase transition-transform group-hover:scale-110 duration-500 shadow-inner">
                {{ $initial }}
            </div>
            @if(!$isGroup && $otherUser)
                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-slate-950 rounded-full flex items-center justify-center border border-white/10">
                    <div class="w-2.5 h-2.5 rounded-full {{ $otherUser->role === 'dosen' ? "bg-emerald-500 shadow-[0_0_8px_theme('colors.emerald.400')]" : 'bg-blue-500' }}"></div>
                </div>
            @endif
        </div>

        <!-- Info -->
        <div class="ml-5 flex-1 min-w-0">
            <div class="flex items-center justify-between mb-1">
                <h4 class="text-sm font-black text-white truncate group-hover:text-emerald-400 transition-colors uppercase tracking-tight">{{ $name }}</h4>
                <span class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">{{ $conversation->last_message_at?->diffForHumans() }}</span>
            </div>
            
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 truncate font-medium max-w-[180px]">
                    @if($lastMessage)
                        <span class="font-bold text-gray-400">{{ $lastMessage->sender_id === auth()->id() ? 'You: ' : ($lastMessage->sender->name . ': ') }}</span>
                        {{ $lastMessage->content }}
                    @else
                        No messages yet
                    @endif
                </p>

                @if($unreadCount > 0)
                    <span class="flex items-center justify-center min-w-[20px] h-5 px-1.5 bg-emerald-600 text-white text-[10px] font-black rounded-lg shadow-lg shadow-emerald-600/20 animate-pulse">
                        {{ $unreadCount }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Bottom Indicator -->
    @if(!$isGroup && $otherUser && $otherUser->role === 'dosen')
        <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between">
            <span class="text-[8px] font-black text-emerald-500/60 uppercase tracking-[0.2em]">{{ $otherUser->responsiveness_tag }}</span>
            <div class="flex items-center space-x-1">
                @for($i=0; $i<3; $i++)
                    <div class="w-1 h-1 rounded-full bg-emerald-500/30"></div>
                @endfor
            </div>
        </div>
    @endif
</a>
