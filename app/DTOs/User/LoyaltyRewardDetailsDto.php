<?php

namespace App\DTOs\User;

readonly class LoyaltyRewardDetailsDto
{
    /**
     * @param  string[]  $unlockedAchievements
     * @param  string[]  $nextAvailableAchievements
     */
    public function __construct(
        public array $unlockedAchievements,
        public array $nextAvailableAchievements,
        public ?string $currentBadge,
        public ?string $nextBadge,
        public int $remainingToUnlockNextBadge,
    ) {}

    /**
     * @return array<string, string|null|int|string[]>
     */
    public function toArray(): array
    {
        return [
            'unlocked_achievements' => $this->unlockedAchievements,
            'next_available_achievements' => $this->nextAvailableAchievements,
            'current_badge' => $this->currentBadge,
            'next_badge' => $this->nextBadge,
            'remaining_to_unlock_next_badge' => $this->remainingToUnlockNextBadge,
        ];
    }
}
