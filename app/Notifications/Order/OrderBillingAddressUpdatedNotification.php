<?php

namespace App\Notifications\Order;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderBillingAddressUpdatedNotification extends Notification implements ShouldQueue
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
            ->subject('Billing address for your order was updated')
            ->line('Your billing address for your **#' . $this->order->order_number . '** order has been updated.')
            ->line('New Billing Address:')
            ->line('Billing Address Line 1: ' . $this->order->billing_address_line_1)
            ->line('Billing Address Line 2: ' . $this->order->billing_address_line_2)
            ->line('Billing City: ' . $this->order->billing_city)
            ->lineIf($this->order->billing_state !== null, 'Billing State: ' . $this->order->billing_state ?? 'N/A')
            ->line('Billing Postcode: ' . $this->order->billing_postcode)
            ->line('Billing Country: ' . optional($this->order->billingCountry)->name ?? 'N/A')
            ->line('Billing Phone Number: ' . $this->order->billing_phone_number)
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
