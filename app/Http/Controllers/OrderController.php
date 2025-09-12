<?php

namespace App\Http\Controllers;

use App\Enums\AggregatedOrderStatus;
use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OrderController extends Controller
{
    use AuthorizesRequests;

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

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $this->authorize('viewInProfile', $order);

        return Inertia::render('Order', [
            'orderDetails' => $this->orderService->getOrder($order),
            'countries' => Country::all(['id', 'name', 'iso_code'])
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
        $this->authorize('cancel', $order);

        $this->orderService->cancel($order);
    }

    public function confirm(Order $order)
    {
        $this->authorize('confirm', $order);

        $result = $this->orderService->confirm($order);

        return redirect()->route('home')->with($result['status'], $result['message']);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('modify', $order);

        $request->validate([
            'order_status' => 'required|in:' . implode(',', AggregatedOrderStatus::values()),
        ]);

        $this->orderService->updateStatus($order, $request->order_status);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function updateItemStatus(Request $request, OrderItem $orderItem)
    {
        $this->authorize('modify', $orderItem);

        $request->validate([
            'order_status' => 'required|in:' . implode(',', OrderStatus::values()),
        ]);

        $this->orderService->updateItemStatus($orderItem, $request->order_status);

        return redirect()->back()->with('success', 'Order item status updated successfully.');
    }

    public function updateItemShippingDate(Request $request, OrderItem $orderItem)
    {
        $this->authorize('modify', $orderItem);

        $request->validate([
            'shipping_date' => 'nullable|date',
        ]);

        $this->orderService->updateItemShippingDate($orderItem, $request->shipping_date);

        return redirect()->back()->with('success', 'Order item shipping date updated successfully.');
    }

    public function updateNote(Request $request, Order $order)
    {
        $this->authorize('modify', $order);

        $request->validate([
            'order_note' => 'nullable|string|max:500'
        ]);

        $this->orderService->updateNote($order, $request->order_note);

        return redirect()->back()->with('success', 'Order note updated successfully.');
    }

    public function updateShippingAddress(Request $request, Order $order)
    {
        $this->authorize('modify', $order);

        $validated = $request->validate([
            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|min:2|max:255',
            'shipping_state' => 'nullable|string|max:255',
            'shipping_postcode' => 'required|string|alpha_num',
            'shipping_country_id' => 'required|integer|exists:countries,id',
            'shipping_phone_number' => 'nullable|string|min:7|max:20',
            'shipping_date' => 'nullable|date',
        ]);

        $this->orderService->updateShippingAddress($order, $validated);
    }

    public function updateBillingAddress(Request $request, Order $order)
    {
        $this->authorize('modify', $order);

        $validated = $request->validate([
            'billing_address_line_1' => 'required|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'required|string|min:2|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_postcode' => 'required|string|alpha_num',
            'billing_country_id' => 'nullable|integer|exists:countries,id',
            'billing_phone_number' => 'nullable|string|min:7|max:20',
        ]);

        $this->orderService->updateBillingAddress($order, $validated);
    }

    public function resendConfirmation(Order $order)
    {
        $this->authorize('modify', $order);

        $this->orderService->resendConfirmation($order);
    }

    public function continueOrder(Order $order)
    {
        $this->authorize('continueOrder', $order);

        return Inertia::location(route('checkout.create', ['order_id' => $order->id]));
    }
}
