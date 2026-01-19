<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // لو تحب تحدد المكان: حط after لعمود موجود فعلاً
            // مثال: ->after('user_id')
            $table->foreignId('way_id')
                ->nullable()
                ->constrained('transportation_ways')   // references id by default
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // الأفضل والأضمن في Laravel
            $table->dropConstrainedForeignId('way_id');
        });
    }
};
