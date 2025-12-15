<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order-status', function (Blueprint $table) {
            if (!Schema::hasColumn('order_status', 'status')) {
                $table->unsignedTinyInteger('status')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_status', function (Blueprint $table) {
            if (Schema::hasColumn('order_status', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
