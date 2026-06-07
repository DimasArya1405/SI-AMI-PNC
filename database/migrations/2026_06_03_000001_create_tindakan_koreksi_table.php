<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindakan_koreksi', function (Blueprint $table) {
            $table->uuid('tindakan_koreksi_id')->primary();
            $table->uuid('penugasan_id');
            $table->uuid('jawaban_audit_id');
            $table->text('akar_penyebab')->nullable();
            $table->text('rencana_koreksi')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->date('target_selesai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'ditolak', 'disetujui', 'selesai'])->default('draft');
            $table->text('catatan_auditor')->nullable();
            $table->uuid('created_by_user_id')->nullable();
            $table->uuid('verified_by_user_id')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('penugasan_id')
                ->references('penugasan_id')
                ->on('penugasan')
                ->cascadeOnDelete();

            $table->foreign('jawaban_audit_id')
                ->references('id')
                ->on('jawaban_audit')
                ->cascadeOnDelete();

            $table->foreign('created_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('verified_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->unique(['penugasan_id', 'jawaban_audit_id'], 'tk_penugasan_jawaban_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindakan_koreksi');
    }
};
