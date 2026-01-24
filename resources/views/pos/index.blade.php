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

    <div class="py-6 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100/50" x-data="{
        pendingCount: {{ $pendingCount }},
        showNotification: false,
        notificationMessage: '',
        notificationType: 'order',
        showBillAlert: false,
        billAlertData: null,
        init() {
            if (typeof Echo !== 'undefined') {
                Echo.private('pos')
                    .listen('.OrderPlaced', (e) => {
                        this.handleNewOrder(e);
                    })
                    .listen('.BillRequested', (e) => {
                        this.handleBillRequest(e);
                    });
            }
        },
        handleBillRequest(data) {
            this.playBillSound();
            this.billAlertData = data;
            this.showBillAlert = true;
        },
        playBillSound() {
            // More urgent sound for bill request
            const audio = new Audio('data:audio/wav;base64,UklGRl9vT19teleVBFZkZXNjcgAAAFNOT1RJRlkgQkVMTCBTT1VORAAAAAAASUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAADhAC7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7//////////////////////////////////////////////////////////////////8AAAAATGF2YzU4LjEzAAAAAAAAAAAAAAAAJAAAAAAAAAAAA4T/////AAAAAAAAAAAAAAAAAAAAAP/7kGQAAANUMEoFPeACNQV40, Grilbl AAD/+5JkAA/wAABpAAAACAAADSAAAAEAAAGkAAAAIAAANIAAAAQAAAaQAAAAgAA');
            audio.volume = 0.7;
            audio.play().catch(() => {});
            // Play twice for urgency
            setTimeout(() => audio.play().catch(() => {}), 300);
        },
        dismissBillAlert() {
            this.showBillAlert = false;
            this.billAlertData = null;
        },
        async handleNewOrder(orderData) {
            // Play notification sound
            this.playNotificationSound();

            // Fetch the order card HTML
            try {
                const response = await fetch(`/pos/orders/${orderData.order_id}/card`);
                if (response.ok) {
                    const html = await response.text();

                    // Insert the new order card at the top of pending column
                    const pendingContainer = document.getElementById('pending-orders');
                    const emptyState = pendingContainer.querySelector('.empty-state');

                    // Remove empty state if exists
                    if (emptyState) {
                        emptyState.remove();
                    }

                    // Create wrapper for animation
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = html;
                    wrapper.firstElementChild.classList.add('animate-slide-in', 'ring-2', 'ring-amber-400', 'ring-offset-2');

                    // Insert at the beginning
                    pendingContainer.insertBefore(wrapper.firstElementChild, pendingContainer.firstChild);

                    // Update pending count
                    this.pendingCount++;

                    // Show notification toast
                    this.showToast(`{{ __('New order from') }} ${orderData.table || @js(__('Table'))}: ${orderData.order_no}`);

                    // Remove highlight after 5 seconds
                    setTimeout(() => {
                        const newCard = pendingContainer.firstElementChild;
                        if (newCard) {
                            newCard.classList.remove('ring-2', 'ring-amber-400', 'ring-offset-2');
                        }
                    }, 5000);
                }
            } catch (error) {
                console.error('Failed to fetch order card:', error);
                // Fallback to reload
                window.location.reload();
            }
        },
        playNotificationSound() {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2JkZSQiHlsYF1le4mVm5eOf2xeVlxsg5OdnpaCbVlLTF55kZ6hmoRqUkE+T2+Lnqahk3pbRTc6VHSQoqWfj3RWQDA2VHKRpKehk3NZQC81VXWUpqijlHZcQzE4WXmYqaull3pgRTU8X3+crqupmH1kSD1BY4OgsK2snYJpS0JGaIejtK+tn4ZtT0dKboumuLGwoIlxVEtOcY+qurSyo41zV09TdpOtvbW0pJB3XFNYe5iwv7e2ppR8YFhdgJy0wLi4qJd/ZV1jhaC3wb27qpqDaWFniqS6w7+9rZyGbmVrjqi+xMHAr5+KcmlvkqzBxsLCsaKMdm1zmq/Dx8TEtKWPeXB3n7PGyMbGt6iSfHR7o7fIysrJuquVgHh/qLvLzMvLvrCYhH2DrL/NzdHOwrSdh4GGsMLP0NLRxLahioSJtMXS09TUx7mjjYiNuMnV1tfXyr2mj4yRu8vX2NnZzL+okY+UvtDa29vc0MGql5OYwtPc3d/e08Ssm5ecxdXf4ODh1cevnpugyd7h4uPi18qyoZ+jzuDj5OXl2sy0pKKm0uPl5ufn3c+2p6aq1eXn6Onp4NLAqqiu2Ojq6+vr49TBra2y2+vt7u7t5tXDsLC13O3v8PHw6NfFtLS54O/x8vLy6tnHt7e74/Hz9PT07NvJuru/5vP19vb17t3Lvr7C6fT29/f38N/MwMHF7PX3+Pj58eHOw8TI7vf4+fn5+OLQxcfL8Pj5+vr6+eTSx8nO8fn6+/v7++XUycrQ8/r7/Pz8/OfWy8zT9Pv8/f39/unYzs/W9vz9/v7+/+vaz9HZ9/3+////////7NzR0tz4/v////////7u3tPU3vn///////////zv4NXX4Pr////////////w4dfZ4vv////////////y49na5fz////////////05Nzc6P3////////////15t7e6v7////////////26N/g7P/////////////36uLi7v/////////////47OTk8P/////////////57ubm8v/////////////77+jo9P/////////////88Orq9v/////////////98uzs+P/////////////+9O7u+v////////////8=');
            audio.volume = 0.5;
            audio.play().catch(() => {});
        },
        showToast(message) {
            this.notificationMessage = message;
            this.showNotification = true;
            setTimeout(() => {
                this.showNotification = false;
            }, 5000);
        }
    }">
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
                            <p class="text-2xl font-bold text-gray-900">{{ $activeCount }}</p>
                            <p class="text-xs text-gray-500">{{ __('In Kitchen') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($topItems->isNotEmpty())
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
                        @if($activeCount > 0)
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $activeCount }}</span>
                        @endif
                    </div>
                    <div class="bg-gradient-to-b from-blue-50/50 to-white rounded-2xl p-4 min-h-[500px] border border-blue-100/50 space-y-4">
                        @foreach($active as $order)
                            @include('pos.partials.order_card', ['order' => $order])
                        @endforeach
                        @if($active->isEmpty())
                            <div class="flex flex-col items-center justify-center h-48 text-gray-400">
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
                 class="bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-8 text-center">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">{{ __('Bill Requested!') }}</h3>
                    <p class="text-white/80 mt-1">{{ __('A customer is waiting for the bill') }}</p>
                </div>
                <!-- Content -->
                <div class="px-6 py-5">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">{{ __('Table') }}</span>
                        <span class="font-bold text-gray-900" x-text="billAlertData?.table || @js(__('Unknown'))"></span>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">{{ __('Order') }}</span>
                        <span class="font-mono text-gray-900" x-text="'#' + (billAlertData?.order_no || '')"></span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-gray-500">{{ __('Total') }}</span>
                        <span class="text-xl font-bold text-gray-900" x-text="'{{ config('pos.currency_symbol') }}' + (billAlertData?.total ? parseFloat(billAlertData.total).toFixed(2) : '0.00')"></span>
                    </div>
                </div>
                <!-- Actions -->
                <div class="px-6 pb-6 flex gap-3">
                    <button @click="dismissBillAlert()"
                            class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                        {{ __('Dismiss') }}
                    </button>
                    <a :href="'/pos/orders/' + (billAlertData?.order_id || '')"
                       class="flex-1 px-4 py-3 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 text-white font-semibold rounded-xl text-center transition-all shadow-lg shadow-violet-600/30">
                        {{ __('View Order') }}
                    </a>
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
