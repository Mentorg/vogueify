<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderStatusUpdated;
use App\Notifications\Order\OrderStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderStatusUpdatedNotification implements ShouldQueue
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
    public function handle(OrderStatusUpdated $event): void
    {
        if ($event->order->order_status === $event->previousStatus) {
            return;
        }

        $event->order->user->notify(new OrderStatusUpdatedNotification($event->order));
    }
}
