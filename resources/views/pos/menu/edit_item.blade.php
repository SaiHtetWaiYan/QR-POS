<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('pos.menu.index') }}"
               class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    {{ __('Edit Menu Item') }}
                </h2>
                <p class="text-sm text-gray-500">{{ $menuItem->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('pos.menu.items.update', $menuItem->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1.5">{{ __('Category') }}</label>
                        <select name="category_id"
                                class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $menuItem->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1.5">{{ __('Item Name') }}</label>
                        <input type="text"
                               name="name"
                               value="{{ $menuItem->name }}"
                               class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1.5">{{ __('Price') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">{{ config('pos.currency_symbol', '$') }}</span>
                            </div>
                            <input type="number"
                                   step="0.01"
                                   name="price"
                                   value="{{ $menuItem->price }}"
                                   class="w-full pl-8 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1.5">{{ __('Description') }}</label>
                        <textarea name="description"
                                  rows="3"
                                  class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ $menuItem->description }}</textarea>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1.5">{{ __('Image') }}</label>
                        @if($menuItem->image_path)
                            <div class="mb-3 flex items-center gap-4">
                                <img src="{{ asset('storage/' . $menuItem->image_path) }}"
                                     class="w-24 h-24 object-cover rounded-xl border border-gray-200"
                                     alt="{{ $menuItem->name }}">
                                <div class="text-sm text-gray-500">
                                    <p class="font-medium text-gray-700">{{ __('Current image') }}</p>
                                    <p class="text-xs">{{ __('Upload a new image to replace') }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-indigo-300 transition-colors">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <input type="file"
                                   name="image"
                                   accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-400 mt-2">{{ __('PNG, JPG up to 2MB') }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('pos.menu.index') }}"
                           class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium text-sm transition-colors">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit"
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-indigo-500/20 hover:shadow-xl transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('Update Item') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
