<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewLatencyDataReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $latency;

    public function __construct($latency)
    {
        $this->latency = $latency;
    }

    public function broadcastOn()
    {
        return new Channel('latency');
    }

    public function broadcastAs()
    {
        return 'NewLatencyDataReceived';
    }

    public function broadcastWith()
    {
        return [
        'latency' => $this->latency
        ];
    }
} 

