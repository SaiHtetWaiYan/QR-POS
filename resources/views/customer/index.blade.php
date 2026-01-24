@extends('layouts.customer')

@section('header')
    {{ $table->name }}
@endsection

@section('subheader')
    {{ __('Browse our menu') }}
@endsection

@section('content')
    @if($activeOrder)
        <a href="{{ route('customer.status', $table->code) }}"
           class="mb-5 bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-4 flex justify-between items-center shadow-lg shadow-amber-500/20 animate-slide-up">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-white">
                    <p class="font-semibold text-sm">{{ __('Order') }} #{{ $activeOrder->order_no }}</p>
                    <p class="text-xs text-white/80">{{ __(ucfirst($activeOrder->status)) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-white">
                <span class="text-sm font-medium">{{ __('View') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
    @endif

    @if($topItems->isNotEmpty())
        <div class="mb-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">{{ __('Most ordered today') }}</p>
                    <p class="text-base font-semibold text-slate-900">{{ __('Popular picks') }}</p>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($topItems as $topItem)
                    <form x-data="{ 
                            qty: 1, 
                            adding: false,
                            note: '',
                            noteOpen: false,
                            async addToCart() {
                                if (this.adding) return;
                                this.adding = true;
                                try {
                                    const response = await fetch('{{ route('customer.cart.add', $table->code) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            menu_item_id: {{ $topItem->id }},
                                            qty: this.qty,
                                            note: this.note
                                        })
                                    });
                                    let data = {};
                                    try {
                                        data = await response.json();
                                    } catch (error) {
                                        data = {};
                                    }
                                    if (response.ok && data.success) {
                                        if (typeof data.cart_count === 'number') {
                                            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                                        }
                                        if (data.message) {
                                            const itemName = data.item_name ? `: ${data.item_name}` : '';
                                            const message = `${data.message}${itemName}`;
                                            window.dispatchEvent(new CustomEvent('toast', { detail: { message } }));
                                        }
                                        this.adding = false;
                                    } else {
                                        this.adding = false;
                                        alert(data.message || @js(__('Failed to add item. Please try again.')));
                                    }
                                } catch (error) {
                                    this.adding = false;
                                    alert(@js(__('Network error. Please check your connection.')));
                                }
                            }
                        }" @submit.prevent="addToCart()">
                        <input type="hidden" name="menu_item_id" value="{{ $topItem->id }}">
                        <input type="hidden" name="qty" :value="qty">

                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
                            <div class="flex">
                                @if($topItem->image_path)
                                    <div class="relative w-28 h-28 shrink-0">
                                        <img src="{{ asset('storage/' . $topItem->image_path) }}"
                                             alt="{{ $topItem->display_name }}"
                                             class="w-full h-full object-cover">
                                        @if(!$topItem->is_available)
                                            <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">{{ __('Sold Out') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="w-28 h-28 shrink-0 bg-gradient-to-br from-slate-100 to-slate-50 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                <div class="flex-1 p-3 flex flex-col justify-between min-w-0">
                                    <div>
                                        <h3 class="font-semibold text-slate-900 text-sm leading-tight">{{ $topItem->display_name }}</h3>
                                        @if($topItem->display_description)
                                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $topItem->display_description }}</p>
                                        @endif
                                    </div>
                                    <div class="flex justify-between items-end mt-2">
                                        <span class="font-bold text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($topItem->price, 2) }}</span>

                                        @if($topItem->is_available ?? true)
                                            <div class="flex items-center gap-1.5">
                                                <button type="button"
                                                        @click="qty > 1 ? qty-- : null"
                                                        class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-medium text-sm hover:bg-slate-200 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                <span class="w-6 text-center text-sm font-semibold tabular-nums" x-text="qty"></span>
                                                <button type="button"
                                                        @click="qty++"
                                                        class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center font-medium text-sm hover:bg-slate-800 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 px-3 py-2.5 bg-slate-50/50">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-500 font-medium">{{ __('Popular') }}</span>
                                    <button type="button"
                                            @click="noteOpen = !noteOpen"
                                            class="flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span x-text="noteOpen ? @js(__('Hide note')) : @js(__('Add note'))"></span>
                                    </button>
                                    <div class="flex-1"></div>
                                    @if($topItem->is_available ?? true)
                                        <button type="submit"
                                                :disabled="adding"
                                                class="px-4 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-full transition-all duration-200 shadow-sm hover:shadow disabled:opacity-50 flex items-center gap-1.5">
                                            <svg x-show="!adding" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <svg x-show="adding" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            <span x-text="adding ? @js(__('Adding...')) : @js(__('Add'))"></span>
                                        </button>
                                    @else
                                        <span class="px-4 py-1.5 bg-slate-200 text-slate-500 text-xs font-semibold rounded-full">{{ __('Unavailable') }}</span>
                                    @endif
                                </div>
                                <div x-cloak x-show="noteOpen"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     class="mt-2">
                                    <input type="text"
                                           x-model="note"
                                           placeholder="{{ __('Special instructions (e.g. No onions, extra spicy)') }}"
                                           class="w-full text-sm border-slate-200 rounded-lg focus:border-amber-500 focus:ring-amber-500 bg-white placeholder:text-slate-400">
                                </div>
                            </div>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    <div x-data="{
        activeCategory: '{{ $categories->first()->id ?? '' }}',
        categoryIds: @js($categories->pluck('id')->values()),
        showNoteFor: null,
        init() {
            const saved = localStorage.getItem('customer_active_category');
            if (saved && this.categoryIds.includes(saved)) {
                this.activeCategory = saved;
            }
        },
        setActiveCategory(id) {
            this.activeCategory = id;
            localStorage.setItem('customer_active_category', id);
        }
    }">
        <!-- Category Tabs -->
        <div class="flex overflow-x-auto gap-2 pb-4 mb-2 no-scrollbar -mx-4 px-4">
            @foreach($categories as $category)
                <button
                    @click="setActiveCategory('{{ $category->id }}')"
                    :class="activeCategory == '{{ $category->id }}'
                        ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20'
                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="whitespace-nowrap px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-200">
                    {{ $category->display_name }}
                </button>
            @endforeach
        </div>

        <!-- Menu Items -->
        <div class="space-y-6 mt-4">
            @foreach($categories as $category)
                <div x-cloak x-show="activeCategory == '{{ $category->id }}'"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="space-y-4">
                    <div class="flex items-center gap-3 mb-4">
                        <h2 class="text-xl font-bold text-slate-900">{{ $category->display_name }}</h2>
                        <span class="text-xs bg-slate-100 text-slate-500 px-2 py-1 rounded-full">
                            {{ $category->menuItems->count() }} {{ trans_choice('ui.customer.items', $category->menuItems->count()) }}
                        </span>
                    </div>

                    @foreach($category->menuItems as $item)
                        <form x-data="{ 
                                qty: 1, 
                                adding: false,
                                note: '',
                                async addToCart() {
                                    if (this.adding) return;
                                    this.adding = true;
                                    try {
                                        const response = await fetch('{{ route('customer.cart.add', $table->code) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                menu_item_id: {{ $item->id }},
                                                qty: this.qty,
                                                note: this.note
                                            })
                                        });
                                        let data = {};
                                        try {
                                            data = await response.json();
                                        } catch (error) {
                                            data = {};
                                        }
                                        if (response.ok && data.success) {
                                            if (typeof data.cart_count === 'number') {
                                                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                                            }
                                        if (data.message) {
                                            const itemName = data.item_name ? `: ${data.item_name}` : '';
                                            const message = `${data.message}${itemName}`;
                                            window.dispatchEvent(new CustomEvent('toast', { detail: { message } }));
                                        }
                                        this.adding = false;
                                    } else {
                                        this.adding = false;
                                        alert(data.message || @js(__('Failed to add item. Please try again.')));
                                    }
                                } catch (error) {
                                    this.adding = false;
                                    alert(@js(__('Network error. Please check your connection.')));
                                }
                            }
                        }" @submit.prevent="addToCart()">
                            <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                            <input type="hidden" name="qty" :value="qty">

                            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
                                <div class="flex">
                                    @if($item->image_path)
                                        <div class="relative w-28 h-28 shrink-0">
                                            <img src="{{ asset('storage/' . $item->image_path) }}"
                                                 alt="{{ $item->display_name }}"
                                                 class="w-full h-full object-cover">
                                            @if(!$item->is_available)
                                                <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center">
                                                    <span class="text-white text-xs font-bold">{{ __('Sold Out') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="w-28 h-28 shrink-0 bg-gradient-to-br from-slate-100 to-slate-50 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="flex-1 p-3 flex flex-col justify-between min-w-0">
                                        <div>
                                            <h3 class="font-semibold text-slate-900 text-sm leading-tight">{{ $item->display_name }}</h3>
                                            @if($item->display_description)
                                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $item->display_description }}</p>
                                            @endif
                                        </div>
                                        <div class="flex justify-between items-end mt-2">
                                            <span class="font-bold text-slate-900">{{ config('pos.currency_symbol') }}{{ number_format($item->price, 2) }}</span>

                                            @if($item->is_available ?? true)
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button"
                                                            @click="qty > 1 ? qty-- : null"
                                                            class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-medium text-sm hover:bg-slate-200 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                        </svg>
                                                    </button>
                                                    <span class="w-6 text-center text-sm font-semibold tabular-nums" x-text="qty"></span>
                                                    <button type="button"
                                                            @click="qty++"
                                                            class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center font-medium text-sm hover:bg-slate-800 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Expandable Note & Add Section -->
                                <div class="border-t border-slate-100 px-3 py-2.5 bg-slate-50/50">
                                    <div class="flex items-center gap-2">
                                                <button type="button"
                                                        @click="showNoteFor = showNoteFor === {{ $item->id }} ? null : {{ $item->id }}"
                                                        class="flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                            </svg>
                                            <span x-text="showNoteFor === {{ $item->id }} ? @js(__('Hide note')) : @js(__('Add note'))"></span>
                                        </button>
                                        <div class="flex-1"></div>
                                        @if($item->is_available ?? true)
                                            <button type="submit"
                                                    :disabled="adding"
                                                    class="px-4 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-full transition-all duration-200 shadow-sm hover:shadow disabled:opacity-50 flex items-center gap-1.5">
                                                <svg x-show="!adding" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                    <svg x-show="adding" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                    </svg>
                                                    <span x-text="adding ? @js(__('Adding...')) : @js(__('Add'))"></span>
                                                </button>
                                            @else
                                                <span class="px-4 py-1.5 bg-slate-200 text-slate-500 text-xs font-semibold rounded-full">{{ __('Unavailable') }}</span>
                                            @endif
                                        </div>
                                    <div x-cloak x-show="showNoteFor === {{ $item->id }}"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         class="mt-2">
                                        <input type="text"
                                               x-model="note"
                                               placeholder="{{ __('Special instructions (e.g. No onions, extra spicy)') }}"
                                               class="w-full text-sm border-slate-200 rounded-lg focus:border-amber-500 focus:ring-amber-500 bg-white placeholder:text-slate-400">
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endforeach

                    @if($category->menuItems->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <p class="text-slate-500 text-sm">{{ __('No items in this category') }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
