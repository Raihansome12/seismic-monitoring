async function updateTime() {
    try {
        let response = await fetch('/time'); // Ambil waktu dari Laravel API
        let data = await response.json();
        document.getElementById('time').textContent = `${data.time} UTC+7`;
    } catch (error) {
        console.error("Gagal mengambil waktu:", error);
    }
}

updateTime(); // Jalankan saat halaman dimuat
setInterval(updateTime, 1000);
