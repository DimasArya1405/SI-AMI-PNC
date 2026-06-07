<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindakan_koreksi_dosen', function (Blueprint $table) {
            $table->uuid('tindakan_koreksi_dosen_id')->primary();
            $table->uuid('tindakan_koreksi_id')->unique();
            $table->uuid('penugasan_id');
            $table->uuid('assigned_by_user_id')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->foreign('tindakan_koreksi_id')
                ->references('tindakan_koreksi_id')
                ->on('tindakan_koreksi')
                ->cascadeOnDelete();

            $table->foreign('penugasan_id')
                ->references('penugasan_id')
                ->on('penugasan')
                ->cascadeOnDelete();

            $table->foreign('assigned_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindakan_koreksi_dosen');
    }
};
