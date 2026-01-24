<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductCreated;
use App\Jobs\Product\ProcessProductImageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleProductCreated implements ShouldQueue
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
    public function handle(ProductCreated $event): void
    {
        ProcessProductImageJob::dispatch($event->productId);
    }
}
