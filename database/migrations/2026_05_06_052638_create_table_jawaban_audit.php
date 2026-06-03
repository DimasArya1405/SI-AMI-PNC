<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('jawaban_audit', function (Blueprint $table) {
        $table->uuid('id')->primary();
        
        // Versi Eksplisit: 
        // 1. Definisikan kolomnya dulu
        // 2. Definisikan foreign key-nya secara manual
        $table->uuid('upt_item_sub_standar_id');
        
        $table->foreign('upt_item_sub_standar_id')
              ->references('upt_item_sub_standar_id') // SESUAIKAN: Nama PK di tabel referensi
              ->on('upt_item_sub_standar_mutu')
              ->onDelete('cascade');
        
        $table->string('kategori_temuan')->nullable();
        $table->boolean('jawaban')->default(0);
        $table->text('catatan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_audit');
    }
};
