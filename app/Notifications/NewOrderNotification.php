<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'user_name' => $this->order->user->name,
            'amount' => $this->order->total_amount,
            'message' => 'New shop transfer receipt submitted by ' . $this->order->user->name . ' (NGN ' . number_format($this->order->total_amount, 2) . ')',
        ];
    }
}
