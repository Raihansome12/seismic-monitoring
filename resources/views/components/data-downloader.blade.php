<div class="flex items-center space-x-4">
    <button onclick="openModal()" class="flex items-center space-x-2 text-gray-900 border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50">
        <img src="img/calendar.png" alt="Calendar" class="w-4 h-4">
        <span>Download Data</span>
    </button>

    <!-- Modal Backdrop -->
    <div id="modalBackdrop" class="fixed inset-0 hidden" style="z-index: 2000"></div>
    <!-- Modal -->
    <div id="historyModal" class="fixed inset-0 flex items-center justify-center hidden" style="z-index: 2001;">
        <div id="modalContent" class="max-w-7xl w-full mx-4">
            <div class="bg-white rounded-lg shadow border border-gray-300 border-t-2 border-t-blue-500 border-b-2 border-b-green-500">
                <div class="p-2 pr-4 flex justify-end bg-gray-100 border-b border-gray-300">
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="m-10">
                    <div class="p-6 border border-gray-300">
                        <div class="mb-4">
                            <h1 class="text-gray-900 font-bold">Measurement Records</h1>
                            <div class="text-sm text-gray-500">Total Recording: {{ count(session('sessions', [])) }}</div>
                        </div>
                        {{-- Scrollable Table Container --}}
                        <div class="overflow-y-auto max-h-56">
                            <table class="table-auto w-full text-gray-900">  
                                <thead>
                                    <tr class="text-center font-bold text-gray-900 border-t border-gray-300">
                                        <th class="py-3">Session</th>
                                        <th class="py-3">Start Time</th>
                                        <th class="py-3">End Time</th>
                                        <th class="py-3">Download</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    @foreach (session('sessions', []) as $session)
                                        <tr class="text-center bg-gray-100">
                                            <td class="py-3">{{ $session['session_name'] }}</td>
                                            <td class="py-3">{{ $session['start_time'] }}</td>
                                            <td class="py-3">{{ $session['end_time'] }}</td>
                                            <td class="py-3 flex items-center justify-center">
                                                <a href=""><img src="img/download.png" alt="Download" class="w-4 h-4"></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <script>
    function openModal() {
        document.getElementById('modalBackdrop').classList.remove('hidden');
        document.getElementById('historyModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('modalBackdrop').classList.add('hidden');
        document.getElementById('historyModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    document.getElementById('modalBackdrop').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    </script>
</div>