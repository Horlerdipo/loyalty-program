<?php

namespace App\Actions\User;

use App\Actions\BaseAction;
use App\DTOs\BaseResponseDto;
use App\Enums\AchievementType;
use App\Events\AchievementUnlocked;
use App\Events\ItemPurchased;
use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnlockAchievements extends BaseAction
{
    public function execute(User $user, int $totalSales, float $totalRevenue): BaseResponseDto
    {
        try {

            //fetch earned achievements
            $earnedAchievementIds = $user->achievements()
                ->join('achievements', 'achievements.id', '=', 'user_achievements.achievement_id')
                ->selectRaw('achievements.type, achievements.id')
                ->get()
                ->groupBy('type')
                ->map(fn($group) => $group->pluck('id'));

            $earnedSalesIds    = $earnedAchievementIds->get(AchievementType::SALES->value, collect());
            $earnedRevenueIds  = $earnedAchievementIds->get(AchievementType::PRICE->value, collect());

            $now = now()->toDateTimeString();
            $newAchievements = Achievement::query()
                ->where(fn($q) => $q
                    ->where(fn($q) => $q
                        ->where('type', AchievementType::SALES)
                        ->where('threshold', '<=', $totalSales)
                        ->whereNotIn('id', $earnedSalesIds)
                    )
                    ->orWhere(fn($q) => $q
                        ->where('type', AchievementType::PRICE)
                        ->where('threshold', '<=', $totalRevenue)
                        ->whereNotIn('id', $earnedRevenueIds)
                    )
                )
                ->pluck('id')
                ->map(function($id) use ($user, $now) {
                    return [
                        'achievement_id' => $id,
                        'user_id' => $user->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })
                ->all();

            if (empty($newAchievements)) {
                return $this->successResponse('No new achievements unlocked');
            }

            DB::transaction(function () use ($user, $newAchievements) {
                UserAchievement::query()->insert($newAchievements);
                AchievementUnlocked::dispatch($user);
            });

            return $this->successResponse('Achievements unlocked successfully');
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
