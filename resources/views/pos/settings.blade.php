<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Settings</h2>
                <p class="text-sm text-gray-500 mt-1">Update tax and service charge rates.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <form action="{{ route('pos.settings.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="shop_name" class="block text-sm font-medium text-gray-700">Shop Name</label>
                            <input type="text" name="shop_name" id="shop_name"
                                   value="{{ old('shop_name', $shopName) }}"
                                   required
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('shop_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shop_phone" class="block text-sm font-medium text-gray-700">Telephone</label>
                            <input type="text" name="shop_phone" id="shop_phone"
                                   value="{{ old('shop_phone', $shopPhone) }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('shop_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="shop_address" class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="shop_address" id="shop_address"
                                   value="{{ old('shop_address', $shopAddress) }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('shop_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currency_symbol" class="block text-sm font-medium text-gray-700">Currency Symbol</label>
                            <input type="text" name="currency_symbol" id="currency_symbol"
                                   value="{{ old('currency_symbol', $currencySymbol) }}"
                                   maxlength="5" required
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('currency_symbol')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tax_rate" class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                            <div class="mt-1 flex items-center gap-2">
                                <input type="number" name="tax_rate" id="tax_rate"
                                       value="{{ old('tax_rate', number_format($taxRate, 2, '.', '')) }}"
                                       min="0" max="100" step="0.01" required
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            @error('tax_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="service_charge" class="block text-sm font-medium text-gray-700">Service Charge (%)</label>
                            <div class="mt-1 flex items-center gap-2">
                                <input type="number" name="service_charge" id="service_charge"
                                       value="{{ old('service_charge', number_format($serviceCharge, 2, '.', '')) }}"
                                       min="0" max="100" step="0.01" required
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            @error('service_charge')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                        These rates apply to new orders and are shown in the customer cart and receipts.
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <button type="submit"
                                class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-500 transition">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
