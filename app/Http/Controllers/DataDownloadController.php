<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SeismicReading;

class DataDownloadController extends Controller
{
    public function detectSessions()
    {
        $data = SeismicReading::orderBy('reading_times')->get();

        $sessions = [];
        $currentSession = [];
        $lastTime = null;
        $sessionIndex = 1;
        $maxIntervalSeconds = 0.6;

        foreach ($data as $row) {
            $currentTime = Carbon::parse($row->reading_times);

            if ($lastTime) {
                $diff = $currentTime->floatDiffInSeconds($lastTime);
                if ($diff > $maxIntervalSeconds) {
                    // Simpan sesi saat ini ke dalam array sessions
                    $sessions[] = [
                        'session_name' => 'SESI ' . $this->convertToRoman($sessionIndex),
                        'start_time' => $currentSession[0]->reading_times->format('y:m:d H:i:s'),
                        'end_time' => end($currentSession)->reading_times->format('y:m:d H:i:s'),
                    ];

                    $sessionIndex++;
                    $currentSession = [];
                }
            }

            $currentSession[] = $row;
            $lastTime = $currentTime;
        }

        // Simpan sesi terakhir
        if (!empty($currentSession)) {
            $sessions[] = [
                'session_name' => 'SESI ' . $this->convertToRoman($sessionIndex),
                'start_time' => $currentSession[0]->reading_times->format('y:m:d H:i:s'),
                'end_time' => end($currentSession)->reading_times->format('y:m:d H:i:s'),
            ];
        }

        // Store sessions in session
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
