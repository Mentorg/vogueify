<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderItemStatusUpdated;
use App\Notifications\Order\OrderItemStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderItemStatusUpdatedNotification implements ShouldQueue
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
    public function handle(OrderItemStatusUpdated $event): void
    {
        $event->orderItem->order->user->notify(new OrderItemStatusUpdatedNotification($event->orderItem));
    }
}
