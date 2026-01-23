<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">Coupon Campaigns</h2>
                <p class="text-sm text-slate-500 mt-1">Manage coupon campaigns and their generated coupons.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pos.reports') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10V9m-9 8h10a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Reports
                </a>
                <a href="{{ route('pos.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-medium text-sm text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-semibold text-slate-900">Coupon Campaigns</h3>
                    <p class="text-sm text-slate-500">Manage coupon campaigns and their generated coupons.</p>
                </div>
                <a href="#create-campaign"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold shadow-sm shadow-indigo-600/30 hover:bg-indigo-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Campaign
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-100/70 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-4">Campaign</div>
                        <div class="col-span-2">Total Amount</div>
                        <div class="col-span-2">Coupon Value</div>
                        <div class="col-span-1">Coupons</div>
                        <div class="col-span-1">Status</div>
                        <div class="col-span-2">Expires</div>
                    </div>
                </div>
                @if($campaigns->isEmpty())
                    <div class="px-5 py-12 text-center text-slate-400 text-sm">
                        No campaigns found.
                    </div>
                @else
                    <div class="divide-y divide-slate-200">
                        @foreach($campaigns as $campaign)
                            <div class="px-5 py-4 text-sm text-slate-700">
                                <div class="grid grid-cols-12 gap-4 items-center">
                                    <div class="col-span-4">
                                        <p class="font-semibold text-slate-900">{{ $campaign->name }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $campaign->starts_at?->format('Y-m-d H:i') ?? 'No start' }}
                                        </p>
                                    </div>
                                    <div class="col-span-2 font-semibold text-slate-900">
                                        {{ config('pos.currency_symbol') }}{{ number_format($campaign->total_discount_amount ?? 0, 2) }}
                                    </div>
                                    <div class="col-span-2">
                                        @if($campaign->type === 'percent')
                                            {{ number_format($campaign->value, 2) }}%
                                        @else
                                            {{ config('pos.currency_symbol') }}{{ number_format($campaign->value, 2) }}
                                        @endif
                                    </div>
                                    <div class="col-span-1">{{ $campaign->discount_codes_count }}</div>
                                    <div class="col-span-1">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $campaign->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <div class="col-span-2 text-slate-500">
                                        {{ $campaign->ends_at?->format('Y-m-d H:i') ?? '-' }}
                                    </div>
                                </div>
                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <a href="{{ route('pos.coupons.edit', $campaign->id) }}"
                                       class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                                        Edit
                                    </a>
                                    <form action="{{ route('pos.coupons.generate', $campaign->id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <input name="generate_quantity" type="number" min="1" max="1000" value="25"
                                               class="w-20 rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                        <button type="submit"
                                                class="text-xs font-semibold text-slate-700 hover:text-slate-900">
                                            Generate
                                        </button>
                                    </form>
                                    <form action="{{ route('pos.coupons.toggle', $campaign->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-xs font-semibold text-slate-700 hover:text-slate-900">
                                            {{ $campaign->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('pos.coupons.destroy', $campaign->id) }}" method="POST" onsubmit="return confirm('Delete this campaign and its codes?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs font-semibold text-red-600 hover:text-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="create-campaign" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <h3 class="font-semibold text-gray-900 mb-4">Create Campaign</h3>
                <form action="{{ route('pos.coupons.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                    @csrf
                    <div class="lg:col-span-3">
                        <label for="name" class="block text-xs font-semibold text-gray-500 mb-2">Campaign Name</label>
                        <input id="name" name="name" type="text" required
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                               placeholder="Summer Sale">
                    </div>
                    <div class="lg:col-span-2">
                        <label for="type" class="block text-xs font-semibold text-gray-500 mb-2">Type</label>
                        <select id="type" name="type" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="percent">Percent</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label for="value" class="block text-xs font-semibold text-gray-500 mb-2">Value</label>
                        <input id="value" name="value" type="number" step="0.01" min="0" required
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                               placeholder="10">
                    </div>
                    <div class="lg:col-span-2">
                        <label for="starts_at" class="block text-xs font-semibold text-gray-500 mb-2">Start</label>
                        <input id="starts_at" name="starts_at" type="datetime-local"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="lg:col-span-2">
                        <label for="ends_at" class="block text-xs font-semibold text-gray-500 mb-2">End</label>
                        <input id="ends_at" name="ends_at" type="datetime-local"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label for="max_uses_per_code" class="block text-xs font-semibold text-gray-500 mb-2">Max Uses</label>
                        <input id="max_uses_per_code" name="max_uses_per_code" type="number" min="1"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                               placeholder="1">
                    </div>
                    <div>
                        <label for="code_prefix" class="block text-xs font-semibold text-gray-500 mb-2">Prefix</label>
                        <input id="code_prefix" name="code_prefix" type="text"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm uppercase"
                               placeholder="SAVE">
                    </div>
                    <div>
                        <label for="code_length" class="block text-xs font-semibold text-gray-500 mb-2">Random Length</label>
                        <input id="code_length" name="code_length" type="number" min="4" max="16" value="8" required
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label for="generate_quantity" class="block text-xs font-semibold text-gray-500 mb-2">Generate</label>
                        <input id="generate_quantity" name="generate_quantity" type="number" min="1" max="1000" value="50" required
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="is_active" name="is_active" type="checkbox" value="1" checked
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-xs font-semibold text-gray-500">Active</label>
                    </div>
                    <div class="lg:col-span-2">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-medium text-sm text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 transition-all">
                            Create Campaign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
