<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderConfirmationRequested;
use App\Notifications\Order\RequestOrderConfirmationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderConfirmationRequestNotification implements ShouldQueue
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
    public function handle(OrderConfirmationRequested $event): void
    {
        $event->order->user->notify(new RequestOrderConfirmationNotification($event->order));
    }
}
