<?php

namespace App\Services;

use App\Models\SeismicReading;
use PhpMqtt\Client\Facades\MQTT;
use App\Events\NewGpsDataReceived;
use Illuminate\Support\Facades\Log;
use App\Events\NewSeismicDataReceived;
use App\Models\GpsLocation;
use App\Models\GroundMotion;
use App\Services\SeismicCalculationService;

class MqttService
{
    protected $seismicCalculationService;

    public function __construct(SeismicCalculationService $seismicCalculationService)
    {
        $this->seismicCalculationService = $seismicCalculationService;
    }

    public function subscribe()
    {
        try {
            $mqtt = MQTT::connection();

            Log::info('MQTT subscribtion starting...');
            
            // Subscribe to sensor topics
            $mqtt->subscribe('sensors/geophone', function ($topic, $message) {
                $this->processGeophoneData(json_decode($message, true));
            }, 0);
            
            $mqtt->subscribe('sensors/gps', function ($topic, $message) {
                $this->processGpsData(json_decode($message, true));
            }, 0);
            
            $mqtt->loop(true);
        } catch (\Exception $e) {
            Log::error('MQTT Subscription error: ' . $e->getMessage());
        }
    }

    protected function processGeophoneData(array $data)
    {
        try {
            if(!isset($data['adc_counts']) || !is_array($data['adc_counts'])) {
                throw new \Exception("Data ADC Counts tidak valid.");
            }

            $adcCounts = array_map('intval', $data['adc_counts']);

            // Calculate seismic parameters
            $calculations = $this->seismicCalculationService->calculate($adcCounts);
            
            // Store data in database
            $reading = SeismicReading::create([
                'adc_counts' => json_encode($data['adc_counts']),
                'reading_times' => $data['reading_times'],
            ]);
            
            GroundMotion::create([
                'seismic_reading_id' => $reading->id,
                'acceleration' => $calculations['avg_acceleration'],
                'velocity' => $calculations['avg_velocity'],
                'displacement' => $calculations['avg_displacement'],
            ]);
            
            // Broadcast event to WebSocket
            event(new NewSeismicDataReceived($reading));
        } catch (\Exception $e) {
            Log::error('Error processing geophone data: ' . $e->getMessage());
        }
    }

    protected function processGpsData(array $data)
    {
        Log::info('Processing GPS data: ', [$data]);
        try {
            // Validasi data sebelum disimpan
            if (!isset($data['latitude']) || !isset($data['longitude'])) {
                Log::warning('GPS data missing longitude or latitude', $data);
                return;
            }

            $latitude = floatval($data['latitude']);
            $longitude = floatval($data['longitude']);

            // Cek apakah koordinat valid
            if (!is_numeric($latitude) || !is_numeric($longitude)) {
                Log::warning('Invalid GPS data received', ['latitude' => $latitude, 'longitude' => $longitude]);
                return;
            }

            // Store GPS data in database
            $location = GpsLocation::create([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'reading_times' => $data['reading_times'],
            ]);
            
            // Broadcast event to WebSocket
            broadcast(new NewGpsDataReceived($location))->toOthers();
            Log::info('GPS event broadcast completed');
            
        } catch (\Exception $e) {
            Log::error('Error processing GPS data: ' . $e->getMessage());
            Log::error('Stack trace:', ['trace' => $e->getTraceAsString()]);
        }
    }
}