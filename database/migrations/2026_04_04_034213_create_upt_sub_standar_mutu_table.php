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
        Schema::create('upt_sub_standar_mutu', function (Blueprint $table) {
            $table->uuid('upt_sub_standar_id')->primary();
            $table->uuid('upt_id');
            $table->uuid('standar_mutu_id');
            $table->integer('urutan')->nullable();

            // referensi ke master, nullable supaya bisa bikin custom
            $table->uuid('sub_standar_master_id')->nullable();

            // editable
            $table->string('nama_sub_standar');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('upt_id')
                ->references('upt_id')
                ->on('upt')
                ->onDelete('cascade');

            $table->foreign('standar_mutu_id')
                ->references('standar_mutu_id')
                ->on('standar_mutu')
                ->onDelete('cascade');

            $table->foreign('sub_standar_master_id')
                ->references('sub_standar_id')
                ->on('sub_standar_mutu')
                ->nullOnDelete();

            $table->unique(['upt_id', 'standar_mutu_id', 'nama_sub_standar'], 'upt_standar_nama_sub_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upt_sub_standar_mutu');
    }
};
