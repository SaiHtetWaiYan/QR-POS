<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    {{ __('Reports') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">{{ __('Daily and monthly performance') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">{{ __('Today') }}</p>
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
                            <p class="text-xs text-slate-500">{{ __('Orders') }}</p>
                            <p class="text-xl font-bold text-slate-900">{{ $dailyCount }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">{{ __('Revenue') }}</p>
                            <p class="text-xl font-bold text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($dailyRevenue, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">{{ __('This Month') }}</p>
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
                            <p class="text-xs text-slate-500">{{ __('Orders') }}</p>
                            <p class="text-xl font-bold text-slate-900">{{ $monthlyCount }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">{{ __('Revenue') }}</p>
                            <p class="text-xl font-bold text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($monthlyRevenue, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">{{ __('Avg order value') }}</p>
                            <p class="text-lg font-semibold text-gray-900">{{ config('pos.currency_symbol') }}{{ number_format($avgOrderValue, 2) }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V7m10 9H8m11 4H8m5-16h6m-3-3v6"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">{{ __('Based on non-cancelled orders this month') }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">{{ __('Items sold') }}</p>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($monthlyItemsSold) }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7l1 12h12l1-12M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">{{ __('Monthly quantity across all menu items') }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">{{ __('Cancelled') }}</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $monthlyCancelledCount }} / {{ $monthlyTotalCount }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">{{ number_format($monthlyCancelRate, 1) }}% {{ __('cancellation rate this month') }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Trends') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Last 14 days performance') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('pos.reports.export', 'last14') }}"
                           class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:border-gray-300 hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-8m0 8l-3-3m3 3l3-3M4 19h16"/>
                            </svg>
                            {{ __('Export last 14 days') }}
                        </a>
                        <a href="{{ route('pos.reports.export', 'month') }}"
                           class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-8m0 8l-3-3m3 3l3-3M4 19h16"/>
                            </svg>
                            {{ __('Export this month') }}
                        </a>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-semibold text-gray-900">{{ __('Orders') }}</p>
                            <p class="text-xs text-gray-400">{{ __('Daily count') }}</p>
                        </div>
                        <div class="flex items-end gap-2 h-32">
                            @foreach($trend as $point)
                                <div class="flex-1 flex flex-col items-center gap-2 min-w-0">
                                    <div class="w-full h-24 rounded-full bg-white border border-gray-200 flex items-end overflow-hidden">
                                        <div class="w-full rounded-full bg-indigo-500" style="height: {{ $trendMaxOrders > 0 ? ($point['orders'] / $trendMaxOrders) * 100 : 0 }}%;"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-400 leading-none">
                                        {{ $loop->index % 2 === 0 ? $point['label'] : '' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-semibold text-gray-900">{{ __('Revenue') }}</p>
                            <p class="text-xs text-gray-400">{{ __('Daily total') }}</p>
                        </div>
                        <div class="flex items-end gap-2 h-32">
                            @foreach($trend as $point)
                                <div class="flex-1 flex flex-col items-center gap-2 min-w-0">
                                    <div class="w-full h-24 rounded-full bg-white border border-gray-200 flex items-end overflow-hidden">
                                        <div class="w-full rounded-full bg-emerald-500" style="height: {{ $trendMaxRevenue > 0 ? ($point['revenue'] / $trendMaxRevenue) * 100 : 0 }}%;"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-400 leading-none">
                                        {{ $loop->index % 2 === 0 ? $point['label'] : '' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs text-gray-500">
                            {{ __('Values reflect paid + active orders, excluding cancelled.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm xl:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Peak hours') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('Orders by hour (last 14 days)') }}</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ __('Busiest') }}: <span class="font-semibold text-gray-700">{{ $busiestHourLabel }}</span>
                            ({{ $busiestHourOrders }})
                        </div>
                    </div>
                    <div class="flex items-end gap-1 h-32">
                        @foreach($hourly as $hour)
                            <div class="flex-1 flex flex-col items-center gap-2 min-w-0">
                                <div class="w-full h-24 rounded-lg bg-white border border-gray-200 flex items-end overflow-hidden">
                                    <div class="w-full bg-sky-500" style="height: {{ $hourMax > 0 ? ($hour['orders'] / $hourMax) * 100 : 0 }}%;"></div>
                                </div>
                                <span class="text-[10px] text-gray-400 leading-none">
                                    {{ $loop->index % 3 === 0 ? $hour['label'] : '' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Status mix') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Monthly order distribution') }}</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($statusBreakdown as $row)
                            <div class="flex items-center justify-between">
                                <span class="text-xs uppercase tracking-wide text-gray-400 font-semibold">{{ __($row['status']) }}</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $row['count'] }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full bg-indigo-500" style="width: {{ $monthlyTotalCount > 0 ? ($row['count'] / $monthlyTotalCount) * 100 : 0 }}%;"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Top items') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Most ordered this month') }}</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wide text-gray-400">
                                    <th class="pb-2">{{ __('Item') }}</th>
                                    <th class="pb-2 text-right">{{ __('Qty') }}</th>
                                    <th class="pb-2 text-right">{{ __('Revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($topItems as $item)
                                    <tr>
                                        <td class="py-3 text-gray-900 font-medium">{{ $item->name_snapshot }}</td>
                                        <td class="py-3 text-right text-gray-700">{{ number_format($item->total_qty) }}</td>
                                        <td class="py-3 text-right text-gray-700">{{ config('pos.currency_symbol') }}{{ number_format($item->total_revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-6 text-center text-gray-500" colspan="3">{{ __('No items yet') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Recent orders') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Latest activity') }}</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wide text-gray-400">
                                    <th class="pb-2">{{ __('Order') }}</th>
                                    <th class="pb-2">{{ __('Table') }}</th>
                                    <th class="pb-2">{{ __('Status') }}</th>
                                    <th class="pb-2 text-right">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td class="py-3 text-gray-900 font-medium">{{ $order->order_no }}</td>
                                        <td class="py-3 text-gray-700">{{ $order->table->name ?? '—' }}</td>
                                        <td class="py-3 text-gray-700 capitalize">{{ $order->status }}</td>
                                        <td class="py-3 text-right text-gray-700">{{ config('pos.currency_symbol') }}{{ number_format($order->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-6 text-center text-gray-500" colspan="4">{{ __('No recent orders') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
