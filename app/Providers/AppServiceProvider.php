<?php

namespace App\Providers;

use App\Events\Order\OrderBillingAddressUpdated;
use App\Events\Order\OrderCancelled;
use App\Events\Order\OrderConfirmationRequested;
use App\Events\Order\OrderConfirmed;
use App\Events\Order\OrderItemShippingDateUpdated;
use App\Events\Order\OrderItemStatusUpdated;
use App\Events\Order\OrderPaid;
use App\Events\Order\OrderShippingAddressUpdated;
use App\Events\Order\OrderStatusUpdated;
use App\Listeners\Order\HandleOrderPaid;
use App\Listeners\Order\MarkOrderItemAsPaid;
use App\Listeners\Order\RestockOrderItems;
use App\Listeners\Order\SendOrderBillingAddressUpdatedNotification;
use App\Listeners\Order\SendOrderCancelledNotification;
use App\Listeners\Order\SendOrderConfirmationRequestNotification;
use App\Listeners\Order\SendOrderConfirmedNotification;
use App\Listeners\Order\SendOrderItemShippingDateUpdatedNotification;
use App\Listeners\Order\SendOrderItemStatusUpdatedNotification;
use App\Listeners\Order\SendOrderShippingAddressUpdatedNotification;
use App\Listeners\Order\SendOrderStatusUpdatedNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            Event::listen(OrderConfirmed::class, SendOrderConfirmedNotification::class);

            Event::listen(OrderCancelled::class, SendOrderCancelledNotification::class);

            Event::listen(OrderCancelled::class, RestockOrderItems::class);

            Event::listen(OrderStatusUpdated::class, SendOrderStatusUpdatedNotification::class);

            Event::listen(OrderBillingAddressUpdated::class, SendOrderBillingAddressUpdatedNotification::class);

            Event::listen(OrderShippingAddressUpdated::class, SendOrderShippingAddressUpdatedNotification::class);

            Event::listen(OrderItemStatusUpdated::class, SendOrderItemStatusUpdatedNotification::class);

            Event::listen(OrderItemShippingDateUpdated::class, SendOrderItemShippingDateUpdatedNotification::class);

            Event::listen(OrderConfirmationRequested::class, SendOrderConfirmationRequestNotification::class);

            Event::listen(OrderPaid::class, HandleOrderPaid::class);
            Event::listen(OrderPaid::class, MarkOrderItemAsPaid::class);
    }
}
