<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderShippingAddressUpdated;
use App\Notifications\Order\OrderShippingAddressUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderShippingAddressUpdatedNotification implements ShouldQueue
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
    public function handle(OrderShippingAddressUpdated $event): void
    {
        $event->order->user->notify(new OrderShippingAddressUpdatedNotification($event->order));
    }
}
