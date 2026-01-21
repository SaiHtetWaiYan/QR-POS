<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    {{ __('POS Dashboard') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Manage incoming orders in real-time</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pos.menu.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Menu
                </a>
                <a href="{{ route('pos.tables.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-medium text-sm text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Tables
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100/50" x-data="{
        init() {
            if (typeof Echo !== 'undefined') {
                Echo.private('pos')
                    .listen('.OrderPlaced', (e) => {
                        console.log('New order received:', e);
                        window.location.reload();
                    });
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <!-- Stats Overview -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $pending->count() }}</p>
                            <p class="text-xs text-gray-500">Pending</p>
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
                            <p class="text-2xl font-bold text-gray-900">{{ $active->count() }}</p>
                            <p class="text-xs text-gray-500">In Kitchen</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $completed->count() }}</p>
                            <p class="text-xs text-gray-500">Complete</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanban Board -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-full items-start">

                <!-- Pending Column -->
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 ring-4 ring-amber-100"></span>
                            Pending
                        </h3>
                        @if($pending->count() > 0)
                            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-full animate-pulse">{{ $pending->count() }}</span>
                        @endif
                    </div>
                    <div class="bg-gradient-to-b from-amber-50/50 to-white rounded-2xl p-4 min-h-[500px] border border-amber-100/50 space-y-4">
                        @foreach($pending as $order)
                            @include('pos.partials.order_card', ['order' => $order])
                        @endforeach
                        @if($pending->isEmpty())
                            <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500">No pending orders</p>
                                <p class="text-xs text-gray-400 mt-1">New orders will appear here</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Active Column -->
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 ring-4 ring-blue-100"></span>
                            Kitchen
                        </h3>
                        @if($active->count() > 0)
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $active->count() }}</span>
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
                                <p class="text-sm font-medium text-gray-500">Kitchen is clear</p>
                                <p class="text-xs text-gray-400 mt-1">Accept orders to start cooking</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Completed Column -->
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                            Ready / Paid
                        </h3>
                        @if($completed->count() > 0)
                            <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $completed->count() }}</span>
                        @endif
                    </div>
                    <div class="bg-gradient-to-b from-emerald-50/50 to-white rounded-2xl p-4 min-h-[500px] border border-emerald-100/50 space-y-4">
                        @foreach($completed as $order)
                            @include('pos.partials.order_card', ['order' => $order])
                        @endforeach
                        @if($completed->isEmpty())
                            <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500">No completed orders</p>
                                <p class="text-xs text-gray-400 mt-1">Served orders appear here</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
