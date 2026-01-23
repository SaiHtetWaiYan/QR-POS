<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">Edit Campaign</h2>
                <p class="text-sm text-slate-500 mt-1">Update campaign settings and review generated codes</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pos.coupons.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Campaigns
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100/50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-full space-y-6">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <h3 class="font-semibold text-gray-900 mb-4">Campaign Details</h3>
                <form action="{{ route('pos.coupons.update', $campaign->id) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                    @csrf
                    @method('PUT')
                    <div class="lg:col-span-4">
                        <label for="name" class="block text-xs font-semibold text-gray-500 mb-2">Campaign Name</label>
                        <input id="name" name="name" type="text" value="{{ $campaign->name }}" required
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="lg:col-span-2">
                        <label for="type" class="block text-xs font-semibold text-gray-500 mb-2">Type</label>
                        <select id="type" name="type" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="percent" {{ $campaign->type === 'percent' ? 'selected' : '' }}>Percent</option>
                            <option value="fixed" {{ $campaign->type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label for="value" class="block text-xs font-semibold text-gray-500 mb-2">Value</label>
                        <input id="value" name="value" type="number" step="0.01" min="0" required value="{{ $campaign->value }}"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="lg:col-span-2">
                        <label for="starts_at" class="block text-xs font-semibold text-gray-500 mb-2">Start</label>
                        <input id="starts_at" name="starts_at" type="datetime-local"
                               value="{{ $campaign->starts_at?->format('Y-m-d\TH:i') }}"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="lg:col-span-2">
                        <label for="ends_at" class="block text-xs font-semibold text-gray-500 mb-2">End</label>
                        <input id="ends_at" name="ends_at" type="datetime-local"
                               value="{{ $campaign->ends_at?->format('Y-m-d\TH:i') }}"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label for="max_uses_per_code" class="block text-xs font-semibold text-gray-500 mb-2">Max Uses</label>
                        <input id="max_uses_per_code" name="max_uses_per_code" type="number" min="1" value="{{ $campaign->max_uses_per_code }}"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label for="code_prefix" class="block text-xs font-semibold text-gray-500 mb-2">Prefix</label>
                        <input id="code_prefix" name="code_prefix" type="text" value="{{ $campaign->code_prefix }}"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm uppercase">
                    </div>
                    <div>
                        <label for="code_length" class="block text-xs font-semibold text-gray-500 mb-2">Random Length</label>
                        <input id="code_length" name="code_length" type="number" min="4" max="16" value="{{ $campaign->code_length }}" required
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ $campaign->is_active ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-xs font-semibold text-gray-500">Active</label>
                    </div>
                    <div class="lg:col-span-2">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-medium text-sm text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Generated Codes</h3>
                    <span class="text-xs text-gray-500">{{ $codes->total() }} total</span>
                </div>
                @if($codes->isEmpty())
                    <div class="text-center py-10 text-gray-400">
                        <p class="text-sm font-medium">No codes generated yet</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wide text-gray-400">
                                    <th class="pb-3">Code</th>
                                    <th class="pb-3">Uses</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Created</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($codes as $code)
                                    <tr>
                                        <td class="py-3 font-semibold text-gray-800">{{ $code->code }}</td>
                                        <td class="py-3 text-gray-600">
                                            {{ $code->uses_count }}
                                            @if($code->max_uses)
                                                / {{ $code->max_uses }}
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $code->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $code->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-gray-600">{{ $code->created_at?->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $codes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
