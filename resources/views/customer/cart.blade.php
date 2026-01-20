@extends('layouts.customer')

@section('header')
    Your Cart
@endsection

@section('subheader')
    {{ count($cart ?? []) }} {{ Str::plural('item', count($cart ?? [])) }}
@endsection

@section('content')
    @if(empty($cart))
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-slate-900 mb-2">Your cart is empty</h2>
            <p class="text-slate-500 mb-8 text-sm">Discover our delicious menu and add items to get started.</p>
            <a href="{{ route('customer.index', $table->code) }}"
               class="inline-flex items-center gap-2 bg-slate-900 text-white px-6 py-3 rounded-full font-semibold hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Browse Menu
            </a>
        </div>
    @else
        <div class="space-y-3 mb-6">
            @foreach($cart as $lineId => $item)
                <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm animate-fade-in" x-data="{ removing: false }">
                    <div class="flex justify-between items-start gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <h3 class="font-semibold text-slate-900">{{ $item['name'] }}</h3>
                                <span class="shrink-0 font-bold text-slate-900 ml-2">{{ config('pos.currency_symbol') }}{{ number_format($item['price'] * $item['qty'], 2) }}</span>
                            </div>
                            @if($item['note'])
                                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                    {{ $item['note'] }}
                                </p>
                            @endif
                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center gap-2 text-sm text-slate-500">
                                    <span class="bg-slate-100 px-2.5 py-1 rounded-full font-medium text-xs">{{ $item['qty'] }}x</span>
                                    <span>{{ config('pos.currency_symbol') }}{{ number_format($item['price'], 2) }} each</span>
                                </div>
                                <form action="{{ route('customer.cart.remove', [$table->code, $lineId]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            @click="removing = true"
                                            :disabled="removing"
                                            class="text-xs text-red-500 hover:text-red-600 font-medium flex items-center gap-1 transition-colors disabled:opacity-50">
                                        <svg x-show="!removing" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span x-text="removing ? 'Removing...' : 'Remove'"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Order Summary -->
        <div class="bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-2xl p-5 border border-slate-200/50 mb-6">
            <h3 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Order Summary
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Subtotal</span>
                    <span class="font-medium text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Tax ({{ config('pos.tax_rate', 0) * 100 }}%)</span>
                    <span class="text-slate-600">{{ config('pos.currency_symbol') }}{{ number_format($subtotal * config('pos.tax_rate', 0), 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Service ({{ config('pos.service_charge', 0) * 100 }}%)</span>
                    <span class="text-slate-600">{{ config('pos.currency_symbol') }}{{ number_format($subtotal * config('pos.service_charge', 0), 2) }}</span>
                </div>
                <div class="border-t border-slate-200 pt-3 mt-3">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-slate-900">Estimated Total</span>
                        <span class="text-xl font-bold text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($subtotal * (1 + config('pos.tax_rate', 0) + config('pos.service_charge', 0)), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Place Order Form -->
        <form action="{{ route('customer.order.place', $table->code) }}" method="POST" x-data="{ submitting: false }">
            @csrf
            <div class="mb-5">
                <label for="customer_note" class="block text-sm font-medium text-slate-700 mb-2">Special Instructions</label>
                <textarea name="customer_note"
                          id="customer_note"
                          rows="2"
                          class="w-full border-slate-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 resize-none text-sm placeholder:text-slate-400"
                          placeholder="Any allergies or special requests for the kitchen?"></textarea>
            </div>
            <button type="submit"
                    @click="submitting = true"
                    :disabled="submitting"
                    class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 transition-all duration-200 disabled:opacity-70 flex items-center justify-center gap-2">
                <svg x-show="!submitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg x-show="submitting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="submitting ? 'Placing Order...' : 'Place Order'"></span>
            </button>
        </form>

        <div class="text-center mt-5">
            <a href="{{ route('customer.index', $table->code) }}"
               class="inline-flex items-center gap-2 text-slate-600 font-medium hover:text-slate-900 transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add more items
            </a>
        </div>
    @endif
@endsection
