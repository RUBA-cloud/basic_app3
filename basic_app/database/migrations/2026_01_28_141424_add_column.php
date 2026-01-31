<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transpartation_way', function (Blueprint $table) {
            // إذا عندك بيانات قديمة، خليها nullable أو default
            $table->double('days_count')->default(0);
            // أو بدل السطر فوق:
            // $table->double('days_count')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transpartation_way', function (Blueprint $table) {
            $table->dropColumn('days_count');
        });
    }
};
