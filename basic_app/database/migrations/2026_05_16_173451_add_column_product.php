<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // ── Up ────────────────────────────────────────────────────────────────────
    public function up(): void
    {
        // ── 1. Clean up the wrong brand_id column on products (if it exists) ──
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'brand_id')) {
                $table->dropColumn('brand_id');
            }
        });

        // ── 2. Re-add soft deletes on products (was accidentally dropped) ─────
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });

        // ── 3. Create the pivot table: brand_product ──────────────────────────
        Schema::create('brand_product', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('product_id');

            $table->timestamps();

            // ── Unique: one brand can be linked to a product only once ─────────
            $table->unique(['brand_id', 'product_id']);

            // ── Foreign keys ──────────────────────────────────────────────────
            $table->foreign('brand_id')
                  ->references('id')
                  ->on('brands')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    // ── Down ──────────────────────────────────────────────────────────────────
    public function down(): void
    {
        // Drop pivot table
        Schema::dropIfExists('brand_product');

        // Remove soft deletes from products
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};