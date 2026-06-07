<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tindakan_koreksi', function (Blueprint $table) {
            $table->text('analisis_ketidaksesuaian')->nullable()->after('jawaban_audit_id');
            $table->text('pelaksanaan_deskripsi')->nullable()->after('bukti_uploaded_at');
            $table->text('hasil_penilaian_auditor')->nullable()->after('pelaksanaan_deskripsi');
            $table->date('tanggal_penilaian_ulang')->nullable()->after('hasil_penilaian_auditor');
            $table->enum('p4mp_status', ['menunggu_verifikasi', 'terverifikasi', 'perlu_perbaikan'])->nullable()->after('tanggal_penilaian_ulang');
            $table->text('p4mp_catatan')->nullable()->after('p4mp_status');
            $table->uuid('p4mp_verified_by_user_id')->nullable()->after('p4mp_catatan');
            $table->timestamp('p4mp_verified_at')->nullable()->after('p4mp_verified_by_user_id');
            $table->string('wadir1_nama')->nullable()->after('p4mp_verified_at');

            $table->foreign('p4mp_verified_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tindakan_koreksi', function (Blueprint $table) {
            $table->dropForeign(['p4mp_verified_by_user_id']);
            $table->dropColumn([
                'analisis_ketidaksesuaian',
                'pelaksanaan_deskripsi',
                'hasil_penilaian_auditor',
                'tanggal_penilaian_ulang',
                'p4mp_status',
                'p4mp_catatan',
                'p4mp_verified_by_user_id',
                'p4mp_verified_at',
                'wadir1_nama',
            ]);
        });
    }
};
