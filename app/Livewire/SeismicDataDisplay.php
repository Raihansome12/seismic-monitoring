<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\SeismicReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SeismicDataDisplay extends Component
{
    protected $listeners = [
        'echo:seismic-data,NewSeismicDataReceived' => 'handleNewSeismicData',
        'refresh' => '$refresh'
    ];

    public $initialData = [];
    public $initialReadingTimes = [];
    const MAX_POINTS = 30000; // Sesuaikan dengan konstanta di JavaScript
    const POINTS_PER_BATCH = 25; // Jumlah data point per batch dari sensor

    public function mount()
    {
        // Ambil data dari database dengan pendekatan yang lebih optimal
        $this->loadHistoricalData();
    }

    protected function loadHistoricalData()
    {
        // Hitung berapa banyak batch yang perlu diambil untuk mendekati MAX_POINTS
        $targetBatches = ceil(self::MAX_POINTS / self::POINTS_PER_BATCH);
        
        // Ambil pembacaan terbaru dari database
        $readings = SeismicReading::orderBy('reading_times', 'desc')
            ->take($targetBatches)
            ->get();
            
        Log::info('Total readings in database: ' . $readings->count());
        Log::info('Retrieved ' . $readings->count() . ' readings from database');
        
        // Proses pembacaan untuk menyiapkan data awal
        $totalPoints = 0;
        $processedReadings = [];
        
        foreach ($readings as $reading) {
            // // Log struktur pembacaan untuk debugging
            // Log::debug('Reading structure: ' . json_encode([
            //     'id' => $reading->id,
            //     'reading_times' => $reading->reading_times,
            //     'adc_counts_type' => gettype($reading->adc_counts),
            //     'adc_counts_is_array' => is_array($reading->adc_counts),
            //     'adc_counts_length' => is_array($reading->adc_counts) ? count($reading->adc_counts) : 'N/A'
            // ]));
            
            // Coba parse adc_counts jika berupa string JSON
            $adcData = $reading->adc_counts;
            if (!is_array($adcData) && is_string($adcData)) {
                try {
                    $adcData = json_decode($adcData, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::warning("Reading ID {$reading->id} has invalid JSON in adc_counts: " . json_last_error_msg());
                        continue;
                    }
                } catch (\Exception $e) {
                    Log::warning("Reading ID {$reading->id} has error parsing adc_counts: " . $e->getMessage());
                    continue;
                }
            }
            
            if (is_array($adcData)) {
                $pointsCount = count($adcData);
                $totalPoints += $pointsCount;
                
                $processedReadings[] = [
                    'data' => $adcData,
                    'time' => $reading->reading_times
                ];
                
                // Jika sudah mencapai atau mendekati MAX_POINTS, berhenti
                if ($totalPoints >= self::MAX_POINTS) {
                    break;
                }
            } else {
                Log::warning("Reading ID {$reading->id} has invalid adc_counts format: " . gettype($adcData));
            }
        }
        
        // Balik urutan untuk mempertahankan urutan kronologis
        $processedReadings = array_reverse($processedReadings);
        
        // Gabungkan data dan waktu
        foreach ($processedReadings as $reading) {
            $this->initialData = array_merge($this->initialData, $reading['data']);
            $this->initialReadingTimes[] = $reading['time'];
        }
        
        // Jika data terlalu banyak, potong hingga MAX_POINTS
        if (count($this->initialData) > self::MAX_POINTS) {
            $this->initialData = array_slice($this->initialData, -self::MAX_POINTS);
            // Sesuaikan juga waktu pembacaan
            $this->initialReadingTimes = array_slice($this->initialReadingTimes, -ceil(self::MAX_POINTS / self::POINTS_PER_BATCH));
        }
        
        // Log informasi untuk debugging
        // Log::info('Processed ' . count($this->initialData) . ' data points from ' . count($processedReadings) . ' readings');
        // Log::info('Loaded historical data', [
        //     'total_points' => count($this->initialData),
        //     'readings_count' => count($processedReadings),
        //     'first_time' => $this->initialReadingTimes[0] ?? null,
        //     'last_time' => end($this->initialReadingTimes) ?? null
        // ]);
    }

    public function render()
    {
        return view('livewire.seismic-data-display', [
            'initialData' => $this->initialData,
            'initialReadingTimes' => $this->initialReadingTimes
        ]);
    }

    public function handleNewSeismicData($event)
    {
        // This method is called when new data is received via WebSocket
        // We don't need to do anything here as the JavaScript handles the real-time updates
        // But we can use this to log or perform other actions if needed
        // Log::info('New seismic data received', ['event' => $event]);
    }
}
