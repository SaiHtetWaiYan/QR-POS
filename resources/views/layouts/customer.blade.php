<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e293b">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>{{ config('app.name', 'QR POS') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes pulse-ring { 0% { transform: scale(0.8); opacity: 1; } 100% { transform: scale(1.3); opacity: 0; } }
        .animate-slide-up { animation: slideUp 0.3s ease-out; }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        .cart-pulse::before { content: ''; position: absolute; inset: -4px; border-radius: 50%; background: inherit; animation: pulse-ring 1.5s ease-out infinite; opacity: 0.5; z-index: -1; }
    </style>
    @php
        $tableCode = request()->route('table');
        $sessionKey = 'customer_session_started_at_' . $tableCode;
        $sessionStartedAt = session($sessionKey);
        $lifetimeSeconds = config('pos.customer_session_lifetime', 30) * 60;
        $remainingSeconds = $sessionStartedAt ? max(0, $lifetimeSeconds - (time() - $sessionStartedAt)) : $lifetimeSeconds;
    @endphp
    <script>
        (function() {
            var remaining = {{ $remainingSeconds }};
            if (remaining > 0) {
                setTimeout(function() {
                    window.location.reload();
                }, remaining * 1000);
            }
        })();
    </script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased"
      x-data="{ showToast: false, toastMessage: '', toastTimeout: null, cartCount: {{ collect(session('cart', []))->sum('qty') }} }"
      x-on:cart-updated.window="cartCount = $event.detail.count"
      x-on:toast.window="toastMessage = $event.detail.message; showToast = true; clearTimeout(toastTimeout); toastTimeout = setTimeout(() => showToast = false, 2500)">
    <div class="min-h-screen flex flex-col max-w-lg mx-auto bg-white shadow-2xl shadow-slate-200/50">
        <!-- Header -->
        <header class="bg-gradient-to-r from-slate-800 to-slate-900 sticky top-0 z-20 safe-area-top">
            <div class="px-5 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <a href="{{ route('customer.index', request()->route('table')) }}" class="text-white/70 hover:text-white transition-colors">
                        @if(!request()->routeIs('customer.index'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        @endif
                    </a>
                    <div>
                        <h1 class="text-white font-semibold text-lg tracking-tight">@yield('header')</h1>
                        @hasSection('subheader')
                            <p class="text-slate-400 text-xs">@yield('subheader')</p>
                        @endif
                    </div>
                </div>
                @if(!request()->routeIs('customer.cart.view'))
                    <a href="{{ route('customer.cart.view', request()->route('table')) }}"
                       x-cloak x-show="cartCount > 0"
                       class="relative flex items-center justify-center w-10 h-10 bg-amber-500 rounded-full text-white shadow-lg shadow-amber-500/30 hover:bg-amber-400 transition-all hover:scale-105 cart-pulse">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-[10px] font-bold flex items-center justify-center shadow-sm">
                            <span x-text="cartCount"></span>
                        </span>
                    </a>
                @endif
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow px-4 py-5 pb-24">
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 animate-slide-up" role="alert">
                    <div class="shrink-0 w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 animate-slide-up" role="alert">
                    <div class="shrink-0 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Toast -->
        <div x-cloak x-show="showToast"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed bottom-24 left-4 right-4 z-40">
            <div class="bg-emerald-600 text-white px-4 py-3 rounded-2xl shadow-lg shadow-emerald-500/30 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold" x-text="toastMessage"></p>
            </div>
        </div>

        <!-- Footer -->
        <footer class="fixed bottom-0 left-0 right-0 max-w-lg mx-auto bg-white/80 backdrop-blur-lg border-t border-slate-100 safe-area-bottom z-10">
            <div class="flex justify-around py-3 px-4">
                <a href="{{ route('customer.index', request()->route('table')) }}"
                   class="flex flex-col items-center gap-1 {{ request()->routeIs('customer.index') ? 'text-slate-900' : 'text-slate-400' }} transition-colors">
                    <svg class="w-6 h-6" fill="{{ request()->routeIs('customer.index') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="text-[10px] font-medium">Menu</span>
                </a>
                <a href="{{ route('customer.cart.view', request()->route('table')) }}"
                   class="flex flex-col items-center gap-1 {{ request()->routeIs('customer.cart.view') ? 'text-slate-900' : 'text-slate-400' }} transition-colors relative">
                    <svg class="w-6 h-6" fill="{{ request()->routeIs('customer.cart.view') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span x-cloak x-show="cartCount > 0"
                          class="absolute -top-1 left-1/2 w-4 h-4 bg-amber-500 rounded-full text-[8px] font-bold flex items-center justify-center text-white">
                        <span x-text="cartCount"></span>
                    </span>
                    <span class="text-[10px] font-medium">Cart</span>
                </a>
                <a href="{{ route('customer.status', request()->route('table')) }}"
                   class="flex flex-col items-center gap-1 {{ request()->routeIs('customer.status') ? 'text-slate-900' : 'text-slate-400' }} transition-colors">
                    <svg class="w-6 h-6" fill="{{ request()->routeIs('customer.status') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="text-[10px] font-medium">Orders</span>
                </a>
            </div>
        </footer>
    </div>
</body>
</html>
