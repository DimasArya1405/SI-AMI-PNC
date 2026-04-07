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
        Schema::create('item_sub_standar', function (Blueprint $table) {
            $table->uuid('item_sub_standar_id')->primary();
            $table->uuid('sub_standar_id');
            $table->uuid('parent_item_id')->nullable();
            $table->text('nama_item');
            $table->string('tipe_item')->nullable();
            $table->integer('level')->default(1);
            $table->integer('urutan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sub_standar_id')->references('sub_standar_id')->on('sub_standar_mutu')->onDelete('cascade');
            $table->foreign('parent_item_id')->references('item_sub_standar_id')->on('item_sub_standar')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sub_standar');
    }
};
