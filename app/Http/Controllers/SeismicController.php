<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SeismicReading;

class SeismicController extends Controller
{
    public function index()
    {
        $title = 'View';
        $isStreaming = $this->checkStreamingStatus();
        return view('data-view', compact('title', 'isStreaming'));
    }
    
    public function quality()
    {
        $title = 'Quality';
        $isStreaming = $this->checkStreamingStatus();
        return view('quality', compact('title', 'isStreaming'));
    }

    private function checkStreamingStatus()
    {
        // Check if there's any data in the last 5 minutes
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);
        $recentData = SeismicReading::where('reading_times', '>=', $fiveMinutesAgo)->exists();
        
        return $recentData;
    }
}
