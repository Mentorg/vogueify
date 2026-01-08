<?php

namespace App\Events\Order;

use App\Enums\AggregatedOrderStatus;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated
{
    use Dispatchable, SerializesModels;

    public $order;
    public AggregatedOrderStatus $previousStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, AggregatedOrderStatus $previousStatus)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
    }
}
