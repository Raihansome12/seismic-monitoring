<div class="mt-6 border border-gray-200 rounded-lg p-6 bg-gray-50">    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <canvas id="seismicChart" width="100%" height="250"></canvas>

    <script>
        const MAX_POINTS = 3000;
        const SPS = 50; // Sample per second (25 data points setiap 0,5 detik = 50 SPS)
        const POINTS_PER_BATCH = 25; // Jumlah data point per batch dari sensor

        // Initialize with data from the database
        const initialData = @json($initialData);
        const initialReadingTimes = @json($initialReadingTimes);
        
        let chart = null;
        let dataBuffer = [];
        let indexBuffer = [];
        let nextInsertIndex = 0;
        let baseTime = null;
        let firstDataIndex = null;
        let isInitialized = false;
        let lastUpdateTime = null;
        let hasHistoricalData = false;
        let totalDataPoints = 0; // Track total data points received

        function initChart() {
            const ctx = document.getElementById('seismicChart').getContext('2d');

            // Initialize data buffers with historical data if available
            if (initialData && initialData.length > 0) {
                console.log(`Initializing chart with ${initialData.length} historical data points`);
                
                // Validasi data historis
                if (Array.isArray(initialData) && initialData.every(item => typeof item === 'number')) {
                    // Inisialisasi buffer dengan MAX_POINTS
                    dataBuffer = Array(MAX_POINTS).fill(0);
                    indexBuffer = Array.from({ length: MAX_POINTS }, (_, i) => i);
                    
                    // Track total data points
                    totalDataPoints = initialData.length;
                    
                    // Masukkan data historis ke buffer, mulai dari kanan
                    // If data exceeds MAX_POINTS, only take the most recent MAX_POINTS
                    const startIndex = Math.max(0, MAX_POINTS - initialData.length);
                    const dataToUse = initialData.length > MAX_POINTS 
                        ? initialData.slice(initialData.length - MAX_POINTS) 
                        : initialData;
                    
                    for (let i = 0; i < dataToUse.length; i++) {
                        dataBuffer[startIndex + i] = dataToUse[i];
                    }
                    
                    nextInsertIndex = MAX_POINTS;
                    
                    isInitialized = true;
                    hasHistoricalData = true;
                    lastUpdateTime = Date.now();
                    
                    console.log(`Initialized with ${dataToUse.length} points, total data points: ${totalDataPoints}`);
                } else {
                    console.warn('Historical data is not in the expected format, initializing with zeros');
                    initializeWithZeros();
                }
            } else {
                console.log('No historical data available, initializing with zeros');
                initializeWithZeros();
            }

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: 'Seismic Waveform',
                        data: indexBuffer.map((x, i) => ({ x, y: dataBuffer[i] })),
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        pointRadius: 0,
                        borderWidth: 1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 50
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            min: 0,
                            max: MAX_POINTS,
                            title: {
                                display: false
                            },
                            ticks: {
                                display: false
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'ADC Counts'
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            setupWebSocket();
        }

        function initializeWithZeros() {
            dataBuffer = Array(MAX_POINTS).fill(0);
            indexBuffer = Array.from({ length: MAX_POINTS }, (_, i) => i);
            nextInsertIndex = MAX_POINTS;
            lastUpdateTime = Date.now();
            totalDataPoints = 0;
        }

        function setupWebSocket() {
            window.Echo.channel('seismic-data')
                .listen('.NewSeismicDataReceived', (e) => {
                    if (e.reading && e.reading.adc_counts) {
                        try {
                            const newDataChunk = JSON.parse(e.reading.adc_counts);

                            if (Array.isArray(newDataChunk) && newDataChunk.length === POINTS_PER_BATCH) {
                                // Aktifkan setelah data pertama jika belum diinisialisasi
                                if (!isInitialized) {
                                    baseTime = Date.now();
                                    firstDataIndex = 0;
                                    isInitialized = true;
                                    console.log('Chart initialized with real-time data');
                                }

                                // Update total data points
                                totalDataPoints += newDataChunk.length;
                                
                                // Geser buffer ke kiri dan tambahkan data baru
                                dataBuffer = [...dataBuffer.slice(POINTS_PER_BATCH), ...newDataChunk];
                                
                                // Update chart dan catat waktu update
                                updateChart();
                                lastUpdateTime = Date.now();
                                
                                // Log progress periodically
                                if (totalDataPoints % 1000 === 0) {
                                    console.log(`Total data points processed: ${totalDataPoints}`);
                                }
                            } else {
                                console.warn(`Received unexpected data format: ${newDataChunk.length} points (expected ${POINTS_PER_BATCH})`);
                            }
                        } catch (err) {
                            console.error("Error parsing adc_counts:", err);
                        }
                    }
                });
        }

        function updateChart() {
            if (!chart) return;

            chart.data.datasets[0].data = indexBuffer.map((x, i) => ({
                x: x,
                y: dataBuffer[i]
            }));

            chart.update('none');
        }

        document.addEventListener('DOMContentLoaded', function () {
            initChart();
        });
    </script>
</div>