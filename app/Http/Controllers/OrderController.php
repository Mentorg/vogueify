<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Notifications\Order\OrderConfirmedNotification;
use App\Services\OrderService;
use Exception;
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
        try {
            $order = $this->orderService->create(new Order, $request);

            return Inertia::location(route('checkout.create', ['order_id' => $order->id]));

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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

    public function confirm(Order $order)
    {
        if ($order->order_status === OrderStatus::Paid) {
            $order->update(['order_status' => OrderStatus::Confirmed]);

            $user = $order->user;
            $user->notify(new OrderConfirmedNotification($order));
        }

        return redirect()->route('home')->with('success', 'Order confirmed!');
    }
}
