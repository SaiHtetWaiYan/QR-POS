<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    {{ __('Order History') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Review orders by day</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm mb-6">
                <form method="GET" action="{{ route('pos.history') }}" class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                    <div class="flex-1" x-data="{
                        open: false,
                        selected: '{{ $date }}',
                        viewMonth: 0,
                        viewYear: 0,
                        init() {
                            const base = this.selected ? new Date(this.selected + 'T00:00:00') : new Date();
                            this.viewMonth = base.getMonth();
                            this.viewYear = base.getFullYear();
                        },
                        get formatted() {
                            if (!this.selected) return 'Select date';
                            const date = new Date(this.selected + 'T00:00:00');
                            return date.toLocaleDateString(undefined, { month: 'short', day: '2-digit', year: 'numeric' });
                        },
                        get monthLabel() {
                            return new Date(this.viewYear, this.viewMonth, 1).toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
                        },
                        get days() {
                            const start = new Date(this.viewYear, this.viewMonth, 1);
                            const end = new Date(this.viewYear, this.viewMonth + 1, 0);
                            const startDay = start.getDay();
                            const totalDays = end.getDate();
                            const cells = [];
                            for (let i = 0; i < startDay; i++) cells.push(null);
                            for (let d = 1; d <= totalDays; d++) cells.push(new Date(this.viewYear, this.viewMonth, d));
                            return cells;
                        },
                        isToday(date) {
                            if (!date) return false;
                            const now = new Date();
                            return date.getFullYear() === now.getFullYear()
                                && date.getMonth() === now.getMonth()
                                && date.getDate() === now.getDate();
                        },
                        isSelected(date) {
                            if (!date || !this.selected) return false;
                            const selected = new Date(this.selected + 'T00:00:00');
                            return date.getFullYear() === selected.getFullYear()
                                && date.getMonth() === selected.getMonth()
                                && date.getDate() === selected.getDate();
                        },
                        selectDate(date) {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            this.selected = `${year}-${month}-${day}`;
                            this.open = false;
                        },
                        prevMonth() {
                            if (this.viewMonth === 0) {
                                this.viewMonth = 11;
                                this.viewYear -= 1;
                            } else {
                                this.viewMonth -= 1;
                            }
                        },
                        nextMonth() {
                            if (this.viewMonth === 11) {
                                this.viewMonth = 0;
                                this.viewYear += 1;
                            } else {
                                this.viewMonth += 1;
                            }
                        }
                    }" @keydown.escape.window="open = false">
                        <label for="history_date" class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                        <button type="button"
                                @click="open = !open"
                                class="w-full max-w-xs flex items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                            <span class="flex items-center gap-2">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <span x-text="formatted"></span>
                            </span>
                            <span class="text-gray-400 text-xs">Change</span>
                        </button>
                        <div x-cloak x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 transform -translate-y-1"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-1"
                             class="relative max-w-xs">
                            <div class="absolute mt-3 w-full rounded-2xl border border-gray-200 bg-white shadow-xl p-4 z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <button type="button"
                                            @click="prevMonth()"
                                            class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-500">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>
                                    <div class="text-sm font-semibold text-gray-800" x-text="monthLabel"></div>
                                    <button type="button"
                                            @click="nextMonth()"
                                            class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-500">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-7 gap-1 text-[10px] text-gray-400 mb-2">
                                    <span class="text-center">Su</span>
                                    <span class="text-center">Mo</span>
                                    <span class="text-center">Tu</span>
                                    <span class="text-center">We</span>
                                    <span class="text-center">Th</span>
                                    <span class="text-center">Fr</span>
                                    <span class="text-center">Sa</span>
                                </div>
                                <div class="grid grid-cols-7 gap-1">
                                    <template x-for="(day, index) in days" :key="index">
                                        <div class="h-8">
                                            <button type="button"
                                                    x-show="day"
                                                    @click="selectDate(day)"
                                                    :class="isSelected(day)
                                                        ? 'bg-indigo-600 text-white'
                                                        : isToday(day)
                                                            ? 'bg-indigo-50 text-indigo-700'
                                                            : 'text-gray-700 hover:bg-gray-100'"
                                                    class="w-8 h-8 rounded-lg text-xs font-semibold transition-colors mx-auto">
                                                <span x-text="day ? day.getDate() : ''"></span>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <div class="pt-3 mt-3 border-t border-gray-100 flex items-center justify-between">
                                    <button type="button"
                                            @click="selectDate(new Date())"
                                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                                        Today
                                    </button>
                                    <button type="button"
                                            @click="open = false"
                                            class="text-xs font-semibold text-gray-500 hover:text-gray-600">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="date"
                               id="history_date"
                               name="date"
                               value="{{ $date }}"
                               x-model="selected"
                               class="sr-only">
                        <p class="text-xs text-gray-400 mt-2">Pick a date to review completed orders.</p>
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

            @php
                $ordersJson = $orders->map(fn ($order) => [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'table' => $order->table->name ?? 'Table',
                    'time' => $order->created_at->format('h:i A'),
                    'status' => $order->status,
                    'total' => $order->total,
                    'url' => route('pos.orders.show', $order->id),
                ]);
            @endphp
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm"
                 x-data="{
                    query: '',
                    visible: 10,
                    orders: {{ Js::from($ordersJson) }},
                    get filtered() {
                        if (!this.query) return this.orders;
                        const q = this.query.toLowerCase();
                        return this.orders.filter(order =>
                            order.order_no.toLowerCase().includes(q) ||
                            order.table.toLowerCase().includes(q)
                        );
                    },
                    get visibleOrders() {
                        return this.filtered.slice(0, this.visible);
                    },
                    statusClass(status) {
                        return {
                            pending: 'bg-amber-100 text-amber-700',
                            accepted: 'bg-blue-100 text-blue-700',
                            preparing: 'bg-orange-100 text-orange-700',
                            served: 'bg-indigo-100 text-indigo-700',
                            paid: 'bg-emerald-100 text-emerald-700',
                            cancelled: 'bg-red-100 text-red-700',
                        }[status] || 'bg-gray-100 text-gray-700';
                    },
                    formatMoney(value) {
                        return '{{ config('pos.currency_symbol') }}' + Number(value).toFixed(2);
                    }
                 }">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">Orders</h3>
                        <span class="text-xs text-gray-500" x-text="`${filtered.length} of ${orders.length}`"></span>
                    </div>
                    <input type="text"
                           x-model="query"
                           placeholder="Search..."
                           class="w-40 rounded-xl border border-gray-200 px-3 py-2 text-xs text-gray-700 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>

                <template x-if="orders.length === 0">
                    <div class="text-center py-12 text-gray-400">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">No orders for this day</p>
                    </div>
                </template>

                <template x-if="orders.length > 0 && filtered.length === 0">
                    <div class="text-center py-12 text-gray-400">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">No matching orders</p>
                    </div>
                </template>

                <div class="space-y-3" x-show="filtered.length > 0">
                    <template x-for="order in visibleOrders" :key="order.id">
                        <div class="border border-gray-100 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-gray-900" x-text="order.order_no"></p>
                                <p class="text-xs text-gray-500">
                                    <span x-text="order.table"></span> • <span x-text="order.time"></span>
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                      :class="statusClass(order.status)"
                                      x-text="order.status.charAt(0).toUpperCase() + order.status.slice(1)"></span>
                                <span class="font-semibold text-gray-900" x-text="formatMoney(order.total)"></span>
                                <a :href="order.url"
                                   class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold">
                                    View
                                </a>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 flex justify-center" x-show="filtered.length > visible">
                    <button type="button"
                            @click="visible += 10"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-all">
                        Load more
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
