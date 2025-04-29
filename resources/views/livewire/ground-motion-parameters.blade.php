<div class="bg-gray-50 rounded-lg p-8 shadow" wire:poll.1000ms="poll" x-data="{ showLegend: false }">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-300">
        <h3 class="font-bold text-gray-900 text-xl">
            Ground Motion
        </h3>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="text-blue-600 hover:text-blue-800 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>

            <!-- Tooltip -->
            <div x-show="open" 
                
                @click.outside="open = false"
                class="absolute bottom-full right-0 mb-4 w-auto max-w-[90vw] p-4 bg-white border border-gray-300 rounded-lg shadow-lg z-50 whitespace-nowrap transform translate-x-[6.2%]">
                
                <table class="min-w-full">  
                    <thead>
                        <tr>
                            <th class="pb-1 text-center font-bold text-gray-900">Parameter</th>
                            <th class="pb-1 text-center font-bold text-gray-900">Description</th>
                            <th class="pb-1 text-center font-bold text-gray-900">Units</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr class="border-t border-gray-300">
                            <td class="py-2 px-3">Acceleration</td>
                            <td class="py-2 px-3">Peak Ground Acceleration, last 5 seconds</td>
                            <td class="py-2 text-left px-3">micrometers/second²</td>
                        </tr>
                        <tr class="border-t border-gray-300">
                            <td class="py-2 px-3">Velocity</td>
                            <td class="py-2 px-3">Peak Ground Velocity, last 5 seconds</td>
                            <td class="py-2 text-left px-3">micrometers/second</td>
                        </tr>
                        <tr class="border-t border-gray-300">
                            <td class="py-2 px-3">Displacement</td>
                            <td class="py-2 px-3">Peak Ground Displacement, last 5 seconds</td>
                            <td class="py-2 text-left px-3">micrometers</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Tooltip arrow -->
                <div class="absolute -bottom-2 right-10 w-4 h-4 transform rotate-45 bg-white border-r border-b border-gray-300"></div>
            </div>
        </div>
    </div>

    <div class="flex justify-center">
        <table class="w-full">
            <tr>
                <td class="py-2">Acceleration</td>
                <td class="py-2 text-right">
                    <div>{{ number_format($acceleration * 1000000, 2) }} µm/s²</div>
                </td>
            </tr>
            <tr class="border-t-2 border-blue-500">
                <td class="py-2">Velocity</td>
                <td class="py-2 text-right">
                    <div>{{ number_format($velocity * 1000000, 2) }} µm/s</div>
                </td>
            </tr>
            <tr class="border-t-2 border-green-500">
                <td class="py-2">Displacement</td>
                <td class="py-2 text-right">
                    <div>{{ number_format($displacement * 1000000, 2) }} µm</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="mt-2 text-sm text-gray-500">
        Last update: {{ $lastUpdate }}
    </div>
</div>
