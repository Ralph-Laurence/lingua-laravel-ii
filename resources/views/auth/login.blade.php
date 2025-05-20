{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}
<x-guest-layout :useLogo="false">

    <x-slot name="modstyles">
        <style>
        body {
            background: #F3F4F6;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-image: linear-gradient(to right, rgba(255, 123, 0, 0.5),
                    /* Red with 50% opacity */
                    /* rgba(0, 4, 255, 0.5), Yellow with 50% opacity */
                    /*rgba(111, 0, 255, 0.5) Blue with 50% opacity */
                    rgba(17, 0, 255, 0.5)),
                    url("{{ asset('assets/img/login_bg.jpg') }}");
            background-size: cover;
            background-position: center;
        }
        .main-container {
            background: none !important;
        }
        #big-text {
            font-size: 18px !important;
            opacity: 0.9;
            font-weight: bold;
        }
        .btn-login:hover {
            background-color: #624CC2 !important;
            border: 1px solid #493477 !important;
        }
        .btn-login:disabled {
            opacity: 0.75;
        }
        .btn-login {
            background-color: #5A4095 !important;
            border: 1px solid #493477 !important;
        }
        .flex-center {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        </style>
    </x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />


    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex-center mb-2">
        <a href="/">
            <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
        </a>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <h6 id="big-text" class="mb-4 w-100 text-center">Login to SignLingua</h6>

        <!-- Login (Username/Email) -->
        <div>
            <x-input-label for="login" :value="__('Username/Email')" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3 btn-login">
                {{ __('Log in') }}
            </x-primary-button>

        </div>
    </form>
</x-guest-layout>
