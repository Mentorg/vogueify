<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(StoreOrderRequest $request, Order $order)
    {
        $createdOrder = $this->orderService->create($order, $request);
        return response()->json([
            'success' => true,
            'message' => 'Order stored successfully.',
            'order' => $createdOrder
        ]);
    }
}
