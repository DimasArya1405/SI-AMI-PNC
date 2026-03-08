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
        Schema::create('auditor', function (Blueprint $table) {
            $table->uuid('auditor_id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('nip')->unique();
            $table->string('nama_lengkap');
            $table->string('jabatan')->nullable();
            $table->uuid('prodi_id')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('email')->unique()->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('prodi_id')
                ->references('prodi_id')
                ->on('prodi')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditor');
    }
};
