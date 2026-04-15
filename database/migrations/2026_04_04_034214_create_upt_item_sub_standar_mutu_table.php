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
        Schema::create('upt_item_sub_standar_mutu', function (Blueprint $table) {
            $table->uuid('upt_item_sub_standar_id')->primary();
            $table->uuid('upt_id');
            $table->uuid('upt_sub_standar_id');
            $table->uuid('item_sub_standar_master_id')->nullable();
            $table->uuid('parent_upt_item_id')->nullable();
            $table->uuid('periode_id');
            $table->string('nama_item');
            $table->string('tipe_item')->nullable();
            $table->integer('level')->default(1);
            $table->integer('urutan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('periode_id')->references('id')->on('periode')->onDelete('cascade');
            $table->foreign('upt_id')->references('upt_id')->on('upt')->onDelete('cascade');
            $table->foreign('upt_sub_standar_id')->references('upt_sub_standar_id')->on('upt_sub_standar_mutu')->onDelete('cascade');
            $table->foreign('item_sub_standar_master_id')->references('item_sub_standar_id')->on('item_sub_standar')->nullOnDelete();
            $table->foreign('parent_upt_item_id')->references('upt_item_sub_standar_id')->on('upt_item_sub_standar_mutu')->onDelete('cascade');

            $table->unique(['upt_sub_standar_id', 'nama_item'], 'upt_sub_nama_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upt_item_sub_standar_mutu');
    }
};
