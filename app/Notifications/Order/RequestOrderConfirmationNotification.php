<?php

namespace App\Notifications\Order;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestOrderConfirmationNotification extends Notification
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
        $confirmationUrl = route('order.confirm', ['order' => $this->order->id]);

        return (new MailMessage)
            ->subject('Please Confirm Your Order')
            ->greeting('Hello there,')
            ->line('Thank you for your order!')
            ->line('Please confirm your order to proceed with processing.')
            ->action('Confirm Order', $confirmationUrl)
            ->line("If you didn't place this order, please contact support.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->order_status,
            'message' => 'Request to confirm order #' . $this->order->order_number
        ];
    }
}
