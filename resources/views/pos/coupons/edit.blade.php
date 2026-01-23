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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
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

                    <div>
                        <label for="ends_at" class="block text-sm font-medium text-gray-700">Expiration Date</label>
                        <input type="date" name="ends_at" id="ends_at"
                               value="{{ old('ends_at', $campaign->ends_at?->format('Y-m-d')) }}" required
                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
