<?php

namespace App\Http\Controllers;

use App\Models\MseedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MseedFileController extends Controller
{
    public function download(Request $request)
    {
        $start = $request->query('start_time');
        $end = $request->query('end_time');

        // Ambil data berdasarkan start_time
        $file = MseedFile::where('start_time', $start)
                        ->where('end_time', $end)
                        ->first();

        if (!$file) {
            return abort(404, 'File not found.');
        }

        $filename = $file->filename ?? 'seismic_data.mseed';

        return Response::make($file->content, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
