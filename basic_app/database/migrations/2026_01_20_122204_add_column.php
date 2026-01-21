<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إذا عندك بيانات قديمة في users، خليهم nullable
            $table->unsignedBigInteger('country_id')->nullable()->after('id');
            $table->unsignedBigInteger('city_id')->nullable()->after('country_id');

            // ✅ غيّر اسم جدول الدول حسب جدولك الحقيقي:
            // إذا جدولك اسمه countries (الأغلب) خليه زي ما هو
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->nullOnDelete();

            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // لازم نحذف القيود قبل الأعمدة
            $table->dropForeign(['country_id']);
            $table->dropForeign(['city_id']);

            $table->dropColumn(['country_id', 'city_id']);
        });
    }
};
