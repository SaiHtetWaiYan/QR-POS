<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    {{ __('Discount Codes') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Create and manage customer discount codes</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pos.coupons.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8L9 19l-4-4-4 4V7a2 2 0 012-2h10z"/>
                    </svg>
                    Coupons
                </a>
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

    <div class="py-6 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full space-y-6">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <h3 class="font-semibold text-gray-900 mb-4">Create Discount Code</h3>
                <form action="{{ route('pos.discounts.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-6 gap-4 items-end">
                    @csrf
                    <div class="lg:col-span-2">
                        <label for="code" class="block text-xs font-semibold text-gray-500 mb-2">Code</label>
                        <input id="code" name="code" type="text" required
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm uppercase"
                               placeholder="SAVE10">
                    </div>
                    <div>
                        <label for="type" class="block text-xs font-semibold text-gray-500 mb-2">Type</label>
                        <select id="type" name="type" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="percent">Percent</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </div>
                    <div>
                        <label for="value" class="block text-xs font-semibold text-gray-500 mb-2">Value</label>
                        <input id="value" name="value" type="number" step="0.01" min="0" required
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                               placeholder="10">
                    </div>
                    <div>
                        <label for="starts_at" class="block text-xs font-semibold text-gray-500 mb-2">Start</label>
                        <input id="starts_at" name="starts_at" type="datetime-local"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label for="ends_at" class="block text-xs font-semibold text-gray-500 mb-2">End</label>
                        <input id="ends_at" name="ends_at" type="datetime-local"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label for="max_uses" class="block text-xs font-semibold text-gray-500 mb-2">Max Uses</label>
                        <input id="max_uses" name="max_uses" type="number" min="1"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                               placeholder="100">
                    </div>
                    <div class="flex items-center gap-2 lg:col-span-2">
                        <input id="is_active" name="is_active" type="checkbox" value="1" checked
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-xs font-semibold text-gray-500">Active</label>
                    </div>
                    <div class="lg:col-span-2">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-medium text-sm text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 transition-all">
                            Create Code
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Existing Codes</h3>
                    <span class="text-xs text-gray-500">{{ $codes->count() }} total</span>
                </div>
                @if($codes->isEmpty())
                    <div class="text-center py-10 text-gray-400">
                        <p class="text-sm font-medium">No discount codes yet</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($codes as $code)
                            <div class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Code</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $code->code }}</p>
                                    </div>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $code->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $code->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-500">
                                    <div class="rounded-xl bg-slate-50 p-2">
                                        <p class="text-[10px] uppercase tracking-wide text-gray-400">Type</p>
                                        <p class="font-semibold text-gray-700">{{ ucfirst($code->type) }}</p>
                                    </div>
                                    <div class="rounded-xl bg-slate-50 p-2">
                                        <p class="text-[10px] uppercase tracking-wide text-gray-400">Value</p>
                                        <p class="font-semibold text-gray-700">
                                            @if($code->type === 'percent')
                                                {{ number_format($code->value, 2) }}%
                                            @else
                                                {{ config('pos.currency_symbol') }}{{ number_format($code->value, 2) }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="rounded-xl bg-slate-50 p-2">
                                        <p class="text-[10px] uppercase tracking-wide text-gray-400">Start</p>
                                        <p class="font-semibold text-gray-700">{{ $code->starts_at?->format('Y-m-d H:i') ?? '-' }}</p>
                                    </div>
                                    <div class="rounded-xl bg-slate-50 p-2">
                                        <p class="text-[10px] uppercase tracking-wide text-gray-400">End</p>
                                        <p class="font-semibold text-gray-700">{{ $code->ends_at?->format('Y-m-d H:i') ?? '-' }}</p>
                                    </div>
                                    <div class="rounded-xl bg-slate-50 p-2 col-span-2">
                                        <p class="text-[10px] uppercase tracking-wide text-gray-400">Uses</p>
                                        <p class="font-semibold text-gray-700">
                                            {{ $code->uses_count }}
                                            @if($code->max_uses)
                                                / {{ $code->max_uses }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center gap-3">
                                    <form action="{{ route('pos.discounts.toggle', $code->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-full text-xs font-semibold text-indigo-600 hover:text-indigo-700 border border-indigo-100 hover:border-indigo-200 rounded-xl py-2">
                                            {{ $code->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('pos.discounts.destroy', $code->id) }}" method="POST" onsubmit="return confirm('Delete this code?')" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full text-xs font-semibold text-red-600 hover:text-red-700 border border-red-100 hover:border-red-200 rounded-xl py-2">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
