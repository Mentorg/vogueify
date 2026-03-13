<?php

namespace App\Notifications\Invoice;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class ResendInvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
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
        $mail = (new MailMessage)
            ->subject("Invoice {$this->invoice->invoice_number} - Copy of Your Invoice")
            ->greeting("Hello {$notifiable->name},")
            ->line("Attached to this email you can find another copy of your invoice.")
            ->line("Order number: #{$this->invoice->order->order_number}")
            ->line("Invoice number: {$this->invoice->invoice_number}")
            ->line("Invoice Date: {$this->invoice->issued_at}")
            ->line("Total Amount: {$this->invoice->total}")
            ->line('Thank you for using our platform!');

        if ($this->invoice?->file_path) {
            $mail->attach(
                storage_path('app/public/' . $this->invoice->file_path),
                [
                    'as' => 'Invoice-' . $this->invoice->invoice_number . '.pdf',
                    'mime' => 'application/pdf',
                ]
            );
        }

        return $mail;
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
