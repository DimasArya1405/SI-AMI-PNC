<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ringkasan_kondisi_audit_temuan', function (Blueprint $table) {
            $table->uuid('rka_temuan_id')->primary();
            $table->uuid('rka_id');
            $table->uuid('jawaban_audit_id');
            $table->text('kondisi_final');
            $table->enum('kategori_final', ['KTS', 'OB']);
            $table->text('rekomendasi')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->foreign('rka_id')
                ->references('rka_id')
                ->on('ringkasan_kondisi_audit')
                ->cascadeOnDelete();

            $table->foreign('jawaban_audit_id')
                ->references('id')
                ->on('jawaban_audit')
                ->cascadeOnDelete();

            $table->unique(['rka_id', 'jawaban_audit_id'], 'rka_temuan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ringkasan_kondisi_audit_temuan');
    }
};
