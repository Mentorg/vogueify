<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'order_status' => OrderStatus::class,
    ];

    protected $fillable = [
        'shipping_date',
        'order_date',
        'subtotal',
        'shipping_cost',
        'tax_amount',
        'total',
        'user_id',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postcode',
        'shipping_country',
        'shipping_phone_number',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_postcode',
        'billing_country',
        'billing_phone_number',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateUniqueOrderNumber();
            }
        });
    }

    protected static function generateUniqueOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingAddress(): string
    {
        return implode(', ', array_filter([
            $this->shipping_address_line_1,
            $this->shipping_city,
            $this->shipping_postcode,
            $this->shipping_country,
        ]));
    }

    public function billingAddress(): string
    {
        return implode(', ', array_filter([
            $this->billing_address_line_1,
            $this->billing_city,
            $this->billing_postcode,
            $this->billing_country,
        ]));
    }
}
