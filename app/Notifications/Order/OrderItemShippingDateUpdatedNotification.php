<?php

namespace App\Notifications\Order;

use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderItemShippingDateUpdatedNotification extends Notification
{
    use Queueable;

    private OrderItem $orderItem;

    /**
     * Create a new notification instance.
     */
    public function __construct(OrderItem $orderItem)
    {
        $this->orderItem = $orderItem;
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
            ->subject('Shipping date changed for an item in your order')
            ->line('The shipping date for your item "**' . $this->orderItem->productVariation->product->name . '**" has changed to **' . Carbon::parse($this->orderItem->shipping_date)->format('d.m.Y H:i') . '**.')
            ->line("We'll notify you again when it ships.");
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
