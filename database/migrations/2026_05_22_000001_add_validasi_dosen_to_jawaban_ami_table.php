<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jawaban_ami', function (Blueprint $table) {
            $table->string('sumber')->default('auditee')->after('keterangan');
            $table->uuid('uploaded_by_user_id')->nullable()->after('sumber');
            $table->uuid('dosen_id')->nullable()->after('uploaded_by_user_id');
            $table->string('status_validasi')->default('diterima')->after('dosen_id');
            $table->text('catatan_validasi')->nullable()->after('status_validasi');
            $table->uuid('validated_by_user_id')->nullable()->after('catatan_validasi');
            $table->timestamp('validated_at')->nullable()->after('validated_by_user_id');

            $table->foreign('uploaded_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('dosen_id')
                ->references('dosen_id')
                ->on('dosen')
                ->nullOnDelete();

            $table->foreign('validated_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('jawaban_ami', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by_user_id']);
            $table->dropForeign(['dosen_id']);
            $table->dropForeign(['validated_by_user_id']);
            $table->dropColumn([
                'sumber',
                'uploaded_by_user_id',
                'dosen_id',
                'status_validasi',
                'catatan_validasi',
                'validated_by_user_id',
                'validated_at',
            ]);
        });
    }
};
