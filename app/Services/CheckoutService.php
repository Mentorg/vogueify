<?php

namespace App\Services;

use App\Enums\AggregatedOrderStatus;
use App\Enums\OrderStatus;
use App\Models\Country;
use App\Models\Order;
use App\Notifications\Order\RequestOrderConfirmationNotification;
use App\Pricing\Exceptions\InvalidCouponException;
use App\Pricing\PricingService;
use App\Pricing\Validators\CouponValidator;
use Illuminate\Support\Facades\Log;
use Exception;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;

class CheckoutService
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function getCheckout($request)
    {
        $user = $request->user();
        if (!$user) abort(404);

        $user->load([
            'cart.cartItems.productVariation.product',
            'address.country',
            'orders.items.productVariation.product',
        ]);

        $pendingOrder = $user->orders
            ->where('order_status', AggregatedOrderStatus::Pending->value)
            ->sortByDesc('created_at')
            ->first();

        $cartItems = $user->cart?->cartItems;
        if (!$cartItems || $cartItems->isEmpty()) {
            abort(404, 'No cart found for checkout.');
        }

        $address = $user->address;
        $countryCode = $address?->country?->iso_code;
        $state = $address?->state;
        $destination = [
            'country_id' => $address?->country_id,
            'postcode' => $address?->postcode,
        ];

        $couponCode = $request->input('coupon') ?? session('applied_coupon');
        $couponModel = null;
        if ($couponCode) {
            try {
                $couponModel = app(CouponValidator::class)->validate($couponCode, $user);
            } catch (InvalidCouponException $e) {
                session()->flash('coupon_error', $e->getMessage());
            }
        }

        $totals = $this->pricingService->calculate($cartItems, [
            'coupon' => $couponModel,
            'countryCode' => $countryCode,
            'state' => $state,
            'destination' => $destination,
        ]);

        return [
            'user' => $user,
            'cart' => $user->cart,
            'address' => $address,
            'pendingOrder' => $pendingOrder,
            'subtotal' => $totals['subtotal'],
            'discount' => $totals['discount'],
            'shipping' => $totals['shipping'],
            'tax' => $totals['tax'],
            'total' => $totals['total'],
            'coupon' => $couponModel?->code,
            'countries' => Country::all(['id', 'name', 'iso_code']),
            'coupon_error' => session('coupon_error') ?? null,
        ];
    }

    public function createSession(Order $order)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = [];

        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item->productVariation->product->name,
                    ],
                    'unit_amount' => intval($item->price_at_time * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        if ($order->shipping_cost > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Shipping'],
                    'unit_amount' => intval($order->shipping_cost * 100),
                ],
                'quantity' => 1,
            ];
        }

        if ($order->tax_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Tax'],
                    'unit_amount' => intval($order->tax_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        $discounts = [];

        if ($order->discount_amount > 0) {
            $coupon = \Stripe\Coupon::create([
                'name' => $order->coupon_code ?? 'Order Discount',
                'amount_off' => intval($order->discount_amount * 100),
                'currency' => 'eur',
                'duration' => 'once',
            ]);

            $discounts[] = ['coupon' => $coupon->id];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'customer_email' => $order->user->email,
            'line_items' => $lineItems,
            'discounts' => $discounts,
            'success_url' => route('checkout.success', ['session_id' => '{CHECKOUT_SESSION_ID}']),
            // 'cancel_url' => route('checkout.cancel'),
            'metadata' => ['order_id' => $order->id],
        ]);

        $order->update(['stripe_session_id' => $session->id]);

        return $session;
    }

    public function success($request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            throw new Exception('Missing Stripe session ID.');
        }

        $session = StripeSession::retrieve($sessionId);
        $paymentIntent = PaymentIntent::retrieve($session->payment_intent);

        $orderId = $session->metadata->order_id ?? null;

        if (!$orderId) {
            throw new Exception('Order ID not found in session metadata.');
        }

        $order = Order::with(['items.productVariation.product', 'items.size', 'cart.cartItems', 'user'])->find($orderId);

        if (!$order) {
            throw new Exception("Order with ID {$orderId} not found.");
        }

        if (
            $session->payment_status === 'paid' &&
            $order->order_status !== AggregatedOrderStatus::Paid
        ) {
            $order->order_status = AggregatedOrderStatus::Paid;
            $order->save();

            foreach ($order->items as $item) {
                $item->order_status = OrderStatus::Paid;
                $item->save();
            }

            if ($order->cart) {
                $order->cart->cartItems()->delete();
                $order->cart->is_locked = false;
                $order->cart->save();
            }

            $order->user->notify(new RequestOrderConfirmationNotification($order));

            Log::info('Order marked as paid via success page.', [
                'order_id' => $order->id,
                'session_id' => $sessionId,
            ]);
        }

        return [
            'session' => $session,
            'paymentIntent' => $paymentIntent,
            'order' => $order
        ];
    }
}
