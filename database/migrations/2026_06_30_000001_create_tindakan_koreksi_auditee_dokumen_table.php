<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindakan_koreksi_auditee_dokumen', function (Blueprint $table) {
            $table->uuid('dokumen_tk_auditee_id')->primary();
            $table->uuid('tindakan_koreksi_id');
            $table->uuid('uploaded_by_user_id')->nullable();
            $table->string('nama_file');
            $table->string('file_path');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('tindakan_koreksi_id')
                ->references('tindakan_koreksi_id')
                ->on('tindakan_koreksi')
                ->cascadeOnDelete();

            $table->foreign('uploaded_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindakan_koreksi_auditee_dokumen');
    }
};
