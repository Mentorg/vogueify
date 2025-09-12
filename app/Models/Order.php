<?php

namespace App\Models;

use App\Enums\AggregatedOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'order_status' => AggregatedOrderStatus::class,
    ];

    protected $fillable = [
        'shipping_date',
        'order_date',
        'order_status',
        'order_note',
        'subtotal',
        'shipping_cost',
        'tax_amount',
        'total',
        'user_id',
        'cart_id',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postcode',
        'shipping_country_id',
        'shipping_phone_number',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_postcode',
        'billing_country_id',
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

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function shippingAddress(): string
    {
        return implode(', ', array_filter([
            $this->shipping_address_line_1,
            $this->shipping_city,
            $this->shipping_postcode,
            $this->shipping_country_id,
        ]));
    }

    public function billingAddress(): string
    {
        return implode(', ', array_filter([
            $this->billing_address_line_1,
            $this->billing_city,
            $this->billing_postcode,
            $this->billing_country_id,
        ]));
    }

    public function shippingCountry()
    {
        return $this->belongsTo(Country::class, 'shipping_country_id');
    }

    public function billingCountry()
    {
        return $this->belongsTo(Country::class, 'billing_country_id');
    }

    public function orderStatuses(): array
    {
        return AggregatedOrderStatus::values();
    }
}
