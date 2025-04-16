<?php

namespace App\Events;

use App\Models\SeismicReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewSeismicDataReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reading;

    public function __construct(SeismicReading $reading)
    {
        $this->reading = $reading;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('seismic-data'),
        ];
    }
    
    public function broadcastAs()
    {
        return 'NewSeismicDataReceived';
    }

    public function broadcastWith()
    {   
        return [
            'reading' => [
                'id' => $this->reading->id,
                'adc_counts' => $this->reading->adc_counts,
                'reading_times' => $this->reading->reading_times
            ]
        ];
    }
}
