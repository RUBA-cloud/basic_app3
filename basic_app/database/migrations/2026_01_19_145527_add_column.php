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

        Schema::table("transpartation_way_history", function (Blueprint $table) {
     $table->double("days_count");
            $table->unsignedBigInteger('type_id')->nullable();
     $table->foreign('type_id')->references('transpartation_types')->nullable();
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
