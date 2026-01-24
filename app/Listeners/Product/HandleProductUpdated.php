<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductUpdated;
use App\Jobs\Product\DeleteProductImageJob;
use App\Jobs\Product\ProcessProductImageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleProductUpdated implements ShouldQueue
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
    public function handle(ProductUpdated $event): void
    {
        ProcessProductImageJob::dispatch($event->product->id);

        if (!empty($event->deletedImagePaths)) {
            DeleteProductImageJob::dispatch($event->deletedImagePaths);
        }
    }
}
