<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">{{ __('Coupon Campaigns') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage coupon campaigns and their generated coupons.') }}</p>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ deleteCampaignId: null, deleteCampaignName: '', showDeleteModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 mb-6">
                <a href="{{ route('pos.coupons.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 rounded-2xl text-white text-sm font-semibold shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 transition">
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    {{ __('New Campaign') }}
                </a>
            </div>
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-6 pr-3 text-left text-sm font-semibold text-gray-900">{{ __('Campaign') }}</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('Total Amount') }}</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('Coupon Value') }}</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('Coupons') }}</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('Status') }}</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('Expires') }}</th>
                                <th class="relative py-3.5 pl-3 pr-6">
                                    <span class="sr-only">{{ __('Actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($campaigns as $campaign)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                        <div class="font-medium text-gray-900">{{ $campaign->title }}</div>
                                        <div class="text-sm text-gray-500">{{ __('Created') }} {{ $campaign->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                        {{ config('pos.currency_symbol') }}{{ number_format($campaign->total_amount ?? 0, 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                        {{ config('pos.currency_symbol') }}{{ number_format($campaign->coupon_value ?? 0, 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $campaign->coupons_count ?? $campaign->total_codes ?? 0 }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @if($campaign->isExpired())
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">{{ __('Expired') }}</span>
                                        @elseif($campaign->is_active)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">{{ __('Active') }}</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $campaign->ends_at ? $campaign->ends_at->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                        <a href="{{ route('pos.coupons.show', $campaign) }}" class="text-gray-600 hover:text-gray-900 mr-3">{{ __('View') }}</a>
                                        <a href="{{ route('pos.coupons.edit', $campaign) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('Edit') }}</a>
                                        <button type="button"
                                                class="text-red-600 hover:text-red-900"
                                                @click="deleteCampaignId = {{ $campaign->id }}; deleteCampaignName = {{ Js::from($campaign->title) }}; showDeleteModal = true">
                                            {{ __('Delete') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        {{ __('No campaigns found. Create your first campaign to get started.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($campaigns->hasPages())
                <div class="mt-6">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Modal -->
        <div x-cloak x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Delete Campaign') }}</h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('Are you sure you want to delete') }} <span class="font-semibold" x-text="deleteCampaignName"></span>? {{ __('This will also delete all associated coupons. This action cannot be undone.') }}
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                            @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50">
                        {{ __('Cancel') }}
                    </button>
                    <form :action="`{{ url('pos/coupons') }}/${deleteCampaignId}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-500">
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
