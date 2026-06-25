<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penugasan', function (Blueprint $table) {
            if (!Schema::hasColumn('penugasan', 'acc_kepala_p4mp')) {
                $table->enum('acc_kepala_p4mp', ['0', '1'])->default('0')->after('status_penugasan');
            }

            if (!Schema::hasColumn('penugasan', 'acc_kepala_p4mp_at')) {
                $table->timestamp('acc_kepala_p4mp_at')->nullable()->after('acc_kepala_p4mp');
            }

            if (!Schema::hasColumn('penugasan', 'acc_kepala_p4mp_by_user_id')) {
                $table->uuid('acc_kepala_p4mp_by_user_id')->nullable()->after('acc_kepala_p4mp_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penugasan', function (Blueprint $table) {
            $columns = [
                'acc_kepala_p4mp',
                'acc_kepala_p4mp_at',
                'acc_kepala_p4mp_by_user_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('penugasan', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
