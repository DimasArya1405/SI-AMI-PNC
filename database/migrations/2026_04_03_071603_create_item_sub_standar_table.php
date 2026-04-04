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
            $table->string('nama_item');
            $table->foreign('sub_standar_id')->references('sub_standar_id')->on('sub_standar_mutu')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
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
