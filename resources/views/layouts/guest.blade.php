<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('pos.shop_name', config('app.name', 'QR POS')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .auth-gradient {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            .floating-shapes::before,
            .floating-shapes::after {
                content: '';
                position: absolute;
                border-radius: 50%;
                opacity: 0.1;
            }
            .floating-shapes::before {
                width: 400px;
                height: 400px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                top: -200px;
                right: -100px;
            }
            .floating-shapes::after {
                width: 300px;
                height: 300px;
                background: linear-gradient(135deg, #f093fb, #f5576c);
                bottom: -150px;
                left: -100px;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-50 via-white to-indigo-50 relative overflow-hidden floating-shapes">
            <div class="relative z-10">
                @php
                    $locale = app()->getLocale();
                @endphp
                <a href="/" class="flex flex-col items-center group">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl shadow-indigo-500/30 group-hover:shadow-2xl group-hover:shadow-indigo-500/40 transition-all duration-300 group-hover:scale-105">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h2M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <span class="mt-4 text-2xl font-bold text-gray-900 tracking-tight">{{ config('pos.shop_name', config('app.name', 'QR POS')) }}</span>
                    <span class="text-sm text-gray-500 mt-1">{{ __('ui.guest.tagline') }}</span>
                </a>
                <div class="mt-3 flex items-center justify-center gap-2 text-sm font-semibold text-gray-500">
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="{{ $locale === 'en' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                        EN
                    </a>
                    <span class="text-gray-300">/</span>
                    <a href="{{ route('locale.switch', 'my') }}"
                       class="{{ $locale === 'my' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('ui.language.myanmar') }}
                    </a>
                </div>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-8 py-8 bg-white/80 backdrop-blur-xl shadow-2xl shadow-gray-200/50 overflow-hidden sm:rounded-3xl border border-white/50 relative z-10">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center text-sm text-gray-400 relative z-10">
                &copy; {{ date('Y') }} {{ config('pos.shop_name', config('app.name', 'QR POS')) }}. {{ __('ui.guest.rights') }}
            </div>
        </div>
    </body>
</html>
