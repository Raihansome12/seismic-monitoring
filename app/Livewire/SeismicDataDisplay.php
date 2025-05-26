<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\SeismicReading;
use Illuminate\Support\Facades\Log;

class SeismicDataDisplay extends Component
{
    protected $listeners = [
        'echo:seismic-data,NewSeismicDataReceived' => 'handleNewSeismicData',
        'refresh' => '$refresh',
    ];

    public $readings = [];
    public $duration;
    
    const DURATION = 600; // in seconds
    
    public function mount()
    {
        // When component loads, get the latest readings
        $this->loadInitialData();
        $this->duration = self::DURATION;
    }
    
    public function loadInitialData()
    {
        // Get readings from the last n seconds (matching chart duration)
        $cutoffTime = Carbon::now()->subSeconds(self::DURATION);
        
        $this->readings = SeismicReading::where('reading_times', '>=', $cutoffTime)
            ->orderBy('reading_times', 'asc')
            ->get()
            ->map(function($reading) {
                return [
                    'timestamp' => $reading->reading_times->timestamp * 1000, // Convert to milliseconds for JS
                    'adc_counts' => json_decode($reading->adc_counts)
                ];
            })
            ->toArray();
    }
    
    public function render()
    {
        return view('livewire.seismic-data-display', [
            'readings' => $this->readings,
        ]);
    }
}  
