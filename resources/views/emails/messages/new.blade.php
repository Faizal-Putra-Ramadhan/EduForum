<x-mail::message>
# Halo! Ada pesan baru untuk Anda.

**{{ $sender->name }}** baru saja mengirimkan Anda sebuah pesan di EduForum.

<x-mail::panel>
"{{ Str::limit($content, 150) }}"
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/forum'">
Buka EduForum & Balas
</x-mail::button>

Jangan biarkan teman atau dosen Anda menunggu lama. Balasan cepat akan membantu Anda mendapatkan poin!

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
