<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automated email reminders to event registrants 2 days and 1 day before the event.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = \Carbon\Carbon::now();
        
        // Find events where reminder_at has passed and reminder has not been sent yet
        $events = \App\Models\Event::where('reminder_at', '<=', $now)
                                   ->where('reminder_sent', false)
                                   ->whereNotNull('reminder_at')
                                   ->get();

        foreach ($events as $event) {
            $registrations = $event->registrations;
            
            foreach ($registrations as $reg) {
                try {
                    // Calculate days to go for the email context
                    $eventDate = \Carbon\Carbon::parse($event->event_date);
                    $daysToGo = $now->diffInDays($eventDate);

                    \Illuminate\Support\Facades\Mail::to($reg->email)->send(new \App\Mail\EventReminderMail($event, $reg, $daysToGo));
                    $this->info("Reminder sent to {$reg->email} for event: {$event->title}");
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder to {$reg->email}: " . $e->getMessage());
                }
            }

            // Mark reminder as sent for this event
            $event->reminder_sent = true;
            $event->save();
        }

        $this->info('Event reminders process completed.');
    }
}
