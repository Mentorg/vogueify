<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderItemShippingDateUpdated;
use App\Notifications\Order\OrderItemShippingDateUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderItemShippingDateUpdatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderItemShippingDateUpdated $event): void
    {
        $event->orderItem->order->user->notify(new OrderItemShippingDateUpdatedNotification($event->orderItem));
    }
}
