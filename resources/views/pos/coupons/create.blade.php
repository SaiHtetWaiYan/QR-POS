<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Create Coupon Campaign</h2>
                <p class="text-sm text-gray-500 mt-1">Set up a new coupon campaign. Coupons will be automatically generated based on total amount divided by coupon value.</p>
            </div>
            <a href="{{ route('pos.coupons.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-700 text-sm font-semibold shadow-sm hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="{{ route('pos.coupons.store') }}" method="POST" class="p-6 space-y-6" x-data="campaignForm()">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Campaign Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required autofocus
                               placeholder="e.g., Holiday Sale 2024"
                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Campaign Amount ({{ config('pos.currency_symbol') }})</label>
                            <input type="number" name="total_amount" id="total_amount" step="0.01" min="1"
                                   value="{{ old('total_amount') }}" required x-model="totalAmount"
                                   placeholder="e.g., 1000.00"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('total_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="coupon_value" class="block text-sm font-medium text-gray-700">Value Per Coupon ({{ config('pos.currency_symbol') }})</label>
                            <input type="number" name="coupon_value" id="coupon_value" step="0.01" min="0.01"
                                   value="{{ old('coupon_value') }}" required x-model="couponValue"
                                   placeholder="e.g., 10.00"
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
                                    This campaign will generate <span class="font-semibold" x-text="calculatedCoupons"></span> coupons worth {{ config('pos.currency_symbol') }}<span x-text="parseFloat(couponValue || 0).toFixed(2)"></span> each.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="ends_at" class="block text-sm font-medium text-gray-700">Expiration Date</label>
                        <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at') }}" required
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('ends_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('pos.coupons.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Cancel</a>
                        <button type="submit"
                                class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-500 transition">
                            Create Campaign
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
