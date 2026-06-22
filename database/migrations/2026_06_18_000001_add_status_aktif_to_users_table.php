<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status_aktif')) {
                $table->boolean('status_aktif')->default(true)->after('role');
            }
        });

        $kepalaP4mpAktif = DB::table('users')
            ->where('role', 'kepala_p4mp')
            ->whereNull('deleted_at')
            ->orderBy('created_at')
            ->value('id');

        if ($kepalaP4mpAktif) {
            DB::table('users')
                ->where('role', 'kepala_p4mp')
                ->where('id', '!=', $kepalaP4mpAktif)
                ->update(['status_aktif' => false]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status_aktif')) {
                $table->dropColumn('status_aktif');
            }
        });
    }
};
