<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tindakan_koreksi', function (Blueprint $table) {
            if (!Schema::hasColumn('tindakan_koreksi', 'auditee_signed_by_user_id')) {
                $table->uuid('auditee_signed_by_user_id')->nullable()->after('verified_at');
            }

            if (!Schema::hasColumn('tindakan_koreksi', 'auditee_signed_at')) {
                $table->timestamp('auditee_signed_at')->nullable()->after('auditee_signed_by_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tindakan_koreksi', function (Blueprint $table) {
            foreach (['auditee_signed_by_user_id', 'auditee_signed_at'] as $column) {
                if (Schema::hasColumn('tindakan_koreksi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
