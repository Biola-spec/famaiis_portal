<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'event',
            'title' => 'Upcoming Event: ' . $this->event->title,
            'message' => 'New event scheduled on ' . date('d M Y', strtotime($this->event->event_date)),
            'event_id' => $this->event->id,
            'timestamp' => now(),
        ];
    }
}
