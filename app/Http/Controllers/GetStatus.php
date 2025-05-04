<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SeismicReading;

class GetStatus extends Controller
{
    public function getSensorStatus()
    {
        // Ambil data 10 detik terakhir
        $tenSecondsAgo = Carbon::now()->subSeconds(10);

        $isStreaming = SeismicReading::where('reading_times', '>=', $tenSecondsAgo)->exists();

        return response()->json(['isStreaming' => $isStreaming]);
    }
}
