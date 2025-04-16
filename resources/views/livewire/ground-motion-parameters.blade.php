<div class="bg-gray-50 rounded-lg p-8 shadow" wire:poll.1000ms="poll">
    <h3 class="font-bold text-gray-900 text-xl mb-6 pb-4 border-b border-gray-300">
        Ground Motion
    </h3>
    <div class="flex justify-center">
        <table class="w-full">
            <tr>
                <td class="py-2">Acceleration</td>
                <td class="py-2 text-right">
                    <div>
                        {{ number_format($acceleration * 1000000, 2) }} µm/s²
                    </div>
                </td>
            </tr>
            <tr class="border-t-2 border-blue-500">
                <td class="py-2">Velocity</td>
                <td class="py-2 text-right">
                    <div>
                        {{ number_format($velocity * 1000000, 2) }} µm/s
                    </div>
                </td>
            </tr>
            <tr class="border-t-2 border-green-500">
                <td class="py-2">Displacement</td>
                <td class="py-2 text-right">
                    <div>
                        {{ number_format($displacement * 1000000, 2) }} µm
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="mt-4 text-sm text-gray-500">
        Last update: {{ $debugInfo }}
    </div>
</div>
