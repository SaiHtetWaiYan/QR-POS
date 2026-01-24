<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">{{ __('Create Coupon Campaign') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Set up a new coupon campaign. Coupons will be automatically generated based on total amount divided by coupon value.') }}</p>
            </div>
            <a href="{{ route('pos.coupons.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-700 text-sm font-semibold shadow-sm hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <form action="{{ route('pos.coupons.store') }}" method="POST" class="p-6 space-y-6" x-data="campaignForm()">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">{{ __('Campaign Title') }}</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required autofocus
                               placeholder="{{ __('e.g., Holiday Sale 2024') }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="total_amount" class="block text-sm font-medium text-gray-700">{{ __('Total Campaign Amount') }} ({{ config('pos.currency_symbol') }})</label>
                            <input type="number" name="total_amount" id="total_amount" step="0.01" min="1"
                                   value="{{ old('total_amount') }}" required x-model="totalAmount"
                                   placeholder="{{ __('e.g., 1000.00') }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('total_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="coupon_value" class="block text-sm font-medium text-gray-700">{{ __('Value Per Coupon') }} ({{ config('pos.currency_symbol') }})</label>
                            <input type="number" name="coupon_value" id="coupon_value" step="0.01" min="0.01"
                                   value="{{ old('coupon_value') }}" required x-model="couponValue"
                                   placeholder="{{ __('e.g., 10.00') }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('coupon_value')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div x-show="calculatedCoupons > 0" class="rounded-xl bg-blue-50 p-4">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    {{ __('This campaign will generate') }} <span class="font-semibold" x-text="calculatedCoupons"></span> {{ __('coupons worth') }} {{ config('pos.currency_symbol') }}<span x-text="parseFloat(couponValue || 0).toFixed(2)"></span> {{ __('each') }}.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div x-data="{
                        open: false,
                        selected: '{{ old('ends_at') }}',
                        viewMonth: 0,
                        viewYear: 0,
                        minDate: null,
                        init() {
                            const tomorrow = new Date();
                            tomorrow.setDate(tomorrow.getDate() + 1);
                            tomorrow.setHours(0, 0, 0, 0);
                            this.minDate = tomorrow;
                            const base = this.selected ? new Date(this.selected + 'T00:00:00') : new Date();
                            const viewBase = base < this.minDate ? this.minDate : base;
                            this.viewMonth = viewBase.getMonth();
                            this.viewYear = viewBase.getFullYear();
                        },
                        get formatted() {
                            if (!this.selected) return @js(__('Select date'));
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
                        isDisabled(date) {
                            if (!date) return true;
                            return date < this.minDate;
                        },
                        selectDate(date) {
                            if (this.isDisabled(date)) return;
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
                        <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Expiration Date') }}</label>
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
                            <span class="text-gray-400 text-xs">{{ __('Change') }}</span>
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
                                    <span class="text-center">{{ __('Su') }}</span>
                                    <span class="text-center">{{ __('Mo') }}</span>
                                    <span class="text-center">{{ __('Tu') }}</span>
                                    <span class="text-center">{{ __('We') }}</span>
                                    <span class="text-center">{{ __('Th') }}</span>
                                    <span class="text-center">{{ __('Fr') }}</span>
                                    <span class="text-center">{{ __('Sa') }}</span>
                                </div>
                                <div class="grid grid-cols-7 gap-1">
                                    <template x-for="(day, index) in days" :key="index">
                                        <div class="h-8">
                                            <button type="button"
                                                    x-show="day"
                                                    @click="selectDate(day)"
                                                    :disabled="isDisabled(day)"
                                                    :class="isSelected(day)
                                                        ? 'bg-indigo-600 text-white'
                                                        : isDisabled(day)
                                                            ? 'text-gray-300 cursor-not-allowed'
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
                                            @click="open = false"
                                            class="text-xs font-semibold text-gray-500 hover:text-gray-600">
                                        {{ __('Close') }}
                                    </button>
                                    <span class="text-[10px] text-gray-400">{{ __('Select a future date') }}</span>
                                </div>
                            </div>
                        </div>
                        <input type="date"
                               id="ends_at"
                               name="ends_at"
                               required
                               x-model="selected"
                               class="sr-only">
                        @error('ends_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('pos.coupons.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                        <button type="submit"
                                class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-500 transition">
                            {{ __('Create Campaign') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function campaignForm() {
            return {
                totalAmount: {{ old('total_amount', 0) }},
                couponValue: {{ old('coupon_value', 0) }},
                get calculatedCoupons() {
                    if (this.totalAmount > 0 && this.couponValue > 0) {
                        return Math.floor(this.totalAmount / this.couponValue);
                    }
                    return 0;
                }
            }
        }
    </script>
</x-app-layout>
