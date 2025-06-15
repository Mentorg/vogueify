<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending             = 'pending';
    case Paid                = 'paid';
    case Confirmed           = 'confirmed';
    case Canceled            = 'canceled';
    case Processing          = 'processing';
    case Shipped             = 'shipped';
    case InTransit           = 'in-transit';
    case OutForDelivery      = 'out-for-delivery';
    case Delivered           = 'delivered';
    case AttemptedDelivery   = 'attempted-delivery';
    case AwaitingPickup      = 'awaiting-pickup';
    case Delayed             = 'delayed';
    case HeldAtCustoms       = 'held-at-customs';
    case Lost                = 'lost';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
