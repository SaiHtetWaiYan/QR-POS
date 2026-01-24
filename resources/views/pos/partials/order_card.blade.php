<div class="relative bg-white p-4 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-200 group"
     data-order-id="{{ $order->id }}"
     x-data="orderCard({{ $order->id }}, '{{ route('pos.orders.updateStatus', $order->id) }}', '{{ csrf_token() }}')">
    <!-- Header -->
    <div class="flex justify-between items-start mb-3">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 14h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                </svg>
            </div>
            <div>
                <span class="block font-bold text-gray-900 leading-tight">{{ $order->table->name }}</span>
                <span class="text-xs text-gray-400 font-mono">#{{ $order->order_no }}</span>
            </div>
        </div>
        <div class="text-right">
            <span class="block font-bold text-lg text-gray-900">{{ config('pos.currency_symbol') }}{{ number_format($order->total, 2) }}</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide mt-1
                {{ $order->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                {{ $order->status === 'accepted' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $order->status === 'preparing' ? 'bg-orange-100 text-orange-700' : '' }}
                {{ $order->status === 'served' ? 'bg-indigo-100 text-indigo-700' : '' }}
                {{ $order->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : '' }}
            ">
                {{ __(ucfirst($order->status)) }}
            </span>
        </div>
    </div>

    <!-- Notifications -->
    @if($order->customer_note)
        <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 px-3 py-2 rounded-r-xl mb-3">
            <p class="text-xs text-red-700 flex items-start gap-1.5">
                <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span><strong class="font-semibold">{{ __('Note') }}:</strong> {{ $order->customer_note }}</span>
            </p>
        </div>
    @endif

    @if($order->bill_requested_at && $order->status !== 'paid')
        <div class="bg-gradient-to-r from-violet-600 to-purple-600 text-white text-xs font-bold px-3 py-2.5 rounded-xl shadow-lg shadow-violet-600/20 mb-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                <span>{{ __('Bill Requested') }}</span>
            </div>
            <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
    @endif

    <!-- Items Summary -->
    <div class="border-t border-gray-100 pt-3 mb-3">
        <div class="flex justify-between items-center text-sm">
            <span class="text-gray-600 font-medium">
                {{ $order->orderItems->sum('qty') }} {{ trans_choice('ui.customer.items', $order->orderItems->sum('qty')) }}
            </span>
            <span class="text-xs text-gray-400">{{ $order->created_at->format('h:i A') }}</span>
        </div>
        <div class="mt-2 space-y-1">
            @foreach($order->orderItems->take(3) as $item)
                <div class="flex items-center gap-2 text-sm">
                    <span class="bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded text-[10px] font-bold">{{ $item->qty }}x</span>
                    <span class="text-gray-700 truncate">{{ $item->name_snapshot }}</span>
                </div>
            @endforeach
            @if($order->orderItems->count() > 3)
                <p class="text-xs text-gray-400 pl-7">+{{ $order->orderItems->count() - 3 }} {{ __('more items') }}</p>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
        <a href="{{ route('pos.orders.show', $order->id) }}"
           class="col-span-2 flex justify-center items-center gap-1.5 w-full px-3 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-medium text-xs text-gray-700 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            {{ __('View Details') }}
        </a>

        @if($order->status === 'pending')
            <button type="button"
                    @click="updateStatus('accepted')"
                    :disabled="loading"
                    class="col-span-2 w-full flex justify-center items-center gap-1.5 px-3 py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white rounded-xl font-semibold text-xs shadow-lg shadow-blue-600/20 transition-all hover:shadow-xl disabled:opacity-50">
                <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg x-cloak x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('Accept Order') }}
            </button>
        @elseif($order->status === 'accepted')
            <button type="button"
                    @click="updateStatus('preparing')"
                    :disabled="loading"
                    class="col-span-2 w-full flex justify-center items-center gap-1.5 px-3 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-400 hover:to-amber-400 text-white rounded-xl font-semibold text-xs shadow-lg shadow-orange-500/20 transition-all hover:shadow-xl disabled:opacity-50">
                <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                </svg>
                <svg x-cloak x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('Start Preparing') }}
            </button>
        @elseif($order->status === 'preparing')
            <button type="button"
                    @click="updateStatus('served')"
                    :disabled="loading"
                    class="col-span-2 w-full flex justify-center items-center gap-1.5 px-3 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white rounded-xl font-semibold text-xs shadow-lg shadow-indigo-600/20 transition-all hover:shadow-xl disabled:opacity-50">
                <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg x-cloak x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('Mark Served') }}
            </button>
        @elseif($order->status === 'served')
            <a href="{{ route('pos.orders.print', $order->id) }}" target="_blank"
               class="flex justify-center items-center gap-1.5 w-full px-3 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-medium text-xs text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                {{ __('Print') }}
            </a>
            <button type="button"
                    @click="showPaidConfirm = true"
                    :disabled="loading"
                    class="w-full flex justify-center items-center gap-1.5 px-3 py-2.5 bg-gradient-to-r from-emerald-600 to-green-500 hover:from-emerald-500 hover:to-green-400 text-white rounded-xl font-semibold text-xs shadow-lg shadow-emerald-600/20 transition-all hover:shadow-xl disabled:opacity-50">
                <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <svg x-cloak x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('Mark Paid') }}
            </button>
        @endif
    </div>

    <!-- Paid Confirmation Dialog -->
    <div x-cloak x-show="showPaidConfirm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 z-20 flex items-center justify-center px-4"
         aria-modal="true"
         role="dialog">
        <div class="absolute inset-0 bg-gray-900/40 rounded-2xl" @click="showPaidConfirm = false"></div>
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
