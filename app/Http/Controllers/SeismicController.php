<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PpsdFile;
use Illuminate\Http\Request;
use App\Models\SeismicReading;
use App\Http\Controllers\DataDownloadController;

class SeismicController extends Controller
{
    public function index()
    {
        $title = 'View';

        $dataDownloadController = new DataDownloadController();
        $sessions = $dataDownloadController->detectSessions();
        
        return view('data-view', compact('title'));
    }
    
    public function quality()
    {
        $title = 'Quality';
        
        // Get the latest PPSD file
        $ppsdFile = PpsdFile::latest()->first();
        
        return view('quality', compact('title', 'ppsdFile'));
    }
}
