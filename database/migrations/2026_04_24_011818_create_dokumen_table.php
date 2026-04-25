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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->uuid('dokumen_id')->primary();

            $table->uuid('upt_item_sub_standar_id');
            $table->uuid('auditee_id');

            $table->string('nama_file');
            $table->string('file_path');
            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('upt_item_sub_standar_id')
                ->references('upt_item_sub_standar_id')
                ->on('upt_item_sub_standar_mutu')
                ->cascadeOnDelete();

            $table->foreign('auditee_id')
                ->references('auditee_id')
                ->on('auditee')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
