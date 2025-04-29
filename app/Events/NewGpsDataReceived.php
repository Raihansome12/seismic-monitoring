<?php

namespace App\Events;

use App\Models\GpsLocation;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewGpsDataReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $location;

    public function __construct(GpsLocation $location)
    {
        $this->location = $location;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('gps-channel'),
        ];
    }
    
    public function broadcastAs()
    {
        return 'NewGpsDataReceived';
    }

    public function broadcastWith()
    {
        Log::info('Broadcasting GPS event', [
            'latitude' => $this->location->latitude,
            'longitude' => $this->location->longitude,
        ]);
        
        return [
        'latitude' => $this->location->latitude,
        'longitude' => $this->location->longitude,
        'reading_times' => $this->location->reading_times
        ];
    }
}