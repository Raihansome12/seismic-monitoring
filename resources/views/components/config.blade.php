<div class="flex items-center space-x-4">
    <span class="font-semibold text-gray-900">IA.STMKG.00.SHZ</span>
    <span class="text-gray-500">|</span>
    <span class="text-gray-700">50 sps</span>
    <span class="text-gray-500">|</span>
    <span class="text-gray-900">Real<span class="text-red-600">Time</span></span>

    <div id="streaming" class="flex hidden items-center px-3 py-1/2 border-2 border-green-500 rounded-full">
        <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-blink mr-2"></span>
        <span class="text-green-500">streaming</span>
    </div>

    <div id="offline" class="hidden px-6 py-1/2 border-2 border-red-500 rounded-full">
        <span class="text-red-500">offline</span>
    </div>

    <div id="latency" class="px-3 py-1/2 border-2 border-blue-500 rounded-full">
        <span class="text-blue-500">Latency: <span id="latency-value">0</span> ms</span>
    </div>

    <script src="{{ asset('js/status-sensor.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Subscribe to latency channel
            Echo.channel('latency')
                .listen('.NewLatencyDataReceived', (e) => {
                    const latencyValue = document.getElementById('latency-value');
                    if (latencyValue) {
                        latencyValue.textContent = e.latency;
                    }
                });
        });
    </script>
</div>


