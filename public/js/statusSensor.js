async function checkSensorStatus() {
    try {
        const response = await fetch('/sensor-status');
        const data = await response.json();

        const isStreaming = data.isStreaming;

        const streamingDiv = document.getElementById('streaming');
        const offlineDiv = document.getElementById('offline');

        if (isStreaming) {
            streamingDiv.classList.remove('hidden');
            offlineDiv.classList.add('hidden');
        } else {
            offlineDiv.classList.remove('hidden');
            streamingDiv.classList.add('hidden');
        }
    } catch (error) {
        console.error('Failed to fetch sensor status:', error);
    }
}

// Jalankan pertama kali
checkSensorStatus();

// Cek status setiap 5 detik
setInterval(checkSensorStatus, 5000);
