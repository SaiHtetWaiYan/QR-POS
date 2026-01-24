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
    </body>
</html>
