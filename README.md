# 🌍 Seismic Monitoring System

A real-time seismic data acquisition and visualization platform built with **Laravel 12**, **Livewire 3**, and **MQTT** — designed to monitor ground motion from geophone sensors and GPS units with live WebSocket broadcasting.

---

## ✨ Features

- **Real-Time Seismic Waveform Display** — Live seismogram visualization streamed directly from geophone sensors via MQTT
- **Ground Motion Parameters** — Automatic calculation of Peak Ground Acceleration (PGA), Peak Ground Velocity (PGV), and Peak Ground Displacement (PGD) from raw 24-bit ADC counts
- **GPS Location Tracking** — Live sensor position mapping with Livewire-powered interactive map
- **Latency Monitoring** — Measures and broadcasts end-to-end data pipeline latency in real time
- **MiniSEED File Download** — Export raw seismic data in standard MiniSEED format for further analysis
- **PPSD Quality Reports** — Probabilistic Power Spectral Density views for signal quality assessment
- **Sensor Status Dashboard** — Live sensor health and connectivity status

---

## 🧱 Tech Stack

| Layer | Technology |
|---|---|
| Backend Framework | Laravel 12 (PHP 8.2+) |
| Frontend Reactivity | Livewire 3 |
| Real-Time Transport | Laravel Reverb (WebSocket) |
| IoT Messaging | MQTT over TLS (HiveMQ Cloud) |
| Database | MySQL |
| Build Tool | Vite + Node.js |
| Testing | Pest PHP |

---

## ⚙️ How It Works

```
Geophone Sensor / GPS Unit
        │
        ▼ (MQTT over TLS)
  HiveMQ Cloud Broker
        │
        ▼
  Laravel MQTT Subscriber (Artisan Command)
        │
        ├─► SeismicCalculationService
        │       ADC counts → Voltage → Velocity → Acceleration / Displacement
        │
        ├─► MySQL Database (SeismicReadings, GroundMotions, GpsLocations)
        │
        └─► Laravel Reverb (WebSocket Broadcast)
                │
                ▼
        Livewire Components (Live UI)
```

**Signal Processing Pipeline:**
1. Raw 24-bit ADC counts are received from the geophone
2. Converted to voltage using a 2.5V reference across 16,777,216 resolution steps
3. Converted to velocity (m/s) using geophone sensitivity (28.8 V·s/m)
4. Differentiated numerically to yield acceleration (m/s²)
5. Integrated numerically to yield displacement (m)
6. Peak values (PGA, PGV, PGD) extracted and stored

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL
- An MQTT broker (e.g. HiveMQ Cloud)

### Installation

```bash
# Clone the repository
git clone https://github.com/your-username/seismic-monitoring.git
cd seismic-monitoring

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy and configure environment
cp .env.example .env
php artisan key:generate
```

### Configuration

Edit `.env` with your credentials:

```env
# Database
DB_DATABASE=seismic_monitoring
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# MQTT Broker
MQTT_HOST=your-broker.hivemq.cloud
MQTT_PORT=8883
MQTT_AUTH_USERNAME=your_mqtt_username
MQTT_AUTH_PASSWORD=your_mqtt_password
MQTT_TLS_ENABLED=true
MQTT_TLS_CA_PATH=/path/to/your/ca-certificate.pem

# Laravel Reverb (WebSocket)
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
```

### Run the Application

```bash
# Run database migrations
php artisan migrate

# Start all services concurrently (server + queue + Vite)
npm run dev
```

Or start services individually:

```bash
php artisan serve          # Web server
php artisan reverb:start   # WebSocket server
php artisan queue:listen   # Queue worker
php artisan mqtt:subscribe # MQTT subscriber
npm run build              # Frontend assets
```

---

## 📡 MQTT Topics

| Topic | Description |
|---|---|
| `sensors/geophone` | Raw ADC count arrays from geophone at 50 SPS |
| `sensors/gps` | GPS coordinates (latitude, longitude, timestamp) |

**Expected geophone payload:**
```json
{
  "adc_counts": [123456, 234567, ...],
  "reading_times": "2025-06-14T10:00:00.000Z"
}
```

**Expected GPS payload:**
```json
{
  "latitude": -0.8612,
  "longitude": 134.0619,
  "reading_times": "2025-06-14T10:00:00.000Z"
}
```

---

## 🗄️ Database Schema

| Table | Description |
|---|---|
| `seismic_readings` | Raw ADC count arrays and timestamps |
| `ground_motions` | Calculated PGA, PGV, PGD per reading |
| `gps_locations` | Latitude, longitude, and timestamps |
| `mseed_files` | Uploaded MiniSEED file records |
| `ppsd_files` | PPSD output file records |

---

## 📁 Project Structure

```
app/
├── Console/Commands/     # MqttSubscriber Artisan command
├── Events/               # WebSocket broadcast events
├── Http/Controllers/     # HTTP endpoints
├── Livewire/             # Real-time UI components
├── Models/               # Eloquent models
└── Services/
    ├── MqttService.php               # MQTT subscription & data routing
    └── SeismicCalculationService.php # Signal processing (ADC → PGA/PGV/PGD)

resources/views/
├── components/           # Reusable Blade components
└── livewire/             # Livewire component views

database/migrations/      # Database schema definitions
tests/
├── mqtt_simulator.py     # Hardware-free MQTT test tool
└── Feature/              # Feature tests
```
