<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_ORDER_PLACED = 'order-placed';
    const STATUS_ORDER_CONFIRMED = 'order-confirmed';
    const STATUS_ORDER_PROCESSING = 'order-processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_IN_TRANSIT = 'in-transit';
    const STATUS_OUT_FOR_DELIVERY = 'out-for-delivery';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_ATTEMPTED_DELIVERY = 'attempted-delivery';
    const STATUS_CANCELED = 'canceled';
    const STATUS_HELD_AT_CUSTOMS = 'held-at-customs';
    const STATUS_AWAITING_PICKUP = 'awaiting-pickup';
    const STATUS_DELAYED = 'delayed';
    const STATUS_LOST = 'lost';

    protected $fillable = [
        'order_number',
        'shipping_date',
        'order_date',
        'subtotal',
        'shipping_cost',
        'tax_amount',
        'total',
        'order_status',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingAddress()
    {
        return "{$this->shipping_address_line_1}, {$this->shipping_city}, {$this->shipping_postcode}, {$this->shipping_country}";
    }

    public function billingAddress()
    {
        return "{$this->billing_address_line_1}, {$this->billing_city}, {$this->billing_postcode}, {$this->billing_country}";
    }
}
