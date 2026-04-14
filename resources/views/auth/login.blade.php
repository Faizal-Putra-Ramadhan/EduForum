<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Please enter your details to sign in.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-xs uppercase tracking-widest font-semibold text-gray-500" />
            <x-text-input id="email" class="block mt-1 w-full bg-white/5 border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" class="text-xs uppercase tracking-widest font-semibold text-gray-500" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-indigo-500 hover:text-indigo-400 transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1 w-full bg-white/5 border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-6">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-md bg-white/5 border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-0 transition-all" name="remember">
                <span class="ms-2 text-sm text-gray-500 group-hover:text-gray-300 transition-colors">{{ __('Keep me signed in') }}</span>
            </label>
        </div>

        <div class="mt-8">
            <x-primary-button class="w-full justify-center py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transform active:scale-[0.98] transition-all">
                {{ __('Sign In') }}
            </x-primary-button>
        </div>

        <div class="mt-8 pt-6 border-t border-white/5 text-center">
            <p class="text-sm text-gray-500">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-semibold text-indigo-500 hover:text-indigo-400 transition-colors">Create an account</a>
            </p>
        </div>
    </form>
</x-guest-layout>
