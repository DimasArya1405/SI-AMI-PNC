<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jawaban_audit', function (Blueprint $table) {
            $table->string('kategori_temuan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('jawaban_audit', function (Blueprint $table) {
            $table->string('kategori_temuan')->nullable(false)->change();
        });
    }
};
