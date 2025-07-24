<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
        $order = $this->orderService->create($order, $request);

        if ($request->wantsJson()) {
            return response()->json(['order_id' => $order->id]);
        }

        return redirect()->route('checkout', ['order_id' => $order->id]);
    }

    public function show(Order $order)
    {
        return Inertia::render('Order', [
            'orderDetails' => $this->orderService->getOrder($order)
        ]);
    }

    public function getUserOrders(Request $request)
    {
        return Inertia::render('Orders', [
            'orders' => $this->orderService->getUserOrders($request)
        ]);
    }

    public function cancel(Order $order)
    {
        $this->orderService->cancel($order);
    }
}
