<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Table Management') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage your restaurant tables and QR codes') }}</p>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ deleteModal: false, deleteTableId: null, deleteTableName: '' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 mb-6">
                <a href="{{ route('pos.tables.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 rounded-2xl font-medium text-sm text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Add Table') }}
                </a>
            </div>
            @if($tables->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 14h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 text-lg">{{ __('No tables yet') }}</h3>
                    <p class="text-gray-500 text-sm mt-1 mb-4">{{ __('Add your first table to generate a QR code for ordering.') }}</p>
                    <a href="{{ route('pos.tables.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 rounded-xl font-medium text-sm text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('Add First Table') }}
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($tables as $table)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:border-gray-200 transition-all">
                            <div class="p-5">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900 text-lg">{{ $table->name }}</h3>
                                            <code class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">{{ $table->code }}</code>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $table->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $table->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                                    <a href="{{ route('pos.tables.qr', $table->id) }}"
                                       class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-medium text-sm text-gray-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h2M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        {{ __('View QR') }}
                                    </a>
                                    <button type="button"
                                            @click="deleteModal = true; deleteTableId = {{ $table->id }}; deleteTableName = '{{ $table->name }}'"
                                            class="inline-flex items-center justify-center w-10 h-10 bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
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
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Delete Table') }}</h3>
                        <p class="text-gray-500 mb-6">{{ __('Are you sure you want to delete') }} <span class="font-semibold text-gray-700" x-text="deleteTableName"></span>? {{ __('This action cannot be undone.') }}</p>

                        <div class="flex gap-3">
                            <button type="button"
                                    @click="deleteModal = false"
                                    class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium text-sm text-gray-700 transition-colors">
                                {{ __('Cancel') }}
                            </button>
                            <form :action="`{{ url('pos/tables') }}/${deleteTableId}`" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 rounded-xl font-medium text-sm text-white transition-colors">
                                    {{ __('Delete Table') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
