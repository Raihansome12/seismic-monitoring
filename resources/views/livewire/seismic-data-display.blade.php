<div class="mt-6 border border-gray-200 rounded-lg p-6 bg-gray-50">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-streaming@latest/dist/chartjs-plugin-streaming.min.js"></script>

    <canvas id="seismicChart" width="100%" height="250"></canvas>

    <script>
        // Define constants first
        const SPS = 50;
        const DURATION = 15; // in seconds
        const REFRESH_RATE = 50;
        const TIME_STEP = 1000 / SPS;
        const DATA_TIMEOUT = 1000;

        // Get initial data from Livewire component
        const initialReadings = @json($readings);
        
        // Process initial readings into chart data format
        const initialData = [];
        let previousTimestamp = null;
        let currentTimestamp = 0;
        
        // Sort readings by timestamp to ensure correct order
        initialReadings.sort((a, b) => a.timestamp - b.timestamp);
        
        initialReadings.forEach(reading => {
            const timestamp = reading.timestamp;
            
            // Check if there's a gap between readings
            if (previousTimestamp !== null) {
                const timeDiff = timestamp - previousTimestamp;
                
                // If gap is significant (more than twice the expected time between batches)
                // Note: Adjust this threshold as needed based on your data frequency
                const expectedBatchTime = TIME_STEP * 20; // Assuming around 20 samples per batch
                if (timeDiff > expectedBatchTime * 2) {
                    // Calculate how many zero points to add to represent the gap
                    const gapPoints = Math.floor(timeDiff / TIME_STEP) - 1;
                    
                    // Add zero points to represent the gap
                    for (let i = 1; i <= gapPoints; i++) {
                        initialData.push({
                            x: previousTimestamp + (i * TIME_STEP),
                            y: 0
                        });
                    }
                    
                    // Update current timestamp to account for the gap
                    currentTimestamp = timestamp;
                }
            }
            
            if (Array.isArray(reading.adc_counts) && reading.adc_counts.length > 0) {
                // Set initial timestamp if this is the first reading
                if (previousTimestamp === null) {
                    currentTimestamp = timestamp;
                }
                
                // Add each data point
                reading.adc_counts.forEach((value, index) => {
                    initialData.push({
                        x: currentTimestamp + (index * TIME_STEP),
                        y: value
                    });
                });
                
                // Update timestamps
                currentTimestamp += reading.adc_counts.length * TIME_STEP;
                previousTimestamp = timestamp;
            }
        });

        let chart = null;
        let lastTimestamp;
        let dataStarted = false;
        let zeroDataInterval;
        let lastDataReceivedTime = null;
        let dataCheckInterval;

        function initChart() {
            const ctx = document.getElementById('seismicChart').getContext('2d');

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: 'Seismic Waveform',
                        data: initialData.length ? initialData : [],
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0,
                        pointRadius: 0,
                        borderWidth: 1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 0,
                    },
                    plugins: {
                        legend: { display: false }
                    },
                    snapGaps: true,
                    scales: {
                        x: {
                            type: 'realtime',
                            realtime: {
                                duration: DURATION * 1000,
                                refresh: REFRESH_RATE,
                                delay: 0,
                                ttl: DURATION * 1000, // time-to-live
                                maxDataLength: DURATION * SPS, // total data
                                pause: false,
                                onRefresh: function () { }
                            },
                            time: {
                                unit: 'second',
                                displayFormats: {
                                    second: 'HH:mm:ss'
                                }
                            },
                            title: {
                                display: true,
                                text: 'Time (HH:mm:ss)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'ADC Counts'
                            }
                        }
                    }
                },
            });

            if (initialData.length) {
                const lastPoint = initialData[initialData.length - 1];
                lastTimestamp = lastPoint.x;
                lastDataReceivedTime = lastPoint.x;
                dataStarted = true;
            } else {
                const now = Date.now();
                lastTimestamp = now;
                lastDataReceivedTime = now;
            }

            setupWebSocket();
            setupDataMonitoring();
        }

        function setupWebSocket() {
            zeroDataInterval = setInterval(() => {
                if (!dataStarted) {
                    const now = Date.now();
                    chart.data.datasets[0].data.push({ x: now, y: 0 });
                    lastTimestamp = now;
                }
            }, 200);

            window.Echo.channel('seismic-data')
                .listen('.NewSeismicDataReceived', (e) => {
                    if (e.reading && e.reading.adc_counts) {
                        try {
                            const newDataChunk = JSON.parse(e.reading.adc_counts);

                            if (Array.isArray(newDataChunk) && newDataChunk.length > 0) {
                                if (!dataStarted) {
                                    dataStarted = true;
                                    clearInterval(zeroDataInterval);
                                    console.log('Data streaming dimulai');
                                }

                                lastDataReceivedTime = Date.now();

                                for (let i = 0; i < newDataChunk.length; i++) {
                                    const timestamp = lastTimestamp + (i * TIME_STEP);
                                    chart.data.datasets[0].data.push({
                                        x: timestamp,
                                        y: newDataChunk[i]
                                    });
                                }

                                lastTimestamp += newDataChunk.length * TIME_STEP;

                                chart.update('none');
                            }
                        } catch (err) {
                            console.error("Error parsing adc_counts:", err);
                        }
                    }
                });
        }

        function setupDataMonitoring() {
            dataCheckInterval = setInterval(() => {
                const now = Date.now();

                if (dataStarted && (now - lastDataReceivedTime > DATA_TIMEOUT)) {
                    const timeGap = now - lastDataReceivedTime;
                    const pointsToAdd = Math.floor(timeGap / TIME_STEP);

                    if (pointsToAdd > 0) {
                        for (let i = 0; i < pointsToAdd; i++) {
                            const timestamp = lastTimestamp + (i * TIME_STEP);
                            chart.data.datasets[0].data.push({
                                x: timestamp,
                                y: 0
                            });
                        }

                        lastTimestamp += pointsToAdd * TIME_STEP;
                        lastDataReceivedTime = now;
                    }
                }
            }, DATA_TIMEOUT / 2);
        }

        document.addEventListener('DOMContentLoaded', function () {
            initChart();

            window.addEventListener('beforeunload', function () {
                if (zeroDataInterval) clearInterval(zeroDataInterval);
                if (dataCheckInterval) clearInterval(dataCheckInterval);
            });
        });
    </script>
</div>