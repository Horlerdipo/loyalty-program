<?php

namespace App\Actions\User;

use App\Actions\BaseAction;
use App\DTOs\BaseResponseDto;
use App\Enums\AchievementType;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\ItemPurchased;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnlockBadges extends BaseAction
{
    public function execute(User $user): BaseResponseDto
    {
        try {

            //fetch achievements count
            //fetch earned badges
            //fetch badges that needs to be earned with the required_count <= achievement count
            $totalAchievements = $user->achievements()->count();
            $earnedBadges = $user->badges()->select('badge_id');
            $newBadges = Badge::query()
                ->where('required_count', '<=', $totalAchievements)
                ->whereNotIn('id', $earnedBadges)
                ->get();

            if ($newBadges->isEmpty()) {
                return $this->successResponse('No new badges unlocked');
            }

            //record unlocked badges
            //dispatch BadgeUnlocked event
            //return success
            $userBadges = DB::transaction(function () use ($user, $newBadges) {
               $now = now()->toDateTimeString();
                $cashbackAmount = Config::float('app.loyalty_program.cashback_paid_per_badge');

                /** @var array<int, array<string, int|bool|float|string>> $records */
                $records = $newBadges->map(fn($badge) => [
                    'user_id'       => $user->id,
                    'badge_id'      => $badge->id,
                    'cashback_paid' => false,
                    'cashback_amount' => $cashbackAmount,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ])->toArray();

                UserBadge::query()->insert($records);

                return UserBadge::query()
                    ->where('user_id', $user->id)
                    ->whereIn('badge_id', $newBadges->pluck('id'))
                    ->get();
            });

            $userBadges->each(fn ($userBadge) => BadgeUnlocked::dispatch($userBadge));

            return $this->successResponse('Badges unlocked successfully');
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
