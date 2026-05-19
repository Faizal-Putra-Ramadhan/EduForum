<x-guest-layout>
    <div class="mb-10 sm:text-left">
        <h2 class="text-4xl font-extrabold text-white tracking-tight mb-2">Join Community</h2>
        <p class="text-gray-400 font-medium">Create your EduForum account in just a few steps.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('role', 'mahasiswa') }}' }" class="space-y-6">
        @csrf

        <!-- Role Selector -->
        <div>
            <x-input-label :value="__('You are registering as')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500 ml-1 mb-3" />
            <div class="grid grid-cols-2 gap-4">
                <label class="relative group cursor-pointer">
                    <input type="radio" name="role" value="mahasiswa" x-model="role" class="sr-only">
                    <div class="flex flex-col items-center justify-center p-4 rounded-2xl border-2 transition-all duration-300"
                         :class="role === 'mahasiswa' ? 'bg-indigo-600/10 border-indigo-500 ring-4 ring-indigo-500/10' : 'bg-white/5 border-gray-800 hover:border-gray-700'">
                        <svg class="w-6 h-6 mb-2 transition-colors" :class="role === 'mahasiswa' ? 'text-indigo-400' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        <span class="text-xs font-black uppercase tracking-widest" :class="role === 'mahasiswa' ? 'text-white' : 'text-gray-400'">Student</span>
                    </div>
                </label>
                <label class="relative group cursor-pointer">
                    <input type="radio" name="role" value="dosen" x-model="role" class="sr-only">
                    <div class="flex flex-col items-center justify-center p-4 rounded-2xl border-2 transition-all duration-300"
                         :class="role === 'dosen' ? 'bg-indigo-600/10 border-indigo-500 ring-4 ring-indigo-500/10' : 'bg-white/5 border-gray-800 hover:border-gray-700'">
                        <svg class="w-6 h-6 mb-2 transition-colors" :class="role === 'dosen' ? 'text-indigo-400' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-xs font-black uppercase tracking-widest" :class="role === 'dosen' ? 'text-white' : 'text-gray-400'">Lecturer</span>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="space-y-4">
            <!-- Full Name -->
            <div class="space-y-2">
                <x-input-label for="name" :value="__('Full Name')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500 ml-1" />
                <input id="name" type="text" name="name" :value="old('name')" required 
                    class="block w-full px-5 py-4 bg-white/5 border-gray-800 focus:border-indigo-500 focus:ring-0 rounded-2xl text-white placeholder-gray-600 transition-all font-medium" 
                    placeholder="John Doe" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Email -->
                <div class="space-y-2">
                    <x-input-label for="email" :value="__('Email')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500 ml-1" />
                    <input id="email" type="email" name="email" :value="old('email')" required 
                        class="block w-full px-5 py-4 bg-white/5 border-gray-800 focus:border-indigo-500 focus:ring-0 rounded-2xl text-white placeholder-gray-600 transition-all font-medium" 
                        placeholder="john@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Phone -->
                <div class="space-y-2">
                    <x-input-label for="phone" :value="__('Phone Number')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500 ml-1" />
                    <input id="phone" type="text" name="phone" :value="old('phone')" required 
                        class="block w-full px-5 py-4 bg-white/5 border-gray-800 focus:border-indigo-500 focus:ring-0 rounded-2xl text-white placeholder-gray-600 transition-all font-medium" 
                        placeholder="0812..." />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- ID Fields -->
                <div x-show="role === 'mahasiswa'" x-transition class="space-y-2">
                    <x-input-label for="nim" :value="__('Student ID (NIM)')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500 ml-1" />
                    <input id="nim" type="text" name="nim" :value="old('nim')" x-bind:required="role === 'mahasiswa'" 
                        class="block w-full px-5 py-4 bg-white/5 border-gray-800 focus:border-indigo-500 focus:ring-0 rounded-2xl text-white placeholder-gray-600 transition-all font-medium" 
                        placeholder="210..." />
                </div>

                <div x-show="role === 'dosen'" x-transition x-cloak class="space-y-2">
                    <x-input-label for="nidn" :value="__('Lecturer ID (NIDN)')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500 ml-1" />
                    <input id="nidn" type="text" name="nidn" :value="old('nidn')" x-bind:required="role === 'dosen'" 
                        class="block w-full px-5 py-4 bg-white/5 border-gray-800 focus:border-indigo-500 focus:ring-0 rounded-2xl text-white placeholder-gray-600 transition-all font-medium" 
                        placeholder="000..." />
                </div>

                <!-- Prodi -->
                <div class="space-y-2">
                    <x-input-label for="prodi" :value="__('Program Study')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500 ml-1" />
                    <input id="prodi" type="text" name="prodi" :value="old('prodi')" required 
                        class="block w-full px-5 py-4 bg-white/5 border-gray-800 focus:border-indigo-500 focus:ring-0 rounded-2xl text-white placeholder-gray-600 transition-all font-medium" 
                        placeholder="Informatika" />
                    <x-input-error :messages="$errors->get('prodi')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <!-- Password -->
                <div class="space-y-2">
                    <x-input-label for="password" :value="__('Password')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500 ml-1" />
                    <input id="password" type="password" name="password" required autocomplete="new-password" 
                        class="block w-full px-5 py-4 bg-white/5 border-gray-800 focus:border-indigo-500 focus:ring-0 rounded-2xl text-white placeholder-gray-600 transition-all font-medium" 
                        placeholder="••••••••" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <x-input-label for="password_confirmation" :value="__('Confirm')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500 ml-1" />
                    <input id="password_confirmation" type="password" name="password_confirmation" required 
                        class="block w-full px-5 py-4 bg-white/5 border-gray-800 focus:border-indigo-500 focus:ring-0 rounded-2xl text-white placeholder-gray-600 transition-all font-medium" 
                        placeholder="••••••••" />
                </div>
            </div>
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl shadow-xl shadow-indigo-500/20 transform active:scale-[0.98] transition-all flex items-center justify-center space-x-2 group">
                <span>Create Professional Account</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </button>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 font-medium">
                Already part of EduForum? 
                <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 transition-colors font-bold ml-1 italic underline decoration-indigo-500/30 underline-offset-4">Sign In</a>
            </p>
        </div>
    </form>
</x-guest-layout>

