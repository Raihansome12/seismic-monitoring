<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GpsLocation extends Model
{

    protected $fillable = [
        'latitude',
        'longitude',
        'reading_times'
    ];

    protected $casts = [
        'reading_times' => 'datetime',
    ];
}
