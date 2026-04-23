<x-guest-layout>
    <div class="mb-10 text-center lg:text-left">
        <h2 class="text-3xl font-black text-white tracking-tight uppercase">Welcome <span class="text-emerald-500">Back</span></h2>
        <p class="text-gray-400 mt-2 font-medium">Access your academic hub and stay synced.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="group">
            <x-input-label for="email" :value="__('E-MAIL ADDRESS')" class="text-[10px] font-black text-gray-500 tracking-[0.2em] mb-2 ml-1 group-focus-within:text-emerald-500 transition-colors" />
            <x-text-input id="email" class="block w-full bg-white/5 border-gray-800/50 focus:border-emerald-500 focus:ring-emerald-500 rounded-2xl py-4 px-5 text-white placeholder-gray-600 transition-all shadow-inner" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email..." />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-bold" />
        </div>

        <!-- Password -->
        <div class="group">
            <div class="flex items-center justify-between mb-2">
                <x-input-label for="password" :value="__('PASSWORD')" class="text-[10px] font-black text-gray-500 tracking-[0.2em] ml-1 group-focus-within:text-emerald-500 transition-colors" />
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-black text-emerald-500 hover:text-emerald-400 transition-colors tracking-widest uppercase" href="{{ route('password.request') }}">
                        {{ __('Forgot?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block w-full bg-white/5 border-gray-800/50 focus:border-emerald-500 focus:ring-emerald-500 rounded-2xl py-4 px-5 text-white placeholder-gray-600 transition-all shadow-inner"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Your secret password..." />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-bold" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="relative flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="w-5 h-5 rounded-lg bg-white/5 border-gray-800 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-slate-950 transition-all" name="remember">
                <span class="ms-3 text-sm font-bold text-gray-500 group-hover:text-emerald-400 transition-colors uppercase tracking-tight">{{ __('Keep me logged in') }}</span>
            </label>
        </div>

        <div class="pt-4">
            <x-primary-button class="w-full justify-center py-5 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl shadow-2xl shadow-emerald-600/30 transform active:scale-[0.97] transition-all text-xs tracking-[0.2em] uppercase">
                {{ __('Secure Sign In') }}
            </x-primary-button>
        </div>

        <div class="pt-8 border-t border-white/5">
            <p class="text-center text-sm font-bold text-gray-500 uppercase tracking-tight">
                New to EduForum? 
                <a href="{{ route('register') }}" class="text-emerald-500 hover:text-emerald-400 transition-colors">{{ __('Create Account') }}</a>
            </p>
        </div>
    </form>
</x-guest-layout>
