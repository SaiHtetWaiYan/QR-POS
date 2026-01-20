<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            QR Code for {{ $table->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <div class="mb-8 flex justify-center">
                    {!! $qr !!}
                </div>

                <h3 class="text-2xl font-bold mb-2">{{ $table->name }}</h3>
                <p class="text-gray-500 mb-6 break-all">{{ $url }}</p>

                <div class="flex justify-center gap-4 no-print">
                    <button onclick="window.print()" class="px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-bold">
                        Print QR Code
                    </button>
                    <a href="{{ route('pos.tables.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-bold">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            .no-print, header, nav { display: none !important; }
            body { background: white; }
            .shadow-sm { box-shadow: none; }
        }
    </style>
</x-app-layout>
