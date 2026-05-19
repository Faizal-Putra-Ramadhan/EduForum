<x-guest-layout>
    <div class="mb-10 sm:text-left">
        <h2 class="text-4xl font-extrabold text-white tracking-tight mb-2">Welcome Back</h2>
        <p class="text-gray-400 font-medium">Please enter your credentials to access your dashboard.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm font-bold" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" :value="__('Email Address')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500 ml-1" />
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-indigo-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                </div>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                    class="block w-full pl-12 pr-4 py-4 bg-white/5 border-gray-800 focus:border-indigo-500 focus:ring-0 rounded-2xl text-white placeholder-gray-600 transition-all font-medium" 
                    placeholder="name@university.edu" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex items-center justify-between ml-1">
                <x-input-label for="password" :value="__('Password')" class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-500" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot?') }}
                    </a>
                @endif
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-indigo-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" type="password" name="password" required 
                    class="block w-full pl-12 pr-4 py-4 bg-white/5 border-gray-800 focus:border-indigo-500 focus:ring-0 rounded-2xl text-white placeholder-gray-600 transition-all font-medium" 
                    placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-md bg-white/5 border-gray-800 text-indigo-600 focus:ring-0 transition-all cursor-pointer" name="remember">
                <span class="ms-2 text-sm text-gray-500 group-hover:text-gray-300 transition-colors font-medium">{{ __('Remember session') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl shadow-xl shadow-indigo-500/20 transform active:scale-[0.98] transition-all flex items-center justify-center space-x-2 group">
                <span>Sign In Securely</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </button>
        </div>

        <!-- Social Login Decorative -->
        <div class="relative py-4">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-800"></div></div>
            <div class="relative flex justify-center text-xs uppercase"><span class="bg-[#0b1120] px-4 text-gray-500 font-bold tracking-widest">Or continue with</span></div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <button type="button" class="flex items-center justify-center py-3 bg-white/5 border border-gray-800 rounded-2xl hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.904 3.192-1.912 4.192-1.352 1.352-3.12 2.624-6.496 2.624-5.26 0-9.624-4.264-9.624-9.536s4.364-9.536 9.624-9.536c3.2 0 5.488 1.256 7.152 2.8l2.312-2.312C18.424 1.08 15.68 0 12.48 0 6.64 0 1.88 4.76 1.88 10.6s4.76 10.6 10.6 10.6c3.16 0 5.56-1.04 7.424-2.992 1.92-1.92 2.528-4.584 2.528-6.756 0-.64-.04-1.24-.132-1.78l-9.82.012z"/>
                </svg>
                <span class="text-xs font-bold text-gray-300">Google</span>
            </button>
            <button type="button" class="flex items-center justify-center py-3 bg-white/5 border border-gray-800 rounded-2xl hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                </svg>
                <span class="text-xs font-bold text-gray-300">GitHub</span>
            </button>
        </div>

        <div class="mt-10 text-center">
            <p class="text-sm text-gray-500 font-medium">
                New to our community? 
                <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 transition-colors font-bold ml-1 italic underline decoration-indigo-500/30 underline-offset-4">Create Account</a>
            </p>
        </div>
    </form>
</x-guest-layout>

