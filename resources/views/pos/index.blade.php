<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    {{ __('POS Dashboard') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">{{ __('Manage incoming orders in real-time') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100/50" x-data="posDashboard({{ $pendingCount }}, {{ $activeCount }})">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <!-- Stats Overview -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900" x-text="pendingCount"></p>
                            <p class="text-xs text-gray-500">{{ __('Pending') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900" x-text="activeCount"></p>
                            <p class="text-xs text-gray-500">{{ __('In Kitchen') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(false && $topItems->isNotEmpty())
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">{{ __('Most ordered items') }}</p>
                            <p class="text-sm text-gray-500">{{ __('All time') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        @foreach($topItems as $item)
                            <div class="flex flex-1 min-w-0 items-center gap-4 rounded-2xl border border-gray-100 bg-gray-50/60 p-4">
                                @if($item->image_path)
                                    <img src="{{ asset('storage/'.$item->image_path) }}"
                                         alt="{{ $item->name_snapshot }}"
                                         class="h-14 w-14 rounded-xl object-cover border border-white shadow-sm">
                                @else
                                    <div class="h-14 w-14 rounded-xl bg-white border border-gray-200 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->name_snapshot }}</p>
                                    <p class="text-xs text-emerald-600 font-semibold mt-1">{{ $item->total_qty }} {{ __('ordered') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Kanban Board -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-full items-start">

                <!-- Pending Column -->
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 ring-4 ring-amber-100"></span>
                            {{ __('Pending') }}
                        </h3>
                        <span x-show="pendingCount > 0" x-text="pendingCount" class="bg-amber-100 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-full animate-pulse"></span>
                    </div>
                    <div id="pending-orders" class="bg-gradient-to-b from-amber-50/50 to-white rounded-2xl p-4 min-h-[500px] border border-amber-100/50 space-y-4">
                        @foreach($pending as $order)
                            @include('pos.partials.order_card', ['order' => $order])
                        @endforeach
                        @if($pending->isEmpty())
                            <div class="empty-state flex flex-col items-center justify-center h-48 text-gray-400">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500">{{ __('No pending orders') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('New orders will appear here') }}</p>
                            </div>
                        @endif
                        @if($pendingCount > 20)
                            <a href="{{ route('pos.history') }}"
                               class="block text-center text-xs text-indigo-600 hover:text-indigo-700 font-semibold pt-2">
                                {{ __('View all pending orders') }}
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Active Column -->
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 ring-4 ring-blue-100"></span>
                            {{ __('Kitchen') }}
                        </h3>
                        <span x-show="activeCount > 0"
                              x-text="activeCount"
                              class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full"></span>
                    </div>
                    <div id="kitchen-orders" class="bg-gradient-to-b from-blue-50/50 to-white rounded-2xl p-4 min-h-[500px] border border-blue-100/50 space-y-4">
                        @foreach($active as $order)
                            @include('pos.partials.order_card', ['order' => $order])
                        @endforeach
                        @if($active->isEmpty())
                            <div class="empty-state flex flex-col items-center justify-center h-48 text-gray-400">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500">{{ __('Kitchen is clear') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('Accept orders to start cooking') }}</p>
                            </div>
                        @endif
                        @if($activeCount > 20)
                            <a href="{{ route('pos.history') }}"
                               class="block text-center text-xs text-indigo-600 hover:text-indigo-700 font-semibold pt-2">
                                {{ __('View all kitchen orders') }}
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- Bill Request Alert Modal -->
        <div x-cloak x-show="showBillAlert"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             @click.self="dismissBillAlert()">
            <div x-show="showBillAlert"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden">
                <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-6 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">{{ __('Bill Requested!') }}</h3>
                    <p class="text-white/80 mt-1">{{ __('A customer is waiting for the bill') }}</p>
                    <span x-cloak x-show="billAlerts.length > 1"
                          class="mt-2 inline-flex items-center justify-center px-3 py-1 rounded-full bg-white/20 text-white text-xs font-semibold"
                          x-text="billAlerts.length"></span>
                </div>
                <div class="px-6 py-5 space-y-3 max-h-[50vh] overflow-y-auto">
                    <template x-for="alert in billAlerts" :key="alert.order_id">
                        <div class="border border-gray-100 rounded-2xl p-4">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>{{ __('Table') }}</span>
                                <span class="font-semibold text-gray-900" x-text="alert.table || @js(__('Unknown'))"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm text-gray-500 mt-2">
                                <span>{{ __('Order') }}</span>
                                <span class="font-mono text-gray-900" x-text="'#' + (alert.order_no || '')"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm text-gray-500 mt-2">
                                <span>{{ __('Total') }}</span>
                                <span class="text-lg font-bold text-gray-900" x-text="'{{ config('pos.currency_symbol') }}' + (alert.total ? parseFloat(alert.total).toFixed(2) : '0.00')"></span>
                            </div>
                            <div class="mt-3 flex gap-2">
                                <button @click="dismissBillAlert(alert.order_id)"
                                        class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors">
                                    {{ __('Dismiss') }}
                                </button>
                                <a :href="'/pos/orders/' + (alert.order_id || '')"
                                   class="flex-1 px-3 py-2 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 text-white text-xs font-semibold rounded-xl text-center transition-all shadow-lg shadow-violet-600/30">
                                    {{ __('View Order') }}
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="px-6 pb-6">
                    <button x-cloak x-show="billAlerts.length > 1"
                            @click="dismissBillAlert()"
                            class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                        {{ __('Dismiss All') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Notification Toast -->
        <div x-cloak x-show="showNotification"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed bottom-6 right-6 z-50">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-6 py-4 rounded-2xl shadow-2xl shadow-amber-500/30 flex items-center gap-4 max-w-md">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-sm">{{ __('New Order!') }}</p>
                    <p class="text-white/90 text-sm" x-text="notificationMessage"></p>
                </div>
                <button @click="showNotification = false" class="text-white/70 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .animate-slide-in {
            animation: slideIn 0.4s ease-out;
        }
    </style>
</x-app-layout>
