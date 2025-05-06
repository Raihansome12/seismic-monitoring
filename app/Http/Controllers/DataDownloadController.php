<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SeismicReading;
use Illuminate\Support\Facades\Log;

class DataDownloadController extends Controller
{
    public function detectSessions()
    {
        $data = SeismicReading::orderBy('reading_times')->get();

        $sessions = [];
        $currentSession = [];
        $lastTime = null;
        $sessionIndex = 1;
        $maxIntervalSeconds = 1;

        foreach ($data as $row) {
            $currentTime = Carbon::parse($row->reading_times);

            if ($lastTime) {        
                // Use absolute value for time difference
                $diff = abs($currentTime->floatDiffInSeconds($lastTime));
                
                // If gap is more than maxIntervalSeconds, finalize current session
                if ($diff > $maxIntervalSeconds) {
                    if (!empty($currentSession)) {
                        $start = Carbon::parse($currentSession[0]->reading_times);
                        $end = Carbon::parse(end($currentSession)->reading_times);
                        $duration = $start->diffInSeconds($end);

                        if($duration >= 60) {
                            $sessions[] = [
                                'session_name' => 'SESI ' . $this->convertToRoman($sessionIndex),
                                'start_time' => $start->format('Y-m-d H:i:s'),
                                'end_time' => $end->format('Y-m-d H:i:s'),
                            ];
                            $sessionIndex++;
                        }
                    }
                    // Start new session with current row
                    $currentSession = [$row];
                    $lastTime = $currentTime;
                    // continue;
                }
            }

            $currentSession[] = $row;
            $lastTime = $currentTime;
        }

        // Handle the last session
        if (!empty($currentSession)) {
            $start = Carbon::parse($currentSession[0]->reading_times);
            $end = Carbon::parse(end($currentSession)->reading_times);
            $duration = $start->diffInSeconds($end);

            if ($duration >= 60) {
                $sessions[] = [
                    'session_name' => 'SESI ' . $this->convertToRoman($sessionIndex),
                    'start_time' => $start->format('Y-m-d H:i:s'),
                    'end_time' => $end->format('Y-m-d H:i:s'),
                ];
            }
        }

        // session()->forget('sessions');
        session(['sessions' => $sessions]);
        return $sessions;
    }

    // Fungsi bantu untuk angka Romawi
    private function convertToRoman($number)
    {
        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4,
            'I' => 1,
        ];
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}
