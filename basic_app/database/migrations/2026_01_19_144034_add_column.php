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
        //
        Schema::create("transpartation_way", function (Blueprint $table) {
 $table->bigIncrements("id");
           $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->boolean('is_active')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
         $table->unsignedBigInteger('city_id')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  $table->foreign('country_id')
                  ->references('id')
                  ->on('country')
                  ->onDelete('set null');

                    $table->foreign('city_id')
                  ->references('id')
                  ->on('cities')
                  ->onDelete('set null');

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
