<?php

namespace App\Events\Product;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $imagePaths;

    /**
     * Create a new event instance.
     */
    public function __construct(array $imagePaths)
    {
        $this->imagePaths = $imagePaths;
    }
}
