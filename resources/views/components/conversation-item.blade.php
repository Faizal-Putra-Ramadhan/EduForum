@props(['conv', 'active'])

@php
    $isGroup = $conv->type === 'group';
    if ($isGroup) {
        $displayTitle = $conv->name ?? 'Quantum Group';
        $avatarChar = substr($displayTitle, 0, 1);
    } else {
        $otherMapping = $conv->conversationUsers->where('user_id', '!=', auth()->id())->first();
        $other = $otherMapping->user ?? null;
        $displayTitle = $other?->name ?? 'Deleted User';
        $avatarChar = substr($displayTitle, 0, 1);
    }
    $lastMessage = $conv->messages->last();
@endphp

<a href="{{ route('conversation.show', $conv->id) }}" 
   {{ $attributes->merge(['class' => 'group flex items-center p-3 rounded-xl transition-all duration-300 relative overflow-hidden ' . ($active ? 'bg-emerald-600/10 border border-emerald-500/20' : 'hover:bg-white/5 border border-transparent')]) }}>
    
    @if($active)
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 rounded-r shadow-[0_0_12px_theme('colors.emerald.400')]"></div>
    @endif

    <div class="relative shrink-0">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-black text-base uppercase transition-transform group-hover:scale-110 duration-500 {{ $isGroup ? 'bg-purple-600/20 text-purple-400 border border-purple-500/20' : 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/20' }}">
            {{ $avatarChar }}
        </div>
        @if(!$isGroup && $other)
            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-slate-950 rounded-full flex items-center justify-center border border-white/10">
                <div class="w-1.5 h-1.5 rounded-full {{ $other->role === 'dosen' ? "bg-emerald-500 shadow-[0_0_5px_theme('colors.emerald.400')]" : 'bg-blue-500' }}"></div>
            </div>
        @endif
    </div>

    <div class="ml-3 flex-1 min-w-0">
        <div class="flex items-center justify-between mb-0.5">
            <span class="font-black text-[12px] uppercase tracking-tight truncate {{ $active ? 'text-emerald-400' : 'text-gray-300 group-hover:text-white' }} transition-colors">{{ $displayTitle }}</span>
            <span class="text-[8px] font-bold text-gray-500 uppercase flex-shrink-0 ml-2 tracking-tighter">{{ $conv->last_message_at?->diffForHumans(null, true) }}</span>
        </div>
        <div class="flex items-center justify-between">
            <p class="text-[10px] text-gray-500 truncate font-medium max-w-[120px]">
                @if($lastMessage)
                    <span class="font-black text-gray-600">{{ $lastMessage->sender_id === auth()->id() ? 'You: ' : '' }}</span>
                    {{ $lastMessage->content }}
                @else
                    No transmissions...
                @endif
            </p>
            @if($conv->unread_count > 0 && !$active)
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_theme('colors.emerald.400')]"></div>
            @endif
        </div>
    </div>
</a>
