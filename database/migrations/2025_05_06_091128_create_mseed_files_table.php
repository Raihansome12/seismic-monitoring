<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mseed_files', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->binary('content');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE mseed_files MODIFY content LONGBLOB");
    }

    public function down(): void
    {
        Schema::dropIfExists('mseed_files');
    }
};
