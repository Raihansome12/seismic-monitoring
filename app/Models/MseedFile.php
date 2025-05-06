<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MseedFile extends Model
{
    protected $table = 'mseed_files';

    protected $fillable = [
        'filename',
        'start_time',
        'end_time',
        'content',
    ];

    protected $cast = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}
