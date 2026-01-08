<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderBillingAddressUpdated;
use App\Notifications\Order\OrderBillingAddressUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderBillingAddressUpdatedNotification implements ShouldQueue
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
    public function handle(OrderBillingAddressUpdated $event): void
    {
        $event->order->user->notify(new OrderBillingAddressUpdatedNotification($event->order));
    }
}
