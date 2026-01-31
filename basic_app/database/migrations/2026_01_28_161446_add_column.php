<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_status_history', function (Blueprint $table) {
            $table->string('icon_data')->default('')->after('name_ar'); // عدّلي after حسب أعمدتك
            $table->string('colors')->default('')->after('icon_data');
        });
    }

    public function down(): void
    {
        Schema::table('order_status_history', function (Blueprint $table) {
            $table->dropColumn(['icon_data', 'colors']);
        });
    }
};
