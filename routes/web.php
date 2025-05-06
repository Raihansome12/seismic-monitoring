<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\SeismicController;
use App\Http\Controllers\GetStatusController;
use App\Http\Controllers\MseedFileController;

Route::get('/', [SeismicController::class, 'index'])->name('data-view');
Route::get('/quality', [SeismicController::class, 'quality'])->name('quality');
Route::get('/sensor-status', [GetStatusController::class, 'getSensorStatus']);
Route::get('/time', [TimeController::class, 'getTime']);
Route::get('/download', [MseedFileController::class, 'download'])->name('mseed.download');
