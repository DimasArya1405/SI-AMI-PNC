<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi_tindakan_koreksi', function (Blueprint $table) {
            $table->uuid('verifikasi_tk_id')->primary();
            $table->uuid('penugasan_id')->unique();
            $table->text('catatan_umum')->nullable();
            $table->string('wadir1_nama')->nullable();
            $table->uuid('finalized_by_user_id')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            $table->foreign('penugasan_id')
                ->references('penugasan_id')
                ->on('penugasan')
                ->cascadeOnDelete();

            $table->foreign('finalized_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_tindakan_koreksi');
    }
};
