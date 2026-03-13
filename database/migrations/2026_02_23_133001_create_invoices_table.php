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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('order_id')->unique()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('file_path')->nullable();
            $table->boolean('pdf_generated')->default(false);
            $table->string('status')->default('issued');
            $table->string('currency', 3)->default('EUR');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->text('internal_note')->nullable();
            $table->timestamp('issued_at');
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('product_image')->nullable();
            $table->string('product_name');
            $table->string('product_sku')->unique();
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        Schema::create('invoice_counters', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->unique();
            $table->unsignedBigInteger('current_number')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoice_counters');
    }
};
