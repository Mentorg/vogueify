<?php

namespace App\Notifications\Order;

use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderItemStatusUpdatedNotification extends Notification
{
    use Queueable;

    protected OrderItem $orderItem;

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
            ->subject('Status Update for Item in Your Order #' . $this->orderItem->order->order_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The status of "**' . $this->orderItem->productVariation->product->name . '**" in your order has been updated to **' . $this->orderItem->order_status . '**.')
            ->action('View Order', route('order.show', $this->orderItem->order->id));
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
