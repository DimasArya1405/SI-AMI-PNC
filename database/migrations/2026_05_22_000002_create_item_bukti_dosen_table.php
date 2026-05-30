<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_bukti_dosen', function (Blueprint $table) {
            $table->uuid('item_bukti_dosen_id')->primary();
            $table->uuid('penugasan_id');
            $table->uuid('upt_item_sub_standar_id');
            $table->uuid('assigned_by_user_id')->nullable();
            $table->timestamps();

            $table->foreign('penugasan_id')
                ->references('penugasan_id')
                ->on('penugasan')
                ->cascadeOnDelete();

            $table->foreign('upt_item_sub_standar_id')
                ->references('upt_item_sub_standar_id')
                ->on('upt_item_sub_standar_mutu')
                ->cascadeOnDelete();

            $table->foreign('assigned_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->unique(['penugasan_id', 'upt_item_sub_standar_id'], 'item_bukti_dosen_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_bukti_dosen');
    }
};
