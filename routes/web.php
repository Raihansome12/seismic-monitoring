<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeismicController;
use App\Http\Controllers\GetStatus;
use App\Http\Controllers\TimeController;

Route::get('/', [SeismicController::class, 'index'])->name('data-view');
Route::get('/quality', [SeismicController::class, 'quality'])->name('quality');
Route::get('/sensor-status', [GetStatus::class, 'getSensorStatus']);
Route::get('/time', [TimeController::class, 'getTime']);
