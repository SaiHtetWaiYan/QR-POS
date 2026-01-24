<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('pos.history') }}"
                   class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 leading-tight">
                        {{ __('Order') }} #{{ $order->order_no }}
                    </h2>
                    <p class="text-sm text-gray-500">{{ $order->table->name }}</p>
                </div>
            </div>
            @php
                $statusColors = [
                    'pending' => 'bg-amber-100 text-amber-700',
                    'accepted' => 'bg-blue-100 text-blue-700',
                    'preparing' => 'bg-orange-100 text-orange-700',
                    'served' => 'bg-indigo-100 text-indigo-700',
                    'paid' => 'bg-emerald-100 text-emerald-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                ];
            @endphp
            <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                {{ __(ucfirst($order->status)) }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Order Header Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-gray-50 to-white p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $order->table->name }}</h3>
                                <p class="text-sm text-gray-500 flex items-center gap-1.5 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $order->created_at->format('M d, Y \a\t h:i A') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">{{ __('Total Amount') }}</p>
                            <p class="text-3xl font-bold text-gray-900">{{ config('pos.currency_symbol') }}{{ number_format($order->total, 2) }}</p>
                        </div>
                    </div>
                </div>

                @if($order->customer_note)
                    <div class="bg-gradient-to-r from-red-50 to-rose-50 border-t border-red-100 px-6 py-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-red-800">{{ __('Customer Note') }}</p>
                                <p class="text-sm text-red-700 mt-0.5">{{ $order->customer_note }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($order->bill_requested_at && $order->status !== 'paid')
                    <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 text-white">
                                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                <span class="font-bold">{{ __('Bill Requested') }}</span>
                            </div>
                            <span class="text-white/80 text-sm">{{ $order->bill_requested_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        {{ __('Order Items') }}
                    </h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($order->orderItems as $item)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <span class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-sm font-bold text-gray-600">
                                    {{ $item->qty }}
                                </span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->name_snapshot }}</p>
                                    @if($item->note)
                                        <p class="text-sm text-gray-500 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                            </svg>
                                            {{ $item->note }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">{{ config('pos.currency_symbol') }}{{ number_format($item->line_total, 2) }}</p>
                                <p class="text-xs text-gray-500">@ {{ config('pos.currency_symbol') }}{{ number_format($item->price_snapshot, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="bg-gray-50 px-6 py-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ __('Subtotal') }}</span>
                        <span class="font-medium text-gray-900">{{ config('pos.currency_symbol') }}{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">{{ __('Tax') }} ({{ config('pos.tax_rate') * 100 }}%)</span>
                        <span class="text-gray-600">{{ config('pos.currency_symbol') }}{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">{{ __('Service Charge') }} ({{ config('pos.service_charge') * 100 }}%)</span>
                        <span class="text-gray-600">{{ config('pos.currency_symbol') }}{{ number_format($order->service_charge, 2) }}</span>
                    </div>
                    @if($order->coupon_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">
                                {{ __('Coupon') }}
                                @if($order->coupon_type === 'percent')
                                    ({{ number_format($order->coupon_value, 2) }}%)
                                @elseif($order->coupon_type === 'fixed')
                                    ({{ config('pos.currency_symbol') }}{{ number_format($order->coupon_value, 2) }})
                                @endif
                            </span>
                            <span class="text-red-600">-{{ config('pos.currency_symbol') }}{{ number_format($order->coupon_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-3 border-t border-gray-200 mt-2">
                        <span class="font-bold text-gray-900">{{ __('Total') }}</span>
                        <span class="font-bold text-xl text-gray-900">{{ config('pos.currency_symbol') }}{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>


            <!-- Actions -->
            @if($order->status !== 'paid' && $order->status !== 'cancelled')
                <div class="flex flex-wrap justify-end gap-3"
                     x-data="{
                        showCancelConfirm: false,
                        showPaidConfirm: false,
                        submitCancel() { this.$refs.cancelForm.submit(); },
                        submitPaid() { this.$refs.paidForm.submit(); }
                     }">
                    <a href="{{ route('pos.orders.print', $order->id) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        {{ __('Print Receipt') }}
                    </a>

                    <form action="{{ route('pos.orders.updateStatus', $order->id) }}" method="POST" x-ref="cancelForm">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit"
                                @click.prevent="showCancelConfirm = true"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-50 border border-red-200 rounded-xl font-medium text-sm text-red-700 hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ __('Cancel Order') }}
                        </button>
                    </form>

                    @if($order->status === 'served')
                        <form action="{{ route('pos.orders.updateStatus', $order->id) }}" method="POST" x-ref="paidForm">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="paid">
                            <button type="submit"
                                    @click.prevent="showPaidConfirm = true"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-green-500 rounded-xl font-semibold text-sm text-white shadow-lg shadow-emerald-500/20 hover:shadow-xl transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                {{ __('Mark Paid') }}
                            </button>
                        </form>
                    @endif

                    <!-- Cancel Confirmation Dialog -->
                    <div x-cloak x-show="showCancelConfirm"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 flex items-center justify-center px-4"
                         aria-modal="true"
                         role="dialog">
                        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showCancelConfirm = false"></div>
                        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-100 p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-gray-900">{{ __('Cancel this order?') }}</p>
                                    <p class="text-sm text-gray-500">{{ __('This action cannot be undone.') }}</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button type="button"
                                        @click="showCancelConfirm = false"
                                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                                    {{ __('Keep Order') }}
                                </button>
                                <button type="button"
                                        @click="submitCancel()"
                                        class="flex-1 py-2.5 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors">
                                    {{ __('Cancel Order') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Paid Confirmation Dialog -->
                    <div x-cloak x-show="showPaidConfirm"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 flex items-center justify-center px-4"
                         aria-modal="true"
                         role="dialog">
                        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showPaidConfirm = false"></div>
                        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-100 p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-gray-900">{{ __('Confirm payment received?') }}</p>
                                    <p class="text-sm text-gray-500">{{ __('This will mark the order as paid.') }}</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button type="button"
                                        @click="showPaidConfirm = false"
                                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                                    {{ __('Not Yet') }}
                                </button>
                                <button type="button"
                                        @click="submitPaid()"
                                        class="flex-1 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors">
                                    {{ __('Mark Paid') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($order->status === 'paid')
                <div class="bg-gradient-to-br from-emerald-500 to-green-500 rounded-2xl p-6 text-white text-center shadow-lg">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="font-bold text-xl">{{ __('Payment Complete') }}</p>
                    <p class="text-emerald-100 text-sm mt-1">{{ __('This order has been paid in full') }}</p>
                </div>
            @endif

            @if($order->status === 'cancelled')
                <div class="bg-gradient-to-br from-red-500 to-rose-500 rounded-2xl p-6 text-white text-center shadow-lg">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <p class="font-bold text-xl">{{ __('Order Cancelled') }}</p>
                    <p class="text-red-100 text-sm mt-1">{{ __('This order has been cancelled') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
