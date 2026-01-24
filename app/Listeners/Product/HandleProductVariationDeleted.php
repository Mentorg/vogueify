<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductVariationDeleted;
use App\Jobs\Product\DeleteProductImageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleProductVariationDeleted implements ShouldQueue
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
    public function handle(ProductVariationDeleted $event): void
    {
        if ($event->imagePath) {
            DeleteProductImageJob::dispatch([$event->imagePath]);
        }
    }
}
