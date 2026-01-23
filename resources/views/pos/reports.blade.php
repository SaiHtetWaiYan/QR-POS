<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    {{ __('Reports') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Daily and monthly performance</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pos.history') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    History
                </a>
                <a href="{{ route('pos.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-medium text-sm text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Today</p>
                            <p class="text-lg font-semibold text-gray-900">{{ \Illuminate\Support\Carbon::parse($today)->format('M d, Y') }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">Orders</p>
                            <p class="text-xl font-bold text-slate-900">{{ $dailyCount }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">Revenue</p>
                            <p class="text-xl font-bold text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($dailyRevenue, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-4 rounded-xl border border-gray-100 bg-white p-3">
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Most ordered item</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $dailyTopItem?->name_snapshot ?? 'No orders yet' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $dailyTopItem ? $dailyTopItem->total_qty.' ordered' : '' }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">This Month</p>
                            <p class="text-lg font-semibold text-gray-900">{{ now()->format('F Y') }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10V9m-9 8h10a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">Orders</p>
                            <p class="text-xl font-bold text-slate-900">{{ $monthlyCount }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">Revenue</p>
                            <p class="text-xl font-bold text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($monthlyRevenue, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-4 rounded-xl border border-gray-100 bg-white p-3">
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Most ordered item</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $monthlyTopItem?->name_snapshot ?? 'No orders yet' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $monthlyTopItem ? $monthlyTopItem->total_qty.' ordered' : '' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
