<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transpartation_way_history', function (Blueprint $table) {
            // اختاري واحد:
            $table->double('days_count')->default(0);
            // أو:
            // $table->double('days_count')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transpartation_way_history', function (Blueprint $table) {
            $table->dropColumn('days_count');
        });
    }
};
