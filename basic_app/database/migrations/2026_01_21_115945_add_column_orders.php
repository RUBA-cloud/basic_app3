<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // ✅ add column
            $table->foreignId('transpartation_id')
                ->nullable()
                ->constrained('transportation_ways') // عدّل اسم الجدول إذا عندك مختلف
                ->nullOnDelete(); // on delete set null
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // ✅ drop FK then column
            $table->dropForeign(['transpartation_id']);
            $table->dropColumn('transpartation_id');
        });
    }
};
