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
    
    public function mount()
    {
        // When component loads, get the latest readings (last 20 seconds)
        $this->loadInitialData();
    }
    
    public function loadInitialData()
    {
        // Get readings from the last 20 seconds (matching chart duration)
        $cutoffTime = Carbon::now()->subSeconds(30);
        
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
            'readings' => $this->readings
        ]);
    }
}  
