<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\SeismicReading;
use Illuminate\Support\Facades\Log;

class SeismicDataDisplay extends Component
{
    public $initialData = [];

    protected $listeners = [
        'echo:seismic-data,NewSeismicDataReceived' => 'handleNewSeismicData',
        'refresh' => '$refresh',
    ];

    public function mount()
    {
        $sps = 50;
        $seconds = 60;
        $interval = 1000 / $sps; // ms

        $readings = SeismicReading::where('reading_times', '>=', now()->subSeconds($seconds))
            ->orderBy('reading_times', 'asc')
            ->get();

        $flattened = [];

        foreach ($readings as $reading) {
            $counts = json_decode($reading->adc_counts);
            $startTime = strtotime($reading->reading_times) * 1000; // ke milidetik

            foreach ($counts as $i => $count) {
                $flattened[] = [
                    'x' => $startTime + ($i * $interval),
                    'y' => $count
                ];
            }
        }

        $this->initialData = $flattened;
    }

    public function render()
    {
        return view('livewire.seismic-data-display');
    }
}
