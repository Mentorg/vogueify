<?php

namespace App\Events\Product;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $productId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }
}
