<x-guest-layout>
    <div class="mb-8 text-center sm:text-left">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Create your account</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-widest text-[10px] font-bold">Join the EduForum community today.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('role', 'mahasiswa') }}' }">
        @csrf

        <div class="space-y-5">
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Full Name')" class="text-[10px] uppercase tracking-widest font-bold text-gray-400" />
                <x-text-input id="name" class="block mt-1 w-full bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-[10px] uppercase tracking-widest font-bold text-gray-400" />
                    <x-text-input id="email" class="block mt-1 w-full bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="john@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Phone -->
                <div>
                    <x-input-label for="phone" :value="__('Phone Number')" class="text-[10px] uppercase tracking-widest font-bold text-gray-400" />
                    <x-text-input id="phone" class="block mt-1 w-full bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" type="text" name="phone" :value="old('phone')" required placeholder="08123456789" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
            </div>

            <!-- Role Selector -->
            <div>
                <x-input-label :value="__('I am a')" class="text-[10px] uppercase tracking-widest font-bold text-gray-400" />
                <div class="grid grid-cols-2 gap-3 mt-1">
                    <label class="flex items-center justify-center p-3 rounded-xl border border-gray-700/50 cursor-pointer transition-all duration-300"
                           :class="role === 'mahasiswa' ? 'bg-indigo-600/20 border-indigo-500 text-indigo-400' : 'bg-white/5 text-gray-500 hover:bg-white/10'">
                        <input type="radio" name="role" value="mahasiswa" x-model="role" class="hidden">
                        <span class="text-sm font-semibold uppercase tracking-wider">Student</span>
                    </label>
                    <label class="flex items-center justify-center p-3 rounded-xl border border-gray-700/50 cursor-pointer transition-all duration-300"
                           :class="role === 'dosen' ? 'bg-indigo-600/20 border-indigo-500 text-indigo-400' : 'bg-white/5 text-gray-500 hover:bg-white/10'">
                        <input type="radio" name="role" value="dosen" x-model="role" class="hidden">
                        <span class="text-sm font-semibold uppercase tracking-wider">Lecturer</span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- NIM/NIDN -->
                <div x-show="role === 'mahasiswa'" x-transition>
                    <x-input-label for="nim" :value="__('Student ID (NIM)')" class="text-[10px] uppercase tracking-widest font-bold text-gray-400" />
                    <x-text-input id="nim" class="block mt-1 w-full bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" type="text" name="nim" :value="old('nim')" x-bind:required="role === 'mahasiswa'" placeholder="2010..."/>
                    <x-input-error :messages="$errors->get('nim')" class="mt-2" />
                </div>

                <div x-show="role === 'dosen'" x-transition x-cloak>
                    <x-input-label for="nidn" :value="__('Lecturer ID (NIDN)')" class="text-[10px] uppercase tracking-widest font-bold text-gray-400" />
                    <x-text-input id="nidn" class="block mt-1 w-full bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" type="text" name="nidn" :value="old('nidn')" x-bind:required="role === 'dosen'" placeholder="0001..."/>
                    <x-input-error :messages="$errors->get('nidn')" class="mt-2" />
                </div>

                <!-- Prodi -->
                <div>
                    <x-input-label for="prodi" :value="__('Program Study')" class="text-[10px] uppercase tracking-widest font-bold text-gray-400" />
                    <x-text-input id="prodi" class="block mt-1 w-full bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" type="text" name="prodi" :value="old('prodi')" required placeholder="Informatika" />
                    <x-input-error :messages="$errors->get('prodi')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-[10px] uppercase tracking-widest font-bold text-gray-400" />
                    <x-text-input id="password" class="block mt-1 w-full bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Repeat Password')" class="text-[10px] uppercase tracking-widest font-bold text-gray-400" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full bg-white/5 border-gray-700/50 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="mt-10">
            <x-primary-button class="w-full justify-center py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transform active:scale-[0.98] transition-all">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>

        <div class="mt-8 pt-6 border-t border-white/5 text-center">
            <p class="text-sm text-gray-500">
                Already registered? 
                <a href="{{ route('login') }}" class="font-bold text-indigo-500 hover:text-indigo-400 transition-colors uppercase tracking-wider text-xs">Sign In</a>
            </p>
        </div>
    </form>
</x-guest-layout>
