<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standar_mutu', function (Blueprint $table) {
            $table->integer('urutan')->nullable()->after('nama_standar_mutu');
        });
    }

    public function down(): void
    {
        Schema::table('standar_mutu', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }
};