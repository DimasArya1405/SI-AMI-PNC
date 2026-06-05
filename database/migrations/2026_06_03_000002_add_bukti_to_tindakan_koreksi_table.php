<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tindakan_koreksi', function (Blueprint $table) {
            $table->string('bukti_nama_file')->nullable()->after('tanggal_selesai');
            $table->string('bukti_file_path')->nullable()->after('bukti_nama_file');
            $table->uuid('bukti_uploaded_by_user_id')->nullable()->after('bukti_file_path');
            $table->timestamp('bukti_uploaded_at')->nullable()->after('bukti_uploaded_by_user_id');

            $table->foreign('bukti_uploaded_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tindakan_koreksi', function (Blueprint $table) {
            $table->dropForeign(['bukti_uploaded_by_user_id']);
            $table->dropColumn([
                'bukti_nama_file',
                'bukti_file_path',
                'bukti_uploaded_by_user_id',
                'bukti_uploaded_at',
            ]);
        });
    }
};
