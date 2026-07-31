<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Order Update - #' . $this->order->id)
                    ->line('The status of your order #' . $this->order->id . ' has been updated to: ' . strtoupper($this->order->status))
                    ->line('Payment status: ' . strtoupper($this->order->payment_status ?? 'pending'))
                    ->action('View Order', url(route('orders.show', $this->order->id)))
                    ->line('Thank you for shopping with us!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'payment_status' => $this->order->payment_status,
            'message' => 'Your order #' . $this->order->id . ' is now ' . $this->order->status . ' with payment ' . ($this->order->payment_status ?? 'pending'),
        ];
    }
}
