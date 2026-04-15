<?php

use App\Actions\User\UnlockBadges;
use App\Enums\BadgeName;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Database\Seeders\AchievementSeeder;
use Database\Seeders\BadgeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->seed([AchievementSeeder::class, BadgeSeeder::class]);
    $this->action = new UnlockBadges;
    $this->user = User::factory()->create();
    Config::set('app.loyalty_program.cashback_paid_per_badge', 5.00);
    Event::fake();
});

function giveAchievements(User $user, int $count): void
{
    $achievements = Achievement::query()->take($count)->pluck('id');
    $input = [];
    $now = now()->toDateTimeString();
    foreach ($achievements as $achievement) {
        $input[] = [
            'user_id' => $user->id,
            'achievement_id' => $achievement,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
    $user->achievements()->createMany($input);
}

describe('no new badges to unlock', function () {
    it('returns early when user has zero achievements', function () {
        $response = $this->action->execute($this->user);

        expect($response->status)->toBeTrue()
            ->and($response->message)->toBe('No new badges unlocked');

        $this->assertDatabaseCount('user_badges', 0);
    });

    it('returns early when all qualifying badges are already earned', function () {
        // 3 achievements qualifies the user for Starter (>=1) and Bronze (>=3)
        giveAchievements($this->user, 3);

        $starterBadge = Badge::query()->where('name', BadgeName::Starter->value)->first();
        $bronzeBadge = Badge::query()->where('name', BadgeName::Bronze->value)->first();

        UserBadge::query()->insert([
            [
                'user_id' => $this->user->id,
                'badge_id' => $starterBadge->id,
                'cashback_paid' => true,
                'cashback_amount' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $this->user->id,
                'badge_id' => $bronzeBadge->id,
                'cashback_paid' => true,
                'cashback_amount' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->action->execute($this->user);

        expect($response->status)->toBeTrue()
            ->and($response->message)->toBe('No new badges unlocked');

        // Count should still be 2 because nothing new inserted
        $this->assertDatabaseCount('user_badges', 2);
    });
});

describe('unlocking badges', function () {
    it('unlocks only Starter badge when user has exactly 1 achievement', function () {
        giveAchievements($this->user, 1);

        $response = $this->action->execute($this->user);

        expect($response->status)->toBeTrue()
            ->and($response->message)->toBe('Badges unlocked successfully');

        $this->assertDatabaseCount('user_badges', 1);
        $this->assertDatabaseHas('user_badges', [
            'user_id' => $this->user->id,
            'badge_id' => Badge::query()->where('name', BadgeName::Starter->value)->value('id'),
        ]);
    });

    it('unlocks Starter and Bronze when user has exactly 3 achievements', function () {
        giveAchievements($this->user, 3);

        $this->action->execute($this->user);

        $this->assertDatabaseCount('user_badges', 2);
        $this->assertDatabaseHas('user_badges', [
            'user_id' => $this->user->id,
            'badge_id' => Badge::query()->where('name', BadgeName::Starter->value)->value('id'),
        ]);
        $this->assertDatabaseHas('user_badges', [
            'user_id' => $this->user->id,
            'badge_id' => Badge::query()->where('name', BadgeName::Bronze->value)->value('id'),
        ]);
    });

    it('does not unlock badges above the achievement threshold', function () {
        // this qualifies user for Starter and Bronze only
        giveAchievements($this->user, 3);

        $this->action->execute($this->user);

        expect(
            UserBadge::query()
                ->where('user_id', $this->user->id)
                ->whereIn('badge_id', Badge::query()->whereIn('name', [
                    BadgeName::Silver->value,   // this requires 6
                    BadgeName::Gold->value,     // this requires 9
                ])->pluck('id'))
                ->exists()
        )->toBeFalse();
    });

    it('does not re-unlock already earned badges', function () {
        giveAchievements($this->user, 3);

        $starterBadge = Badge::query()->where('name', BadgeName::Starter->value)->first();
        UserBadge::query()->insert([[
            'user_id' => $this->user->id,
            'badge_id' => $starterBadge->id,
            'cashback_paid' => false,
            'cashback_amount' => 5.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]]);

        $this->action->execute($this->user);

        // Only Bronze should be newly inserted cause Starter already existed
        expect(
            UserBadge::query()
                ->where('user_id', $this->user->id)
                ->where('badge_id', $starterBadge->id)
                ->count()
        )->toBe(1);

        $this->assertDatabaseCount('user_badges', 2); // both Bronze and Starter should exist in the db
    });
});

describe('cashback', function () {
    it('sets cashback_paid to false on all new user badges', function () {
        giveAchievements($this->user, 1);

        $this->action->execute($this->user);

        expect(
            UserBadge::query()
                ->where('user_id', $this->user->id)
                ->where('cashback_paid', true)
                ->exists()
        )->toBeFalse();
    });

    it('uses the cashback amount from config', function () {
        Config::set('app.loyalty_program.cashback_paid_per_badge', 12.50);
        giveAchievements($this->user, 1);
        $this->action->execute($this->user);

        $this->assertDatabaseHas('user_badges', [
            'user_id' => $this->user->id,
            'cashback_amount' => 12.50,
        ]);
    });
});

describe('dispatched events', function () {
    it('dispatches a BadgeUnlocked event for each newly unlocked badge', function () {
        giveAchievements($this->user, 3); // unlocks Starter and Bronze so it should dispatch 2 events

        $this->action->execute($this->user);

        Event::assertDispatched(BadgeUnlocked::class, 2);
    });

    it('dispatches BadgeUnlocked with the correct UserBadge', function () {
        giveAchievements($this->user, 1);

        $this->action->execute($this->user);

        $starterBadgeId = Badge::query()->where('name', BadgeName::Starter->value)->value('id');

        Event::assertDispatched(BadgeUnlocked::class,
            fn ($event) => $event->userBadge->badge_id === $starterBadgeId
                && $event->userBadge->user_id === $this->user->id
        );
    });

    it('does not dispatch any event when no new badges are unlocked', function () {
        $this->action->execute($this->user);

        Event::assertNotDispatched(BadgeUnlocked::class);
    });
});
