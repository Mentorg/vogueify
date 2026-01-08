<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderConfirmed;
use App\Notifications\Order\OrderConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderConfirmedNotification implements ShouldQueue
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
    public function handle(OrderConfirmed $event): void
    {
        $event->order->user->notify(new OrderConfirmedNotification($event->order));
    }
}
