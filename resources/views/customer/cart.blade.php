@extends('layouts.customer')

@section('header')
    Your Cart
@endsection

@section('subheader')
    <span id="cart-item-count">{{ count($cart ?? []) }}</span>
    <span id="cart-item-label">{{ Str::plural('item', count($cart ?? [])) }}</span>
@endsection

@section('content')
    <div id="cart-state"
         data-count="{{ count($cart ?? []) }}"
         data-subtotal="{{ $subtotal ?? 0 }}"
         data-tax-rate="{{ config('pos.tax_rate', 0) }}"
         data-service-rate="{{ config('pos.service_charge', 0) }}"
         data-currency="{{ config('pos.currency_symbol') }}"></div>

    <div id="cart-empty-state" class="{{ empty($cart) ? '' : 'hidden opacity-0' }} transition-opacity duration-200">
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
    </div>

    <div id="cart-body" class="{{ empty($cart) ? 'hidden opacity-0' : '' }} transition-opacity duration-200">
        <div class="space-y-3 mb-6">
            @foreach($cart as $lineId => $item)
                <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm animate-fade-in"
                     data-cart-item
                     data-line-total="{{ $item['price'] * $item['qty'] }}"
                     x-data="{
                        qty: {{ $item['qty'] }},
                        price: {{ $item['price'] }},
                        lineTotal: {{ $item['price'] * $item['qty'] }},
                        updating: false,
                        removing: false,
                        currency: '{{ config('pos.currency_symbol') }}',
                        formatMoney(value) {
                            return `${this.currency}${Number(value).toFixed(2)}`;
                        },
                        updateTotals(data) {
                            const state = document.getElementById('cart-state');
                            if (!state) return;
                            const subtotalEl = document.getElementById('cart-subtotal');
                            const taxEl = document.getElementById('cart-tax');
                            const serviceEl = document.getElementById('cart-service');
                            const totalEl = document.getElementById('cart-total');
                            if (subtotalEl) subtotalEl.textContent = this.formatMoney(data.subtotal);
                            if (taxEl) taxEl.textContent = this.formatMoney(data.tax);
                            if (serviceEl) serviceEl.textContent = this.formatMoney(data.service);
                            if (totalEl) totalEl.textContent = this.formatMoney(data.total);
                            state.dataset.subtotal = String(data.subtotal);
                        },
                        async updateQty(newQty) {
                            if (this.updating || this.removing || newQty < 1) return;
                            this.updating = true;
                            try {
                                const response = await fetch('{{ route('customer.cart.update', [$table->code, $lineId]) }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ qty: newQty })
                                });
                                let data = {};
                                try {
                                    data = await response.json();
                                } catch (error) {
                                    data = {};
                                }
                                if (response.ok && data.success) {
                                    this.qty = data.qty;
                                    this.lineTotal = data.line_total;
                                    this.updateTotals(data);
                                    const itemEl = this.$el.closest('[data-cart-item]');
                                    if (itemEl) itemEl.dataset.lineTotal = String(data.line_total);
                                } else {
                                    alert(data.message || 'Failed to update item. Please try again.');
                                }
                            } catch (error) {
                                alert('Network error. Please check your connection.');
                            } finally {
                                this.updating = false;
                            }
                        },
                        async removeItem() {
                            if (this.removing || this.updating) return;
                            this.removing = true;
                            try {
                                const response = await fetch('{{ route('customer.cart.remove', [$table->code, $lineId]) }}', {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                                let data = {};
                                try {
                                    data = await response.json();
                                } catch (error) {
                                    data = {};
                                }
                                if (response.ok && data.success) {
                                    if (typeof data.cart_count === 'number') {
                                        window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                                    }
                                    const itemEl = this.$el.closest('[data-cart-item]');
                                    const lineTotal = Number(itemEl?.dataset.lineTotal || 0);
                                    if (itemEl) {
                                        itemEl.remove();
                                    }

                                    const state = document.getElementById('cart-state');
                                    if (state) {
                                        const taxRate = Number(state.dataset.taxRate || 0);
                                        const serviceRate = Number(state.dataset.serviceRate || 0);
                                        const currency = state.dataset.currency || '';
                                        const formatMoney = (value) => `${currency}${Number(value).toFixed(2)}`;
                                        const count = Math.max(0, Number(state.dataset.count || 0) - 1);
                                        const subtotal = Math.max(0, Number(state.dataset.subtotal || 0) - lineTotal);
                                        state.dataset.count = String(count);
                                        state.dataset.subtotal = String(subtotal);

                                        const tax = subtotal * taxRate;
                                        const service = subtotal * serviceRate;
                                        const total = subtotal + tax + service;

                                        const subtotalEl = document.getElementById('cart-subtotal');
                                        const taxEl = document.getElementById('cart-tax');
                                        const serviceEl = document.getElementById('cart-service');
                                        const totalEl = document.getElementById('cart-total');
                                        if (subtotalEl) subtotalEl.textContent = formatMoney(subtotal);
                                        if (taxEl) taxEl.textContent = formatMoney(tax);
                                        if (serviceEl) serviceEl.textContent = formatMoney(service);
                                        if (totalEl) totalEl.textContent = formatMoney(total);

                                        const countEl = document.getElementById('cart-item-count');
                                        const labelEl = document.getElementById('cart-item-label');
                                        if (countEl) countEl.textContent = String(count);
                                        if (labelEl) labelEl.textContent = count === 1 ? 'item' : 'items';

                                        if (count === 0) {
                                            const emptyState = document.getElementById('cart-empty-state');
                                            const cartBody = document.getElementById('cart-body');
                                            if (cartBody) {
                                                cartBody.classList.add('opacity-0');
                                                setTimeout(() => cartBody.classList.add('hidden'), 200);
                                            }
                                            if (emptyState) {
                                                emptyState.classList.remove('hidden');
                                                requestAnimationFrame(() => emptyState.classList.remove('opacity-0'));
                                            }
                                        }
                                    }
                                } else {
                                    this.removing = false;
                                    alert(data.message || 'Failed to remove item. Please try again.');
                                }
                            } catch (error) {
                                this.removing = false;
                                alert('Network error. Please check your connection.');
                            }
                        }
                     }">
                    <div class="flex justify-between items-start gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <h3 class="font-semibold text-slate-900">{{ $item['name'] }}</h3>
                                <span class="shrink-0 font-bold text-slate-900 ml-2" x-text="formatMoney(lineTotal)"></span>
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
                                    <div class="flex items-center gap-1.5">
                                        <button type="button"
                                                @click="updateQty(qty - 1)"
                                                :disabled="updating || qty <= 1"
                                                class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-medium text-sm hover:bg-slate-200 transition-colors disabled:opacity-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="w-6 text-center text-sm font-semibold tabular-nums" x-text="qty"></span>
                                        <button type="button"
                                                @click="updateQty(qty + 1)"
                                                :disabled="updating"
                                                class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center font-medium text-sm hover:bg-slate-800 transition-colors disabled:opacity-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <span x-text="`${currency}${Number(price).toFixed(2)} each`"></span>
                                </div>
                                <form action="{{ route('customer.cart.remove', [$table->code, $lineId]) }}"
                                      method="POST"
                                      @submit.prevent="removeItem()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            :disabled="removing || updating"
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
                    <span id="cart-subtotal" class="font-medium text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Tax ({{ config('pos.tax_rate', 0) * 100 }}%)</span>
                    <span id="cart-tax" class="text-slate-600">{{ config('pos.currency_symbol') }}{{ number_format($subtotal * config('pos.tax_rate', 0), 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Service ({{ config('pos.service_charge', 0) * 100 }}%)</span>
                    <span id="cart-service" class="text-slate-600">{{ config('pos.currency_symbol') }}{{ number_format($subtotal * config('pos.service_charge', 0), 2) }}</span>
                </div>
                <div class="border-t border-slate-200 pt-3 mt-3">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-slate-900">Estimated Total</span>
                        <span id="cart-total" class="text-xl font-bold text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($subtotal * (1 + config('pos.tax_rate', 0) + config('pos.service_charge', 0)), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Place Order Form -->
        <form x-data="{ 
                submitting: false,
                showError: false,
                errorMessage: '',
                customerNote: '',
                async placeOrder() {
                    if (this.submitting) return;
                    this.submitting = true;
                    try {
                        const response = await fetch('{{ route('customer.order.place', $table->code) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                customer_note: this.customerNote
                            })
                        });
                        const data = await response.json();
                        if (response.ok && data.success) {
                            window.location.href = data.redirect;
                        } else {
                            this.submitting = false;
                            this.errorMessage = data.message || 'Failed to place order. Please try again.';
                            this.showError = true;
                        }
                    } catch (error) {
                        this.submitting = false;
                        this.errorMessage = 'Network error. Please check your connection.';
                        this.showError = true;
                    }
                }
            }" @submit.prevent="placeOrder()">
            <div class="mb-5">
                <label for="customer_note" class="block text-sm font-medium text-slate-700 mb-2">Special Instructions</label>
                <textarea x-model="customerNote"
                          id="customer_note"
                          rows="2"
                          class="w-full border-slate-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 resize-none text-sm placeholder:text-slate-400"
                          placeholder="Any allergies or special requests for the kitchen?"></textarea>
            </div>
            <button type="submit"
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

            <!-- Error Dialog -->
            <div x-cloak x-show="showError"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center px-4"
                 aria-modal="true"
                 role="dialog">
                <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showError = false"></div>
                <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-100 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-slate-900">Notice</p>
                            <p class="text-sm text-slate-500" x-text="errorMessage"></p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button type="button"
                                @click="showError = false"
                                class="flex-1 py-2.5 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition-colors">
                            OK
                        </button>
                    </div>
                </div>
            </div>
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
    </div>
@endsection
