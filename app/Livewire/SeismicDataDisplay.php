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
        'refresh' => '$refresh'
    ];

    public function render()
    {
        return view('livewire.seismic-data-display');
    }
}
