<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppsd_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mseed_file_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->binary('content');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE ppsd_files MODIFY content MEDIUMBLOB");
    }

    public function down(): void
    {
        Schema::dropIfExists('ppsd_files');
    }
};
