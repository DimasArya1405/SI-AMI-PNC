<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ringkasan_kondisi_audit', function (Blueprint $table) {
            $table->uuid('rka_id')->primary();
            $table->uuid('penugasan_id')->unique();
            $table->date('tanggal_rapat')->nullable();
            $table->text('ringkasan_umum')->nullable();
            $table->text('catatan_rapat')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->enum('acc_p4mp', ['0', '1'])->default(null);
            $table->uuid('created_by_user_id')->nullable();
            $table->uuid('finalized_by_user_id')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('acc_p4mp_at')->nullable();
            $table->timestamps();

            $table->foreign('penugasan_id')
                ->references('penugasan_id')
                ->on('penugasan')
                ->cascadeOnDelete();

            $table->foreign('created_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('finalized_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ringkasan_kondisi_audit');
    }
};
