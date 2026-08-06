<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InsufficientWalletFundsNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $student,
        private readonly float $amount,
        private readonly float $balance,
        private readonly User $attendant
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'student_id' => $this->student->id,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'attendant_id' => $this->attendant->id,
            'message' => 'Insufficient wallet funds for ' . $this->student->name . ' on a shop purchase.',
        ];
    }
}
