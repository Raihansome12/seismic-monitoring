<x-layout>
    <x-slot:title>{{ $title }}</x-slot>
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-xl font-semibold mb-8 pb-4 border-b border-gray-300">Probabilistic Power Spectral Density</h2>
        
        @if($ppsdFile)
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1 shadow">
                    <img src="data:image/png;base64,{{ base64_encode($ppsdFile->content) }}" 
                         alt="PPSD Graph" 
                         class="w-full h-auto rounded-lg shadow">
                </div>
                
                <div class="flex-1">
                    <div class="bg-gray-50 px-6 py-4 rounded-lg shadow">
                            <table class="w-full">  
                                <tbody>
                                    <tr>
                                        <td class="py-4 px-3">Station Name</td>
                                        <td class="py-4 px-3">:</td>
                                        <td class="py-4 text-left px-3">IA.STMKG.00.SHZ</td>
                                    </tr>
                                    <tr class="border-t-2 border-blue-500">
                                        <td class="py-4 px-3">Start Time Record</td>
                                        <td class="py-4 px-3">:</td>
                                        <td class="py-4 text-left px-3">{{ $ppsdFile->start_time->format('Y-m-d H:i:s') }} UTC+7</td>
                                    </tr>
                                    <tr class="border-t-2 border-green-500">
                                        <td class="py-4 px-3">End Time Record</td>
                                        <td class="py-4 px-3">:</td>
                                        <td class="py-4 text-left px-3">{{ $ppsdFile->end_time->format('Y-m-d H:i:s') }} UTC+7</td>
                                    </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                Belum ada data PPSD yang tersedia
            </div>
        @endif
    </div>
</x-layout>