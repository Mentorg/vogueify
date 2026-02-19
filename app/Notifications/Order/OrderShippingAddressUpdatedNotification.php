<?php

namespace App\Notifications\Order;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShippingAddressUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Shipping address for your order was updated')
            ->line('The shipping address for your **#' . $this->order->order_number . '** order has been updated.')
            ->line('New Shipping Address:')
            ->line('Shipping Address Line 1: ' . $this->order->shipping_address_line_1)
            ->line('Shipping Address Line 2: ' . $this->order->shipping_address_line_2)
            ->line('Shipping City: ' . $this->order->shipping_city)
            ->lineIf($this->order->shipping_state !== null, 'Shipping State: ' . $this->order->shipping_state ?? 'N/A')
            ->line('Shipping Postcode: ' . $this->order->shipping_postcode)
            ->line('Shipping Country: ' . optional($this->order->shipping_country_id)->name ?? 'N/A')
            ->line('Shipping Phone Number: ' . $this->order->shipping_phone_number)
            ->line("If you didn't make this change, please contact support immediately!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
