<x-layout>
    <x-slot:title>{{ $title }}</x-slot>
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-xl font-semibold mb-8 pb-4 border-b border-gray-300">Probabilistic Power Spectral Density</h2>
        
        @if($ppsdFile)
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1">
                    <img src="data:image/png;base64,{{ base64_encode($ppsdFile->content) }}" 
                         alt="PPSD Graph" 
                         class="w-full h-auto rounded-lg shadow">
                </div>
                
                <div class="flex-1">
                    <div class="bg-gray-50 px-6 py-4 rounded-lg shadow">
                            <table class="w-full table-fixed">  
                                <colgroup>
                                    <col class="w-1/3"/>
                                    <col class="w-[20px]"/>
                                    <col />
                                </colgroup>
                                <tbody class="[&>tr>td]:py-4 [&>tr>td]:px-3">
                                    <tr>
                                        <td>Station Name</td>
                                        <td>:</td>
                                        <td class="text-left">IA.STMKG.00.SHZ</td>
                                    </tr>
                                    <tr class="border-t-2 border-blue-500">
                                        <td>Start Time Record</td>
                                        <td>:</td>
                                        <td class="text-left">{{ $ppsdFile->start_time->format('Y-m-d H:i:s') }} UTC+7</td>
                                    </tr>
                                    <tr class="border-t-2 border-green-500">
                                        <td>End Time Record</td>
                                        <td>:</td>
                                        <td class="text-left">{{ $ppsdFile->end_time->format('Y-m-d H:i:s') }} UTC+7</td>
                                    </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1 flex items-center justify-center bg-gray-50 rounded-lg p-8 shadow text-center py-8 text-gray-500">
                    Belum ada data PPSD yang tersedia
                </div>
                
                <div class="flex-1">
                    <div class="bg-gray-50 px-6 py-4 rounded-lg shadow">
                            <table class="w-full table-fixed">  
                                <colgroup>
                                    <col class="w-1/3"/>
                                    <col class="w-[20px]"/>
                                    <col />
                                </colgroup>
                                <tbody class="[&>tr>td]:py-4 [&>tr>td]:px-3">
                                    <tr>
                                        <td>Station Name</td>
                                        <td>:</td>
                                        <td class="text-left">-</td>
                                    </tr>
                                    <tr class="border-t-2 border-blue-500">
                                        <td>Start Time Record</td>
                                        <td>:</td>
                                        <td class="text-left">-</td>
                                    </tr>
                                    <tr class="border-t-2 border-green-500">
                                        <td>End Time Record</td>
                                        <td>:</td>
                                        <td class="text-left">-</td>
                                    </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layout>