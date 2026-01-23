<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    {{ __('Order History') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Review orders by day</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pos.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
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
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm mb-6">
                <form method="GET" action="{{ route('pos.history') }}" class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                    <div class="flex-1">
                        <label for="history_date" class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                        <input type="date"
                               id="history_date"
                               name="date"
                               value="{{ $date }}"
                               class="w-full max-w-xs rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-medium text-sm text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h3.28a1 1 0 01.948.684l1.2 3.6a1 1 0 00.95.684h7.244a1 1 0 01.894 1.447l-3.5 7A1 1 0 0114.118 17H7a1 1 0 01-.948-.684L3.28 5.684A1 1 0 013 4z"/>
                        </svg>
                        View
                    </button>
                </form>

                @if($availableDates->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach($availableDates->take(10) as $availableDate)
                            <a href="{{ route('pos.history', ['date' => $availableDate]) }}"
                               class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ $availableDate === $date ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300' }}">
                                {{ \Illuminate\Support\Carbon::parse($availableDate)->format('M d, Y') }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500">Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500">Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">{{ config('pos.currency_symbol') }}{{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500">Date</p>
                    <p class="text-sm font-semibold text-gray-900">{{ \Illuminate\Support\Carbon::parse($date)->format('M d, Y') }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500">Statuses</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $orders->groupBy('status')->count() }}</p>
                </div>
            </div>

            @if($orders->isEmpty())
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <div class="text-center py-12 text-gray-400">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">No orders for this day</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-full items-start">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 ring-4 ring-amber-100"></span>
                                Pending
                            </h3>
                            @if($pending->count() > 0)
                                <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $pending->count() }}</span>
                            @endif
                        </div>
                        <div class="bg-gradient-to-b from-amber-50/50 to-white rounded-2xl p-4 min-h-[400px] border border-amber-100/50 space-y-4">
                            @foreach($pending as $order)
                                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $order->order_no }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->table->name ?? 'Table' }}</p>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $order->created_at->format('h:i A') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">Pending</span>
                                        <span class="font-semibold text-gray-900">{{ config('pos.currency_symbol') }}{{ number_format($order->total, 2) }}</span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('pos.orders.show', $order->id) }}"
                                           class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                            @if($pending->isEmpty())
                                <div class="text-center text-xs text-gray-400 py-6">No pending orders</div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col h-full">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 ring-4 ring-blue-100"></span>
                                Kitchen
                            </h3>
                            @if($active->count() > 0)
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $active->count() }}</span>
                            @endif
                        </div>
                        <div class="bg-gradient-to-b from-blue-50/50 to-white rounded-2xl p-4 min-h-[400px] border border-blue-100/50 space-y-4">
                            @foreach($active as $order)
                                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $order->order_no }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->table->name ?? 'Table' }}</p>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $order->created_at->format('h:i A') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                            {{ $order->status === 'accepted' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $order->status === 'preparing' ? 'bg-orange-100 text-orange-700' : '' }}
                                            {{ $order->status === 'served' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                        ">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        <span class="font-semibold text-gray-900">{{ config('pos.currency_symbol') }}{{ number_format($order->total, 2) }}</span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('pos.orders.show', $order->id) }}"
                                           class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                            @if($active->isEmpty())
                                <div class="text-center text-xs text-gray-400 py-6">No kitchen orders</div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col h-full">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                                Complete
                            </h3>
                            @if($completed->count() > 0)
                                <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $completed->count() }}</span>
                            @endif
                        </div>
                        <div class="bg-gradient-to-b from-emerald-50/50 to-white rounded-2xl p-4 min-h-[400px] border border-emerald-100/50 space-y-4">
                            @foreach($completed as $order)
                                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $order->order_no }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->table->name ?? 'Table' }}</p>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $order->created_at->format('h:i A') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">Paid</span>
                                        <span class="font-semibold text-gray-900">{{ config('pos.currency_symbol') }}{{ number_format($order->total, 2) }}</span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('pos.orders.show', $order->id) }}"
                                           class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                            @if($completed->isEmpty())
                                <div class="text-center text-xs text-gray-400 py-6">No completed orders</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
