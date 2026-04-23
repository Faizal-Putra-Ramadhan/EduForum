<x-mail::message>
# Halo! Ada pesan baru untuk Anda.

@if(!empty($groupName))
**{{ $sender->name }}** telah **me-mention (tag) Anda** di dalam grup diskusi: **{{ $groupName }}**.
@else
**{{ $sender->name }}** baru saja mengirimkan Anda sebuah pesan pribadi di EduForum.
@endif

<x-mail::panel>
"{{ Str::limit($content, 150) }}"
</x-mail::panel>

<x-mail::button :url="$actionUrl ?? config('app.url') . '/forum'">
Buka & Balas Sekarang
</x-mail::button>

Jangan biarkan mahasiswa atau rekan-rekan akademik Anda menunggu terlalu lama. Mari dukung percepatan diskusi!

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
