<div class="mt-6 border border-gray-200 rounded-lg p-6 bg-gray-50">    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-streaming@latest/dist/chartjs-plugin-streaming.min.js"></script>
    
    <canvas id="seismicChart" width="100%" height="250"></canvas>

    <script>
        const SPS = 50; // Sample per second
        const DURATION = 60; // Duration in seconds to show on chart
        const REFRESH_RATE = 50; // Chart refresh rate in ms
        const TIME_STEP = 20; // Time step per data point in ms (1000/SPS)
        const DATA_TIMEOUT = 1000; // Timeout dalam ms untuk mendeteksi data berhenti masuk

        let chart = null;
        let lastTimestamp;
        let dataStarted = false; // Flag untuk menandai apakah data sudah mulai masuk
        let zeroDataInterval; // Interval untuk menambahkan data nol
        let lastDataReceivedTime = null; // Waktu terakhir data diterima
        let dataCheckInterval; // Interval untuk memeriksa apakah data masih masuk
        
        function initChart() {
            const ctx = document.getElementById('seismicChart').getContext('2d');

            // Inisialisasi data awal dengan y=0
            const now = Date.now();
            lastTimestamp = now; // Set lastTimestamp ke waktu sekarang
            lastDataReceivedTime = now; // Inisialisasi waktu terakhir data diterima
            const initialData = [];
            
            // Buat titik data awal untuk mengisi chart
            for (let i = DURATION; i > 0; i--) {
                initialData.push({
                    x: now - i * 1000, // Titik data dari DURATION detik lalu sampai sekarang
                    y: 0               // Semua nilai awal diatur ke 0
                });
            }
            
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: 'Seismic Waveform',
                        data: initialData,  // Gunakan data awal yang sudah dibuat
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
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            type: 'realtime',
                            realtime: {
                                duration: DURATION * 1000,
                                refresh: REFRESH_RATE,
                                delay: 0,
                                onRefresh: function(chart) {
                                    // This space is left intentionally empty
                                    // Data is added via the WebSocket instead
                                }
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
                }
            });

            console.log('Streaming chart initialized');
            setupWebSocket();
            setupDataMonitoring();
        }

        function setupDataMonitoring() {
            // Set interval untuk memeriksa apakah data masih masuk
            dataCheckInterval = setInterval(() => {
                const now = Date.now();
                
                // Jika data pernah diterima, dan sudah lewat DATA_TIMEOUT tanpa data baru
                if (dataStarted && (now - lastDataReceivedTime > DATA_TIMEOUT)) {
                    // Hitung berapa banyak titik data 0 yang perlu ditambahkan
                    const timeGap = now - lastDataReceivedTime;
                    const pointsToAdd = Math.floor(timeGap / TIME_STEP);
                    
                    if (pointsToAdd > 0) {
                        console.log(`Data tidak masuk selama ${timeGap}ms, menambahkan ${pointsToAdd} titik data 0`);
                        
                        // Tambahkan titik data 0 untuk mengisi celah
                        for (let i = 0; i < pointsToAdd; i++) {
                            const timestamp = lastTimestamp + (i * TIME_STEP);
                            chart.data.datasets[0].data.push({
                                x: timestamp,
                                y: 0
                            });
                        }
                        
                        // Perbarui lastTimestamp
                        lastTimestamp += pointsToAdd * TIME_STEP;
                    }
                    
                    // Perbarui lastDataReceivedTime
                    lastDataReceivedTime = now;
                }
            }, DATA_TIMEOUT / 2); // Periksa 2 kali lebih cepat dari timeout
        }
        
        function setupWebSocket() {
            // Tambahkan interval untuk terus menambahkan data nol sampai data nyata tiba
            zeroDataInterval = setInterval(() => {
                if (!dataStarted) {
                    const now = Date.now();
                    // Tambahkan titik data nol baru
                    chart.data.datasets[0].data.push({
                        x: now,
                        y: 0
                    });
                    lastTimestamp = now;
                }
            }, 200); // Update setiap 200ms
            
            window.Echo.channel('seismic-data')
                .listen('.NewSeismicDataReceived', (e) => {
                    if (e.reading && e.reading.adc_counts) {
                        try {
                            const newDataChunk = JSON.parse(e.reading.adc_counts);

                            if (Array.isArray(newDataChunk) && newDataChunk.length > 0) {
                                // Jika ini adalah batch data pertama yang masuk
                                if (!dataStarted) {
                                    dataStarted = true;
                                    clearInterval(zeroDataInterval); // Hentikan penambahan data nol
                                    console.log('Data streaming dimulai');
                                }
                                
                                // Update waktu terakhir data diterima
                                lastDataReceivedTime = Date.now();
                                
                                // Add each data point with its own timestamp
                                for (let i = 0; i < newDataChunk.length; i++) {
                                    const timestamp = lastTimestamp + (i * TIME_STEP);
                                    
                                    chart.data.datasets[0].data.push({
                                        x: timestamp,
                                        y: newDataChunk[i]
                                    });
                                }
                                
                                // Update lastTimestamp for the next chunk
                                lastTimestamp += newDataChunk.length * TIME_STEP;
                                
                                // No need to call chart.update() - the plugin handles it
                            }
                        } catch (err) {
                            console.error("Error parsing adc_counts:", err);
                        }
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initChart();
            
            // Tambahkan penanganan untuk membersihkan interval jika halaman ditutup
            window.addEventListener('beforeunload', function() {
                if (zeroDataInterval) {
                    clearInterval(zeroDataInterval);
                }
                if (dataCheckInterval) {
                    clearInterval(dataCheckInterval);
                }
            });
        });
    </script>
</div>