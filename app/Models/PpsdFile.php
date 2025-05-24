<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpsdFile extends Model
{
    protected $table = 'ppsd_files';

    protected $fillable = [
        'mseed_file_id',
        'filename',
        'start_time',
        'end_time',
        'content',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function mseedFile()
    {
        return $this->belongsTo(MseedFile::class, 'mseed_file_id');
    }
}
