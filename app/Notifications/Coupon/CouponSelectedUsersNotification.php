<?php

namespace App\Notifications\Coupon;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CouponSelectedUsersNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $coupon;

    /**
     * Create a new notification instance.
     */
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
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
            ->subject('You have received a new coupon!')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('We are excited to inform you that you have been selected to receive a special coupon!')
            ->line('Here are your coupon details:')
            ->line('Coupon Code: ' . $this->coupon->code)
            ->line('Entity: ' . ucfirst($this->coupon->couponType))
            ->line('Type: ' . ucfirst($this->coupon->type))
            ->line('Value: ' . ($this->coupon->type === 'percentage' ? $this->coupon->value . '%' : '$' . number_format($this->coupon->value, 2)))
            ->line(match ($this->coupon->couponType) {
                'categories' => 'Applicable Categories: ' . $this->coupon->categories->pluck('name')->join(', '),

                'products' => 'Applicable Products: ' . $this->coupon->products->pluck('name')->join(', '),

                'variations' => 'Applicable Product Variations: ' .
                    $this->coupon->productVariations->map(function ($variation) {

                        $productName = $variation->product->name;

                        $colorParts = [];

                        if ($variation->color) {
                            $colorParts[] = $variation->color->name;
                        }
                        if ($variation->primaryColor) {
                            $colorParts[] = $variation->primaryColor->name;
                        }
                        if ($variation->secondaryColor) {
                            $colorParts[] = $variation->secondaryColor->name;
                        }

                        if (!empty($colorParts)) {
                            return $productName . ' (' . implode(' / ', $colorParts) . ')';
                        }

                        return $productName;
                    })->join(', '),

                default => 'Applicable To: All Products',
            })
            ->line('Valid From: ' . $this->coupon->starts_at->toFormattedDateString())
            ->line('Valid Until: ' . $this->coupon->expires_at->toFormattedDateString())
            ->line('Add this coupon on the cart page to enjoy your reward.')
            ->action('Shop Now', url('/'))
            ->line('If you have any questions, feel free to contact our support team.')
            ->line('Thank you for being a valued customer!')
            ->salutation('Best regards, Vogueify Team');
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
