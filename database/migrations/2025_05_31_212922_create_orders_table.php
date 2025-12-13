<?php

use App\Enums\OrderStatus;
use App\Enums\AggregatedOrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->date('shipping_date')->nullable();
            $table->date('order_date');
            $table->enum('order_status', AggregatedOrderStatus::values())->default(AggregatedOrderStatus::Pending->value);
            $table->text('order_note')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('shipping_address_line_1');
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_state')->nullable();
            $table->string('shipping_postcode');
            $table->unsignedBigInteger('shipping_country_id');
            $table->foreign('shipping_country_id')->references('id')->on('countries');
            $table->string('shipping_phone_number')->nullable();
            $table->string('billing_address_line_1')->nullable();
            $table->string('billing_address_line_2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postcode')->nullable();
            $table->unsignedBigInteger('billing_country_id')->nullable();
            $table->foreign('billing_country_id')->references('id')->on('countries');
            $table->string('billing_phone_number')->nullable();
            $table->foreignId('cart_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variation_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->nullable()->constrained('sizes')->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price_at_time', 6, 2);
            $table->date('shipping_date')->nullable();
            $table->enum('order_status', OrderStatus::values())->default(OrderStatus::Pending->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
    }
};
