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
            $SecondsAgo = Carbon::now()->subSeconds(5);
    
            $this->acceleration = GroundMotion::where('created_at', '>=', $SecondsAgo)->max('acceleration');
            $this->velocity = GroundMotion::where('created_at', '>=', $SecondsAgo)->max('velocity');
            $this->displacement = GroundMotion::where('created_at', '>=', $SecondsAgo)->max('displacement');
            
            // Optionally, set last update time
            $lastUpdateTime = GroundMotion::where('created_at', '>=', $SecondsAgo)
                ->latest('created_at')
                ->value('created_at');
            
            // Convert UTC to UTC+7 (Asia/Jakarta)
            if ($lastUpdateTime) {
                $this->lastUpdate = Carbon::parse($lastUpdateTime)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
            } else {
                $this->lastUpdate = null;
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
