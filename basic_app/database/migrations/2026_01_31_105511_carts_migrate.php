<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_additional_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->foreignId('cart_id')
                ->nullable()
                ->constrained('carts')
                ->nullOnDelete();

            // ✅ عدّلي اسم الجدول إذا كان مختلف عندك
            $table->foreignId('additional_id')
                ->nullable()
                ->constrained('additionals')
                ->nullOnDelete();

            // ✅ إذا فعلاً بدك user_id لازم تضيفيه
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ✅ يمنع تكرار نفس الإضافة لنفس المنتج داخل نفس الطلب
            $table->unique(['cart_id', 'product_id', 'additional_id'], 'order_product_additional_unique');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_additional_product');
    }
};
