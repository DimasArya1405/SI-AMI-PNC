<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan', function (Blueprint $table) {
            $table->uuid('penugasan_id')->primary();

            // Foreign UUID
            $table->uuid('periode_id');
            $table->uuid('upt_id');
            $table->uuid('auditor_id_1');
            $table->uuid('auditor_id_2');

            // Jadwal Audit
            $table->date('tanggal_audit')->nullable();
            $table->time('jam')->nullable();

            $table->enum('status_penugasan', ['pending','aktif', 'selesai'])
                  ->default('aktif');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan');
    }
};