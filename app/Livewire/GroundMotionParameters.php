<?php

namespace App\Livewire;

use App\Models\GroundMotion;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GroundMotionParameters extends Component
{
    public $acceleration = 0;
    public $velocity = 0;
    public $displacement = 0;
    public $lastUpdate = null;
    public $debugInfo = '';

    protected $listeners = ['echo:seismic-data,NewSeismicDataReceived' => 'handleNewReading'];

    public function mount()
    {
        $this->updateWithLatestSecond();
    }

    public function handleNewReading($payload)
    {
        $this->updateWithLatestSecond();
    }

    public function poll()
    {
        $this->updateWithLatestSecond();
    }

    private function updateWithLatestSecond()
    {
        try {
            // Get all ground motion readings from the last second
            $oneSecondAgo = Carbon::now()->subSecond();
            $recentReadings = GroundMotion::where('created_at', '>=', $oneSecondAgo)
                ->latest('created_at')
                ->get();

            if ($recentReadings->isNotEmpty()) {
                // Calculate averages for the last second
                $this->acceleration = $recentReadings->avg('acceleration');
                $this->velocity = $recentReadings->avg('velocity');
                $this->displacement = $recentReadings->avg('displacement');
                $this->lastUpdate = $recentReadings->first()->created_at;
                
                Log::info('Ground motion parameters updated with 1-second average', [
                    'readings_count' => $recentReadings->count(),
                    'acceleration' => $this->acceleration,
                    'velocity' => $this->velocity,
                    'displacement' => $this->displacement
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating ground motion parameters: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ground-motion-parameters');
    }
}
