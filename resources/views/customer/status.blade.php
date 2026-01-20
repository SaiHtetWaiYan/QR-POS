@extends('layouts.customer')

@section('header')
    Order Status
@endsection

@section('subheader')
    @if($order)
        Order #{{ $order->order_no }}
    @endif
@endsection

@section('content')
    @if(!$order)
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-slate-900 mb-2">No active order</h2>
            <p class="text-slate-500 mb-8 text-sm">Start ordering from our menu to see your order status here.</p>
            <a href="{{ route('customer.index', $table->code) }}"
               class="inline-flex items-center gap-2 bg-slate-900 text-white px-6 py-3 rounded-full font-semibold hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Browse Menu
            </a>
        </div>
    @else
        <div x-data="{ refreshing: false }"
             x-init="setInterval(() => { refreshing = true; setTimeout(() => window.location.reload(), 500) }, 15000)">

            <!-- Status Progress -->
            @php
                $statuses = ['pending', 'accepted', 'preparing', 'served', 'paid'];
                $currentIndex = array_search($order->status, $statuses);
                if ($currentIndex === false) $currentIndex = -1;
            @endphp

            <div class="mb-6">
                <div class="flex items-center justify-between relative">
                    <div class="absolute top-4 left-0 right-0 h-0.5 bg-slate-200"></div>
                    <div class="absolute top-4 left-0 h-0.5 bg-emerald-500 transition-all duration-500"
                         style="width: {{ $currentIndex >= 0 ? (($currentIndex / (count($statuses) - 1)) * 100) : 0 }}%"></div>

                    @foreach($statuses as $index => $status)
                        <div class="relative flex flex-col items-center z-10">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                                {{ $index <= $currentIndex
                                    ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                                    : 'bg-slate-100 text-slate-400' }}">
                                @if($index < $currentIndex)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @elseif($index == $currentIndex)
                                    <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                @else
                                    <span class="w-2 h-2 bg-slate-300 rounded-full"></span>
                                @endif
                            </div>
                            <span class="mt-2 text-[10px] font-medium {{ $index <= $currentIndex ? 'text-slate-900' : 'text-slate-400' }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Current Status Banner -->
            @php
                $statusConfig = [
                    'pending' => ['bg' => 'bg-amber-500', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Waiting for confirmation'],
                    'accepted' => ['bg' => 'bg-blue-500', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Order confirmed'],
                    'preparing' => ['bg' => 'bg-orange-500', 'icon' => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z', 'text' => 'Being prepared'],
                    'served' => ['bg' => 'bg-emerald-500', 'icon' => 'M5 13l4 4L19 7', 'text' => 'Ready to enjoy'],
                    'paid' => ['bg' => 'bg-slate-700', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'text' => 'Complete'],
                    'cancelled' => ['bg' => 'bg-red-500', 'icon' => 'M6 18L18 6M6 6l12 12', 'text' => 'Cancelled'],
                ];
                $config = $statusConfig[$order->status] ?? $statusConfig['pending'];
            @endphp

            <div class="{{ $config['bg'] }} rounded-2xl p-5 mb-6 shadow-lg animate-fade-in">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="text-white">
                        <p class="text-sm font-medium text-white/80">Current Status</p>
                        <p class="text-xl font-bold">{{ $config['text'] }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-white/80 text-xs" x-show="!refreshing">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Auto-refreshing every 15s</span>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden mb-6 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-semibold text-slate-900">Order Items</h3>
                    <span class="text-xs text-slate-500">{{ $order->orderItems->sum('qty') }} items</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($order->orderItems as $item)
                        <div class="px-5 py-3 flex justify-between items-start">
                            <div class="flex items-start gap-3">
                                <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2 py-1 rounded-full">{{ $item->qty }}x</span>
                                <div>
                                    <span class="font-medium text-slate-900 text-sm">{{ $item->name_snapshot }}</span>
                                    @if($item->note)
                                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                            </svg>
                                            {{ $item->note }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <span class="text-sm font-medium text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($item->line_total, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Order Totals -->
                <div class="bg-slate-50 px-5 py-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Tax ({{ config('pos.tax_rate') * 100 }}%)</span>
                        <span class="text-slate-600">{{ config('pos.currency_symbol') }}{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Service ({{ config('pos.service_charge') * 100 }}%)</span>
                        <span class="text-slate-600">{{ config('pos.currency_symbol') }}{{ number_format($order->service_charge, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-200 mt-2">
                        <span class="font-semibold text-slate-900">Total</span>
                        <span class="text-xl font-bold text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($order->status !== 'paid' && $order->status !== 'cancelled')
                @if($order->bill_requested_at)
                    <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-5 text-white shadow-lg shadow-violet-500/30 animate-fade-in">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center animate-pulse">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-lg">Bill Requested</p>
                                <p class="text-sm text-white/80">Staff has been notified</p>
                            </div>
                        </div>
                    </div>
                @else
                    <form action="{{ route('customer.order.bill', [$table->code, $order->id]) }}" method="POST" x-data="{ requesting: false }">
                        @csrf
                        <button type="submit"
                                @click="if(!confirm('Request the bill?')) { $event.preventDefault(); return; } requesting = true;"
                                :disabled="requesting"
                                class="w-full bg-gradient-to-r from-slate-800 to-slate-900 text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-slate-900/30 hover:shadow-xl transition-all duration-200 disabled:opacity-70 flex items-center justify-center gap-2">
                            <svg x-show="!requesting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                            <svg x-show="requesting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="requesting ? 'Requesting...' : 'Request Bill'"></span>
                        </button>
                    </form>
                @endif
            @elseif($order->status === 'paid')
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white text-center shadow-lg shadow-emerald-500/30">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="font-bold text-xl mb-1">Thank You!</p>
                    <p class="text-emerald-100 text-sm">We hope you enjoyed your meal</p>
                </div>

                <div class="text-center mt-6">
                    <a href="{{ route('customer.index', $table->code) }}"
                       class="inline-flex items-center gap-2 text-slate-600 font-medium hover:text-slate-900 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Start New Order
                    </a>
                </div>
            @endif
        </div>
    @endif
@endsection
