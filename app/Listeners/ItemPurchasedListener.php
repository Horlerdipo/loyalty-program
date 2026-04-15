<?php

namespace App\Listeners;

use App\Actions\User\UnlockAchievements;
use App\Events\ItemPurchased;
use App\Models\Purchase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Config;

class ItemPurchasedListener implements ShouldQueue
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
    public function handle(ItemPurchased $event): void
    {
        if (! Config::boolean('app.loyalty_program.active')) {
            return;
        }

        $totalRevenue = Purchase::query()
            ->where('user_id', $event->purchase->user_id)
            ->sum('amount');

        $totalSales = Purchase::query()
            ->where('user_id', $event->purchase->user_id)
            ->count();

        if (is_null($event->purchase->user)) {
            return;
        }

        (new UnlockAchievements)->execute($event->purchase->user, $totalSales, floatval($totalRevenue));
    }
}
