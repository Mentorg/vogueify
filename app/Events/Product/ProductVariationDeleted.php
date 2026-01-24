<?php

namespace App\Events\Product;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductVariationDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ?string $imagePath;

    /**
     * Create a new event instance.
     */
    public function __construct(?string $imagePath)
    {
        $this->imagePath = $imagePath;
    }
}
