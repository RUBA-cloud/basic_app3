<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_additional_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            // ✅ عدّلي اسم الجدول إذا كان مختلف عندك
            $table->foreignId('additional_id')
                ->nullable()
                ->constrained('additional')
                ->nullOnDelete();

            // ✅ إذا فعلاً بدك user_id لازم تضيفيه
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->unique(['order_id', 'product_id', 'additional_id'], 'order_product_additional_unique');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_additional_product');
    }
};
