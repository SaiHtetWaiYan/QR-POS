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
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-slate-50">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-slate-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @if(request()->routeIs('pos.*'))
                            @php
                                $posNav = [
                                    [
                                        'key' => 'dashboard',
                                        'label' => __('ui.nav.dashboard'),
                                        'route' => route('pos.index'),
                                        'active' => request()->routeIs('pos.index'),
                                    ],
                                    [
                                        'key' => 'history',
                                        'label' => __('ui.nav.history'),
                                        'route' => route('pos.history'),
                                        'active' => request()->routeIs('pos.history', 'pos.orders.*'),
                                    ],
                                    [
                                        'key' => 'reports',
                                        'label' => __('ui.nav.reports'),
                                        'route' => route('pos.reports'),
                                        'active' => request()->routeIs('pos.reports'),
                                    ],
                                    [
                                        'key' => 'menu',
                                        'label' => __('ui.nav.menu'),
                                        'route' => route('pos.menu.index'),
                                        'active' => request()->routeIs('pos.menu.*'),
                                    ],
                                    [
                                        'key' => 'coupons',
                                        'label' => __('ui.nav.coupons'),
                                        'route' => route('pos.coupons.index'),
                                        'active' => request()->routeIs('pos.coupons.*'),
                                    ],
                                    [
                                        'key' => 'tables',
                                        'label' => __('ui.nav.tables'),
                                        'route' => route('pos.tables.index'),
                                        'active' => request()->routeIs('pos.tables.*'),
                                    ],
                                    [
                                        'key' => 'settings',
                                        'label' => __('ui.nav.settings'),
                                        'route' => route('pos.settings.edit'),
                                        'active' => request()->routeIs('pos.settings.*'),
                                    ],
                                ];
                            @endphp
                            <div class="mb-5">
                                <div class="flex flex-wrap gap-3">
                                    @foreach($posNav as $item)
                                        <a href="{{ $item['route'] }}"
                                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl border text-sm font-semibold transition-all duration-150 {{ $item['active'] ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-600/20' : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                                            @if($item['key'] === 'dashboard')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            @elseif($item['key'] === 'coupons')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8L9 19l-4-4-4 4V7a2 2 0 012-2h10z"/>
                                                </svg>
                                            @elseif($item['key'] === 'reports')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10V9m-9 8h10a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            @elseif($item['key'] === 'history')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            @elseif($item['key'] === 'menu')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            @elseif($item['key'] === 'tables')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                                </svg>
                                            @elseif($item['key'] === 'settings')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 100-6 3 3 0 000 6zm7.4 0a1.65 1.65 0 01.33 1.82l-.2.4a2 2 0 01-2.2 1.1 7.98 7.98 0 01-1.6-.7 7.98 7.98 0 01-1.2 1.2 7.98 7.98 0 01.7 1.6 2 2 0 01-1.1 2.2l-.4.2a1.65 1.65 0 01-1.82-.33 8.17 8.17 0 01-1.1-1.1 8.17 8.17 0 01-1.1 1.1 1.65 1.65 0 01-1.82.33l-.4-.2a2 2 0 01-1.1-2.2 7.98 7.98 0 01.7-1.6 7.98 7.98 0 01-1.2-1.2 7.98 7.98 0 01-1.6.7 2 2 0 01-2.2-1.1l-.2-.4A1.65 1.65 0 013.6 15a8.17 8.17 0 010-2 1.65 1.65 0 01-.33-1.82l.2-.4a2 2 0 012.2-1.1 7.98 7.98 0 011.6.7 7.98 7.98 0 011.2-1.2 7.98 7.98 0 01-.7-1.6 2 2 0 011.1-2.2l.4-.2a1.65 1.65 0 011.82.33 8.17 8.17 0 011.1 1.1 8.17 8.17 0 011.1-1.1 1.65 1.65 0 011.82-.33l.4.2a2 2 0 011.1 2.2 7.98 7.98 0 01-.7 1.6 7.98 7.98 0 011.2 1.2 7.98 7.98 0 011.6-.7 2 2 0 012.2 1.1l.2.4a1.65 1.65 0 01-.33 1.82 8.17 8.17 0 010 2z"/>
                                                </svg>
                                            @endif
                                            {{ $item['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @if(request()->routeIs('pos.*') && !request()->routeIs('pos.index'))
            <div x-data="{
                    showBillAlert: false,
                    billAlertData: null,
                    init() {
                        if (typeof Echo !== 'undefined') {
                            Echo.private('pos')
                                .listen('.BillRequested', (e) => {
                                    this.handleBillRequest(e);
                                });
                        }
                    },
                    handleBillRequest(data) {
                        this.playBillSound();
                        this.billAlertData = data;
                        if (this.showBillAlert) {
                            this.showBillAlert = false;
                            this.$nextTick(() => {
                                this.showBillAlert = true;
                            });
                        } else {
                            this.showBillAlert = true;
                        }
                    },
                    playBillSound() {
                        const audio = new Audio('data:audio/wav;base64,UklGRl9vT19teleVBFZkZXNjcgAAAFNOT1RJRlkgQkVMTCBTT1VORAAAAAAASUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAADhAC7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7//////////////////////////////////////////////////////////////////8AAAAATGF2YzU4LjEzAAAAAAAAAAAAAAAAJAAAAAAAAAAAA4T/////AAAAAAAAAAAAAAAAAAAAAP/7kGQAAANUMEoFPeACNQV40, Grilbl AAD/+5JkAA/wAABpAAAACAAADSAAAAEAAAGkAAAAIAAANIAAAAQAAAaQAAAAgAA');
                        audio.volume = 0.7;
                        audio.play().catch(() => {});
                        setTimeout(() => audio.play().catch(() => {}), 300);
                    },
                    dismissBillAlert() {
                        this.showBillAlert = false;
                        this.billAlertData = null;
                    }
                }">
                <div x-cloak x-show="showBillAlert"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                     @click.self="dismissBillAlert()">
                    <div x-show="showBillAlert"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden">
                        <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-8 text-center">
                            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">{{ __('Bill Requested!') }}</h3>
                            <p class="text-white/80 mt-1">{{ __('A customer is waiting for the bill') }}</p>
                        </div>
                        <div class="px-6 py-5">
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-gray-500">{{ __('Table') }}</span>
                                <span class="font-bold text-gray-900" x-text="billAlertData?.table || @js(__('Unknown'))"></span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-gray-500">{{ __('Order') }}</span>
                                <span class="font-mono text-gray-900" x-text="'#' + (billAlertData?.order_no || '')"></span>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <span class="text-gray-500">{{ __('Total') }}</span>
                                <span class="text-xl font-bold text-gray-900" x-text="'{{ config('pos.currency_symbol') }}' + (billAlertData?.total ? parseFloat(billAlertData.total).toFixed(2) : '0.00')"></span>
                            </div>
                        </div>
                        <div class="px-6 pb-6 flex gap-3">
                            <button @click="dismissBillAlert()"
                                    class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                                {{ __('Dismiss') }}
                            </button>
                            <a :href="'/pos/orders/' + (billAlertData?.order_id || '')"
                               class="flex-1 px-4 py-3 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 text-white font-semibold rounded-xl text-center transition-all shadow-lg shadow-violet-600/30">
                                {{ __('View Order') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </body>
</html>
