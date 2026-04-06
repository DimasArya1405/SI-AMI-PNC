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
        Schema::create('sub_standar_mutu', function (Blueprint $table) {
            $table->uuid('sub_standar_id')->primary();
            $table->uuid('standar_mutu_id');
            $table->string('nama_sub_standar');
            $table->integer('urutan')->nullable();
            $table->foreign('standar_mutu_id')->references('standar_mutu_id')->on('standar_mutu')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_standar');
    }
};
