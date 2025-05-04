<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SeismicReading;
use App\Http\Controllers\DataDownloadController;

class SeismicController extends Controller
{
    public function index()
    {
        $title = 'View';
        $dataDownloadController = new DataDownloadController();
        $dataDownloadController->detectSessions();
        return view('data-view', compact('title'));
    }
    
    public function quality()
    {
        $title = 'Quality';
        return view('quality', compact('title'));
    }
}
