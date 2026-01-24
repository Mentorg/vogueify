<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductDeleted;
use App\Jobs\Product\DeleteProductImageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleProductDeleted implements ShouldQueue
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
    public function handle(ProductDeleted $event): void
    {
        DeleteProductImageJob::dispatch($event->imagePaths);
    }
}
