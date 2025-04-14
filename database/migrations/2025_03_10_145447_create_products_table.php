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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->json('features')->nullable();
            $table->enum('gender', ['men', 'women', 'unisex'])->default('unisex');
            $table->timestamps();
            $table->unsignedBigInteger('category_id')->index();
            $table->foreign('category_id')->references('id')->on('product_categories')->cascadeOnDelete();
        });

        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('color');
            $table->string('type');
            $table->decimal('price', 6, 2);
            $table->integer('stock')->default(0);
            $table->string('sku')->unique();
            $table->timestamps();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('image');
            $table->timestamps();
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('wishlist');
    }
};
