<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_branches', function (Blueprint $table) {
            // ✅ add column
            if (!Schema::hasColumn('company_branches', 'company_info_id')) {
                $table->unsignedBigInteger('company_info_id')->nullable()->after('id');
            }

            // ✅ add foreign key
            // (name the constraint to be able to drop it safely later)

        });
    }

    public function down(): void
    {
        Schema::table('company_branches', function (Blueprint $table) {
            // ✅ drop FK then column
            if (Schema::hasColumn('company_branches', 'company_info_id')) {
                $table->dropForeign('company_branches_company_info_id_fk');
                $table->dropColumn('company_info_id');
            }
        });
    }
};
