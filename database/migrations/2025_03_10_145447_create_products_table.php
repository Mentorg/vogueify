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
            $table->string('slug')->unique();
            $table->timestamps();
            $table->unsignedBigInteger('category_id')->index();
            $table->foreign('category_id')->references('id')->on('product_categories')->cascadeOnDelete();
        });

        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->decimal('price', 6, 2);
            $table->string('sku')->unique();
            $table->timestamps();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('primary_color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('secondary_color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('product_type_id')->constrained('product_types')->cascadeOnDelete();
        });

        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('product_variation_size', function (Blueprint $table) {
            $table->id();
            $table->integer('stock')->default(0);
            $table->unsignedBigInteger('product_variation_id');
            $table->foreign('product_variation_id')->references('id')->on('product_variations')->cascadeOnDelete();
            $table->unsignedBigInteger('size_id');
            $table->foreign('size_id')->references('id')->on('sizes')->cascadeOnDelete();
            $table->unique(['product_variation_id', 'size_id']);
        });

        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('hex_code')->nullable();
            $table->timestamps();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('image');
            $table->timestamps();
        });

        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('label');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('product_categories')->cascadeOnDelete();
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
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('product_variation_size');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('product_types');
        Schema::dropIfExists('wishlist');
    }
};
