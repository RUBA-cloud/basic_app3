<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("modules", function (Blueprint $table) {
             $table->boolean('country_module')->default(false);
            $table->boolean('city_module')->default(false);
            $table->boolean('transpartation_type_module')->default(false);
            $table->boolean('transportation_way_module')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
