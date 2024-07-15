<?php

namespace App\Console\Commands;

use App\Notifications\EventReminderNotification;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This sends users notifications before event starts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = \App\Models\Event::with('attendees.user')->whereBetween('start_time', [now(), now()->addDay()])->get();

        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);

        $events->each(fn($event) => $event->attendees->each(
            fn($attendee) => $this->info($attendee->user->notify(
                new EventReminderNotification($event)
            ))));
            
        $this->info("Found {$eventCount} {$eventLabel}");
        $this->info('Reminders finally sent!');

    }
}
