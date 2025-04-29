<div class="mt-6 border border-gray-200 rounded-lg p-6 bg-gray-50">    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <canvas id="seismicChart" width="100%" height="250"></canvas>

    <script>
        const MAX_POINTS = 30000;
        const SPS = 50; // Sample per second
        const TICK_INTERVAL_SECONDS = 10;

        let chart = null;
        let dataBuffer = Array(MAX_POINTS).fill(0);
        let indexBuffer = Array.from({ length: MAX_POINTS }, (_, i) => i);
        let nextInsertIndex = MAX_POINTS;
        let showTicks = false;
        let baseTime = null;
        let firstDataIndex = null;

        // Initialize with historical data from PHP
        const initialData = @json($initialData);
        const initialReadingTimes = @json($initialReadingTimes);
        
        function initializeWithHistoricalData() {
            if (initialData && initialData.length > 0) {
                // Set the base time from the last reading time
                if (initialReadingTimes && initialReadingTimes.length > 0) {
                    const lastReadingTime = new Date(initialReadingTimes[initialReadingTimes.length - 1]);
                    baseTime = lastReadingTime.getTime();
                    
                    // Calculate how many seconds of data we have
                    const secondsOfData = initialData.length / SPS;
                    
                    // Set the first data index to maintain proper time alignment
                    firstDataIndex = MAX_POINTS - initialData.length;
                    
                    // Initialize the data buffers with zeros first
                    dataBuffer = Array(MAX_POINTS).fill(0);
                    indexBuffer = Array.from({ length: MAX_POINTS }, (_, i) => i);
                    
                    // Place the historical data at the end of the buffer
                    for (let i = 0; i < initialData.length; i++) {
                        dataBuffer[MAX_POINTS - initialData.length + i] = initialData[i];
                    }
                    
                    // Set the next insert index to continue from where we left off
                    nextInsertIndex = MAX_POINTS;
                    
                    // Enable ticks since we have data
                    showTicks = true;
                    
                    console.log(`Initialized with ${initialData.length} historical data points at the end of buffer`);
                    console.log(`First data index: ${firstDataIndex}, Base time: ${new Date(baseTime).toISOString()}`);
                }
            } else {
                // If no historical data, initialize with zeros
                dataBuffer = Array(MAX_POINTS).fill(0);
                indexBuffer = Array.from({ length: MAX_POINTS }, (_, i) => i);
                nextInsertIndex = MAX_POINTS;
                console.log('No historical data available, initialized with zeros');
            }
        }

        function initChart() {
            const ctx = document.getElementById('seismicChart').getContext('2d');

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
                                display: true,
                                text: 'Time (HH:mm:ss)'
                            },
                            ticks: {
                                display: true, // Always display ticks
                                callback: function (value) {
                                    // Only show time labels if we have valid time data
                                    if (baseTime === null || firstDataIndex === null) return '';

                                    const secondsFromStart = (value - firstDataIndex) / SPS;

                                    if (secondsFromStart < 0 || secondsFromStart % TICK_INTERVAL_SECONDS !== 0) return '';

                                    const timestamp = baseTime + secondsFromStart * 1000;
                                    const date = new Date(timestamp);
                                    const h = String(date.getHours()).padStart(2, '0');
                                    const m = String(date.getMinutes()).padStart(2, '0');
                                    const s = String(date.getSeconds()).padStart(2, '0');

                                    return `${h}:${m}:${s}`;
                                }
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

            console.log('Chart initialized');
            setupWebSocket();
        }

        function setupWebSocket() {
            window.Echo.channel('seismic-data')
                .listen('.NewSeismicDataReceived', (e) => {
                    if (e.reading && e.reading.adc_counts) {
                        try {
                            const newDataChunk = JSON.parse(e.reading.adc_counts);

                            if (Array.isArray(newDataChunk)) {
                                // Aktifkan tick setelah data pertama
                                if (!showTicks) {
                                    showTicks = true;
                                    baseTime = Date.now();
                                    firstDataIndex = nextInsertIndex;
                                }

                                for (let i = 0; i < newDataChunk.length; i++) {
                                    dataBuffer.push(newDataChunk[i]);
                                    indexBuffer.push(nextInsertIndex++);
                                }

                                // Potong agar tetap MAX_POINTS
                                if (dataBuffer.length > MAX_POINTS) {
                                    dataBuffer = dataBuffer.slice(-MAX_POINTS);
                                    indexBuffer = indexBuffer.slice(-MAX_POINTS);
                                }

                                updateChart();
                            }
                        } catch (err) {
                            console.error("Error parsing adc_counts:", err);
                        }
                    }
                });
        }

        function updateChart() {
            if (!chart) return;

            chart.data.datasets[0].data = dataBuffer.map((y, i) => ({
                x: indexBuffer[i],
                y: y
            }));

            chart.options.scales.x.min = indexBuffer[0];
            chart.options.scales.x.max = indexBuffer[indexBuffer.length - 1];

            chart.update('none');
        }

        document.addEventListener('DOMContentLoaded', function () {
            initializeWithHistoricalData();
            initChart();
        });
    </script>
</div>