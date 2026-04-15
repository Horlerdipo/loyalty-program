<?php

namespace App\Listeners;

use App\Events\BadgeUnlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class BadgeUnlockedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BadgeUnlocked $event): void
    {
        $now = now()->toDateTimeString();
        Log::info("Badge {$event->userBadge->badge?->name->name} successfully unlocked at $now for user {$event->userBadge->user?->name} and {$event->userBadge->cashback_amount} paid successfully", [
            'user_badge_id' => $event->userBadge->id,
        ]);

        $event->userBadge->update([
            'cashback_paid' => true,
        ]);
    }
}
