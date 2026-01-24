<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Menu Management') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage your menu categories and items') }}</p>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        deleteModal: false,
        deleteType: '',
        deleteId: null,
        deleteName: '',
        editModal: false,
        editCategoryId: null,
        editCategoryName: '',
        editCategoryNameMy: '',
        openEditCategory(category) {
            this.editCategoryId = category.id;
            this.editCategoryName = category.name;
            this.editCategoryNameMy = category.nameMy;
            this.editModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                <a href="{{ route('pos.menu.items.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 rounded-2xl font-medium text-sm text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Add Menu Item') }}
                </a>
            </div>

            <!-- Add Category -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    {{ __('Add New Category') }}
                </h3>
                <form action="{{ route('pos.menu.categories.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="text"
                           name="name"
                           placeholder="{{ __('Category name (e.g., Appetizers, Main Course)') }}"
                           class="flex-1 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                           required>
                    <input type="text"
                           name="name_my"
                           placeholder="{{ __('Category name (Myanmar)') }}"
                           class="flex-1 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-900 rounded-xl font-medium text-sm text-white hover:bg-gray-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('Add Category') }}
                    </button>
                </form>
            </div>

            <!-- Categories & Items -->
            <div class="space-y-6">
                @foreach($categories as $category)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $category->display_name }}</h4>
                                    <p class="text-xs text-gray-500">
                                        {{ $category->menuItems->count() }} {{ trans_choice('ui.customer.items', $category->menuItems->count()) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button"
                                        @click="openEditCategory({ id: {{ $category->id }}, name: @js($category->name), nameMy: @js($category->name_my) })"
                                        class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center gap-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    {{ __('Edit') }}
                                </button>
                                <button type="button"
                                        @click="deleteModal = true; deleteType = 'category'; deleteId = {{ $category->id }}; deleteName = @js($category->display_name)"
                                        class="text-red-500 hover:text-red-600 text-sm font-medium flex items-center gap-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            @if($category->menuItems->isEmpty())
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">{{ __('No items in this category') }}</p>
                                    <a href="{{ route('pos.menu.items.create') }}" class="text-indigo-600 text-sm font-medium hover:text-indigo-700 mt-2 inline-block">{{ __('Add first item') }}</a>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($category->menuItems as $item)
                                        <div class="flex gap-4 p-4 rounded-xl border {{ $item->is_available ? 'bg-white border-gray-200 hover:border-gray-300' : 'bg-red-50 border-red-200' }} transition-colors">
                                            @if($item->image_path)
                                                <img src="{{ asset('storage/' . $item->image_path) }}"
                                                     class="w-20 h-20 object-cover rounded-xl shrink-0"
                                                     alt="{{ $item->display_name }}">
                                            @else
                                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl flex items-center justify-center shrink-0">
                                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div class="flex justify-between items-start gap-2">
                                                    <div class="min-w-0">
                                                        <h5 class="font-semibold text-gray-900 truncate">{{ $item->display_name }}</h5>
                                                        <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $item->display_description }}</p>
                                                    </div>
                                                    <span class="font-bold text-gray-900 shrink-0">{{ config('pos.currency_symbol') }}{{ number_format($item->price, 2) }}</span>
                                                </div>

                                                @if(!$item->is_available)
                                                    <span class="inline-block mt-2 px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full uppercase">{{ __('Unavailable') }}</span>
                                                @endif

                                                <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100">
                                                    <a href="{{ route('pos.menu.items.edit', $item->id) }}"
                                                       class="text-indigo-600 hover:text-indigo-700 text-xs font-medium flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        {{ __('Edit') }}
                                                    </a>

                                                    <form action="{{ route('pos.menu.items.toggle', $item->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <button type="submit"
                                                                class="{{ $item->is_available ? 'text-amber-600 hover:text-amber-700' : 'text-emerald-600 hover:text-emerald-700' }} text-xs font-medium flex items-center gap-1">
                                                            @if($item->is_available)
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                                </svg>
                                                                {{ __('Disable') }}
                                                            @else
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                                {{ __('Enable') }}
                                                            @endif
                                                        </button>
                                                    </form>

                                                    <button type="button"
                                                            @click="deleteModal = true; deleteType = 'item'; deleteId = {{ $item->id }}; deleteName = @js($item->display_name)"
                                                            class="text-red-500 hover:text-red-600 text-xs font-medium flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        {{ __('Delete') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if($categories->isEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 text-lg">{{ __('No categories yet') }}</h3>
                        <p class="text-gray-500 text-sm mt-1">{{ __('Start by adding a category above, then add menu items.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="deleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex min-h-full items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="deleteModal = false"></div>

                <!-- Modal Content -->
                <div x-show="deleteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">

                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            {{ __('Delete') }} <span x-text="deleteType === 'category' ? @js(__('Category')) : @js(__('Menu Item'))"></span>
                        </h3>
                        <p class="text-gray-500 mb-1">
                            {{ __('Are you sure you want to delete') }} <span class="font-semibold text-gray-700" x-text="deleteName"></span>?
                        </p>
                        <p class="text-gray-400 text-sm mb-6" x-show="deleteType === 'category'">{{ __('This will also delete all items in this category.') }}</p>
                        <p class="text-gray-400 text-sm mb-6" x-show="deleteType === 'item'">{{ __('This action cannot be undone.') }}</p>

                        <div class="flex gap-3">
                            <button type="button"
                                    @click="deleteModal = false"
                                    class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium text-sm text-gray-700 transition-colors">
                                {{ __('Cancel') }}
                            </button>
                            <form x-show="deleteType === 'category'" :action="`{{ url('pos/menu/categories') }}/${deleteId}`" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 rounded-xl font-medium text-sm text-white transition-colors">
                                    {{ __('Delete Category') }}
                                </button>
                            </form>
                            <form x-show="deleteType === 'item'" :action="`{{ url('pos/menu/items') }}/${deleteId}`" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 rounded-xl font-medium text-sm text-white transition-colors">
                                    {{ __('Delete Item') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div x-cloak x-show="editModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="editModal = false"></div>

                <div x-show="editModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('Edit Category') }}</h3>
                    <form :action="`{{ url('pos/menu/categories') }}/${editCategoryId}`" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Category Name') }}</label>
                            <input type="text"
                                   name="name"
                                   x-model="editCategoryName"
                                   class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Category Name (Myanmar)') }}</label>
                            <input type="text"
                                   name="name_my"
                                   x-model="editCategoryNameMy"
                                   class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button"
                                    @click="editModal = false"
                                    class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium text-sm text-gray-700 transition-colors">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                    class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 rounded-xl font-medium text-sm text-white transition-colors">
                                {{ __('Update Category') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
