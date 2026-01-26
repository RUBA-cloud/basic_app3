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
        Schema::create("order_additional_product", function (Blueprint $table) {

            $table->bigIncrements("id");
            $table->unsignedBigInteger("order_id");
            $table->unsignedBigInteger("additional_id");
            $table->unsignedBigInteger("product_id");
            $table->foreign("product_id")->references("id")->on("products");
            $table->foreign("order_id")->references("id")->on("order_id");
            $table->foreign("additional_id")->references("id")->on("additional");
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
