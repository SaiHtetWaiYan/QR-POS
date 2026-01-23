<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1e293b">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>Session Expired - {{ config('pos.shop_name', config('app.name', 'QR POS')) }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen flex flex-col max-w-lg mx-auto bg-white shadow-2xl shadow-slate-200/50">
        <!-- Header -->
        <header class="bg-gradient-to-r from-slate-800 to-slate-900 safe-area-top">
            <div class="px-5 py-4">
                <h1 class="text-white font-semibold text-lg tracking-tight text-center">Session Expired</h1>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-grow flex items-center justify-center px-6 py-12">
            <div class="text-center">
                <!-- Icon -->
                <div class="mx-auto w-24 h-24 bg-amber-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <!-- Message -->
                <h2 class="text-2xl font-bold text-slate-900 mb-3">Your Session Has Expired</h2>
                <p class="text-slate-600 mb-8 max-w-xs mx-auto">
                    For your security, sessions expire after 1 hour of inactivity. Please scan the QR code on your table to continue ordering.
                </p>

                <!-- QR Code Icon -->
                <div class="mx-auto w-32 h-32 bg-slate-100 rounded-2xl flex items-center justify-center mb-6 border-2 border-dashed border-slate-300">
                    <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>

                <p class="text-sm text-slate-500">
                    Scan the QR code to start a new session
                </p>
            </div>
        </main>
    </div>
</body>
</html>
