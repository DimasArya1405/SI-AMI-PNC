<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::create('pengajuan_jadwal_audit', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Foreign UUID
            $table->uuid('penugasan_id');
            $table->uuid('id_pengaju');

            $table->integer('ketua_auditor')->default(0);
            $table->integer('anggota_auditor')->default(0);
            $table->integer('upt')->default(0);

            // Jadwal Audit
            $table->date('tanggal_audit')->nullable();
            $table->time('jam')->nullable();
            $table->string('alasan')->nullable();
            $table->integer('jumlah_setuju')->nullable()->default(1);

            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_jadwal_audit');
    }
};
