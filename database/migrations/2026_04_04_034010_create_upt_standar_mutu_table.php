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
        Schema::create('upt_standar_mutu', function (Blueprint $table) {
            $table->uuid('upt_standar_mutu_id')->primary();
            $table->uuid('upt_id');
            $table->uuid('standar_mutu_id');
            $table->uuid('periode_id');

            $table->foreign('periode_id')
                ->references('id')
                ->on('periode')
                ->onDelete('cascade');

            $table->foreign('upt_id')
                ->references('upt_id')
                ->on('upt')
                ->onDelete('cascade');

            $table->foreign('standar_mutu_id')
                ->references('standar_mutu_id')
                ->on('standar_mutu')
                ->onDelete('cascade');

            $table->unique(['upt_id', 'standar_mutu_id', 'periode_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upt_standar_mutu');
    }
};
