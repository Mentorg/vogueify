<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RestockOrderItems implements ShouldQueue
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
    public function handle(OrderCancelled $event): void
    {
        foreach ($event->order->items as $item) {
                $variation = $item->productVariation;

                if (!is_null($variation?->stock)) {
                    $variation->stock += $item->quantity;
                    $variation->save();
                }
            }
    }
}
