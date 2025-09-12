<?php

namespace App\Enums;

enum AggregatedOrderStatus: string
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

    public static function fromOrderStatuses(array $statuses): self
    {
        if (in_array(self::Lost->value, $statuses)) {
            return self::Lost;
        }

        if (in_array(self::HeldAtCustoms->value, $statuses)) {
            return self::HeldAtCustoms;
        }

        if (in_array(self::Delayed->value, $statuses)) {
            return self::Delayed;
        }

        if (in_array(self::AwaitingPickup->value, $statuses)) {
            return self::AwaitingPickup;
        }

        if (in_array(self::AttemptedDelivery->value, $statuses)) {
            return self::AttemptedDelivery;
        }

        if (in_array(self::OutForDelivery->value, $statuses)) {
            return self::OutForDelivery;
        }

        if (in_array(self::InTransit->value, $statuses)) {
            return self::InTransit;
        }

        if (in_array(self::Shipped->value, $statuses)) {
            return self::Shipped;
        }

        if (in_array(self::Processing->value, $statuses)) {
            return self::Processing;
        }

        if (in_array(self::Confirmed->value, $statuses)) {
            return self::Confirmed;
        }

        if (in_array(self::Paid->value, $statuses)) {
            return self::Paid;
        }

        if (in_array(self::Pending->value, $statuses)) {
            return self::Pending;
        }

        return self::Pending;
    }
}
