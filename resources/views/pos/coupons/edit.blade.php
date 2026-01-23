<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Edit Campaign</h2>
                <p class="text-sm text-gray-500 mt-1">Update campaign settings. Total amount and coupon value cannot be changed.</p>
            </div>
            <a href="{{ route('pos.coupons.show', $campaign) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-700 text-sm font-semibold shadow-sm hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Campaign
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Campaign Stats (Read-only) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Campaign Summary</h3>
                    <p class="text-sm text-gray-500 mt-1">These values are locked and cannot be modified.</p>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Total Amount</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">
                            {{ config('pos.currency_symbol') }}{{ number_format($campaign->total_amount ?? 0, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Coupon Value</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">
                            {{ config('pos.currency_symbol') }}{{ number_format($campaign->coupon_value ?? 0, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Total Coupons</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">
                            {{ $campaign->total_codes ?? $campaign->coupons()->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <form action="{{ route('pos.coupons.update', $campaign) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Campaign Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $campaign->title) }}" required
                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{
                        open: false,
                        selected: '{{ old('ends_at', $campaign->ends_at?->format('Y-m-d')) }}',
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
                        <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-2">Expiration Date</label>
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
                                        Close
                                    </button>
                                    <span class="text-[10px] text-gray-400">Select a future date</span>
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

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $campaign->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Active Campaign
                        </label>
                    </div>
                    <p class="text-sm text-gray-500 -mt-4">When disabled, all unused coupons in this campaign will also be deactivated.</p>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('pos.coupons.show', $campaign) }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Cancel</a>
                        <button type="submit"
                                class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-500 transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
