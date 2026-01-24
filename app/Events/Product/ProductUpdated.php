<?php

namespace App\Events\Product;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public array $deletedImagePaths;

    /**
     * Create a new event instance.
     */
    public function __construct(Product $product, array $deletedImagePaths = [])
    {
        $this->product = $product;
        $this->deletedImagePaths = $deletedImagePaths;
    }
}
