<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">{{ $campaign->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">View campaign details and manage coupons.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pos.coupons.edit', $campaign) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 rounded-xl text-white text-sm font-semibold shadow-sm hover:bg-indigo-500 transition">
                    Edit Campaign
                </a>
                <a href="{{ route('pos.coupons.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-700 text-sm font-semibold shadow-sm hover:bg-gray-50 transition">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        deleteCouponId: null,
        deleteCouponCode: '',
        showDeleteModal: false,
        disableCouponId: null,
        disableCouponCode: '',
        showDisableModal: false,
        enableCouponId: null,
        enableCouponCode: '',
        showEnableModal: false
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="text-sm font-medium text-gray-500">Total Amount</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ config('pos.currency_symbol') }}{{ number_format($campaign->total_amount ?? 0, 2) }}
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="text-sm font-medium text-gray-500">Coupon Value</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ config('pos.currency_symbol') }}{{ number_format($campaign->coupon_value ?? 0, 2) }}
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="text-sm font-medium text-gray-500">Total Coupons</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ $campaign->total_codes ?? $campaign->coupons()->count() }}
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="text-sm font-medium text-gray-500">Expires</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ $campaign->ends_at ? $campaign->ends_at->format('M d, Y') : '-' }}
                    </div>
                </div>
            </div>

            <!-- Coupons Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-gray-900">Coupons</h3>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('pos.coupons.show', $campaign) }}"
                               class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium {{ !request('status') ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                All ({{ $statusCounts['all'] }})
                            </a>
                            <a href="{{ route('pos.coupons.show', ['campaign' => $campaign, 'status' => 'unused']) }}"
                               class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') === 'unused' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                Unused ({{ $statusCounts['unused'] }})
                            </a>
                            <a href="{{ route('pos.coupons.show', ['campaign' => $campaign, 'status' => 'used']) }}"
                               class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') === 'used' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                Used ({{ $statusCounts['used'] }})
                            </a>
                            <a href="{{ route('pos.coupons.show', ['campaign' => $campaign, 'status' => 'expired']) }}"
                               class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') === 'expired' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                Expired ({{ $statusCounts['expired'] }})
                            </a>
                            <a href="{{ route('pos.coupons.show', ['campaign' => $campaign, 'status' => 'disabled']) }}"
                               class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') === 'disabled' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                Disabled ({{ $statusCounts['disabled'] }})
                            </a>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-6 pr-3 text-left text-sm font-semibold text-gray-900">Code</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Value</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Expires</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Used At</th>
                                <th class="relative py-3.5 pl-3 pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($coupons as $coupon)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                        <span class="font-mono text-sm font-medium text-gray-900">{{ $coupon->code }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                        {{ config('pos.currency_symbol') }}{{ number_format($coupon->value, 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @if($coupon->status === 'unused')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Unused</span>
                                        @elseif($coupon->status === 'used')
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">Used</span>
                                        @elseif($coupon->status === 'expired')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Expired</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Disabled</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $coupon->ends_at ? $coupon->ends_at->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $coupon->used_at ? $coupon->used_at->format('M d, Y H:i') : '-' }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                        @if($coupon->status === 'unused')
                                            <button type="button"
                                                    class="text-gray-600 hover:text-gray-900 mr-3"
                                                    @click="disableCouponId = {{ $coupon->id }}; disableCouponCode = '{{ $coupon->code }}'; showDisableModal = true">
                                                Disable
                                            </button>
                                        @endif
                                        @if($coupon->status === 'disabled')
                                            <button type="button"
                                                    class="text-green-600 hover:text-green-900 mr-3"
                                                    @click="enableCouponId = {{ $coupon->id }}; enableCouponCode = '{{ $coupon->code }}'; showEnableModal = true">
                                                Enable
                                            </button>
                                        @endif
                                        <button type="button"
                                                class="text-red-600 hover:text-red-900"
                                                @click="deleteCouponId = {{ $coupon->id }}; deleteCouponCode = '{{ $coupon->code }}'; showDeleteModal = true">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No coupons found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($coupons->hasPages())
                <div class="mt-6">
                    {{ $coupons->links() }}
                </div>
            @endif
        </div>

        <!-- Disable Modal -->
        <div x-cloak x-show="showDisableModal"
             x-transition
             class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showDisableModal = false"></div>
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6">
                <h2 class="text-lg font-semibold text-gray-900">Disable Coupon</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to disable coupon <span class="font-mono font-semibold" x-text="disableCouponCode"></span>? This coupon will no longer be redeemable.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showDisableModal = false"
                            class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50">
                        Cancel
                    </button>
                    <form :action="`{{ url('pos/coupon-codes') }}/${disableCouponId}/disable`" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-gray-600 rounded-xl hover:bg-gray-500">
                            Disable
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Enable Modal -->
        <div x-cloak x-show="showEnableModal"
             x-transition
             class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showEnableModal = false"></div>
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6">
                <h2 class="text-lg font-semibold text-gray-900">Enable Coupon</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to enable coupon <span class="font-mono font-semibold" x-text="enableCouponCode"></span>? This coupon will be redeemable again.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showEnableModal = false"
                            class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50">
                        Cancel
                    </button>
                    <form :action="`{{ url('pos/coupon-codes') }}/${enableCouponId}/enable`" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-500">
                            Enable
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-cloak x-show="showDeleteModal"
             x-transition
             class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6">
                <h2 class="text-lg font-semibold text-gray-900">Delete Coupon</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to delete coupon <span class="font-mono font-semibold" x-text="deleteCouponCode"></span>? This action cannot be undone.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50">
                        Cancel
                    </button>
                    <form :action="`{{ url('pos/coupon-codes') }}/${deleteCouponId}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-500">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
