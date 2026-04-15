<?php

namespace App\Actions\User;

use App\Actions\BaseAction;
use App\DTOs\BaseResponseDto;
use App\DTOs\ObjectResponseDto;
use App\DTOs\User\LoyaltyRewardDetailsDto;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;

class FetchLoyaltyRewardDetails extends BaseAction
{
    /**
     * @return ObjectResponseDto<LoyaltyRewardDetailsDto>|BaseResponseDto
     */
    public function execute(string $userId): ObjectResponseDto|BaseResponseDto
    {
        try {

            $user = User::query()
                ->where('id', $userId)
                ->first();

            if (is_null($user)) {
                return $this->errorResponse('User not found', 400);
            }

            $earnedUserAchievements = $user->achievements()->with('achievement')->get();
            $earnedCount = $earnedUserAchievements->count();
            $earnedAchievementIds = $earnedUserAchievements->pluck('achievement_id');

            $nextAvailableAchievements = Achievement::query()
                ->whereNotIn('id', $earnedAchievementIds)
                ->orderBy('threshold')
                ->pluck('name')
                ->toArray();

            $earnedBadgeIds = $user->badges()->pluck('badge_id');
            $allBadges = Badge::query()->orderBy('required_count')->get();

            $currentBadge = $allBadges
                ->filter(fn ($badge) => $earnedBadgeIds->contains($badge->id))
                ->last();

            $nextBadge = $allBadges
                ->filter(fn ($badge) => $badge->required_count > $earnedCount)
                ->first();

            return $this->objectResponse(
                status: true,
                message: 'Loyalty progress fetched successfully',
                data: new LoyaltyRewardDetailsDto(
                    unlockedAchievements: $earnedUserAchievements->pluck('achievement.name')->toArray(),
                    nextAvailableAchievements: $nextAvailableAchievements,
                    currentBadge: $currentBadge?->name->value,
                    nextBadge: $nextBadge?->name->value,
                    remainingToUnlockNextBadge: $nextBadge ? $nextBadge->required_count - $earnedCount : 0
                )
            );
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
