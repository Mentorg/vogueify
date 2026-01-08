<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderPaid;
use App\Models\Order;
use App\Notifications\Order\RequestOrderConfirmationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleOrderPaid implements ShouldQueue
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
    public function handle(OrderPaid $event): void
    {
        $order = Order::with(['cart.cartItems', 'user'])->find($event->order->id);

        if ($order->cart) {
            $order->cart->cartItems()->delete();
            $order->cart->update(['is_locked' => false]);
        }

        $order->user->notify(new RequestOrderConfirmationNotification($order));
    }
}
