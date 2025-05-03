<x-layout>
    <x-slot:title>{{ $title }}</x-slot>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center border-b border-gray-300 pb-4">
            {{-- Konfigurasi Channel --}}
            <x-config></x-config>

            {{-- Menu Download --}}
            @livewire('data-downloader')
        
        </div>
        {{-- Seismic Trace --}}
        @livewire('seismic-data-display')
    
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Ground Motion Parameters --}}
            @livewire('ground-motion-parameters')

            {{-- Pin Maps/Leaflet --}}
            @livewire('gps-location-map')

        </div>
    </div>
</x-layout>