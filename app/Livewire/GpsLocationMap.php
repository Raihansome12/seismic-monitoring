<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GpsLocation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GpsLocationMap extends Component
{     
    public $location;
    public $currentTime;
    public $city = 'Unknown';

    protected $listeners = [
        'echo:gps-channel,.NewGpsDataReceived' => 'handleNewLocation',
        'handleNewLocation' => 'handleNewLocation'
    ];

    public function mount()
    {
        // Load most recent location
        $latestLocation = GpsLocation::latest()->first();
        if ($latestLocation) {
            $this->location = [
                'latitude' => $latestLocation->latitude, 
                'longitude' => $latestLocation->longitude
            ];
            $this->fetchCityName($latestLocation->latitude, $latestLocation->longitude);
        } else {
            $this->location = [
                'latitude' => -6.15075243,
                'longitude' => 105.44465359
            ];
        }

        $this->currentTime = now()->setTimezone('Asia/Jakarta')->format('H:i:s');
    }

    public function handleNewLocation($payload)
    {      
        try {
            $this->location = [
                'latitude' => $payload['latitude'],
                'longitude' => $payload['longitude'],
            ];
            
            Log::info('Updated location:', $this->location);
            
            $this->fetchCityName($payload['latitude'], $payload['longitude']);
            $this->currentTime = now()->setTimezone('Asia/Jakarta')->format('H:i:s');

            $this->dispatch('gps-location-updated', $this->location);
            
        } catch (\Exception $e) {
            Log::error('Error in handleNewLocation: ' . $e->getMessage());
        }
    }

    private function fetchCityName($latitude, $longitude)
    {
        try {
            $apiKey = "76f06bc3e6df41358d58aa3b80c00349"; // Ganti dengan API Key kamu

            $response = Http::get("https://api.opencagedata.com/geocode/v1/json", [
                'q' => "$latitude,$longitude",
                'key' => $apiKey,
                'language' => 'id', // Bahasa Indonesia
                'pretty' => 1
            ]);

            $data = $response->json();

            $components = $data['results'][0]['components'] ?? [];
            
            // Ambil nama kabupaten/kota dan provinsi
            $city = $components['city'] ??
                    $components['county'] ??
                    'Unknown';
            $province = $components['state'] ?? ''; // Provinsi

            // Format hasilnya: "Kabupaten/Kota, Provinsi"
            $this->city = trim("{$city}, {$province}", ", ");

        } catch (\Exception $e) {
            Log::error("Error fetching city: " . $e->getMessage());
            $this->city = 'Unknown';
        }

        
    }

    public function render()
    {
        return view('livewire.gps-location-map', [
            'location' => $this->location,
            'city' => $this->city,
            'currentTime' => $this->currentTime,
        ]);
    }
}
