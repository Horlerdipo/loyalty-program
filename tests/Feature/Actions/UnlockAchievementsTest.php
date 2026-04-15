<?php

use App\Actions\User\UnlockAchievements;
use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Database\Seeders\AchievementSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->seed([AchievementSeeder::class]);
    $this->user = User::factory()->create();
    Event::fake();
});

describe('Sales Achievement Test', function () {

    it('successfully unlocks new sales based achievements, dispatches event and return success', function () {
        foreach (AchievementSeeder::$salesAchievements as $salesAchievement) {
            Event::fake();
            $response = (new UnlockAchievements())->execute($this->user, $salesAchievement['threshold'], 0);

            expect($response->status)->toBeTrue();
            $achievement = Achievement::query()
                ->where('name', $salesAchievement['name'])
                ->firstOrFail();
            $this->assertDatabaseHas('user_achievements', [
                'achievement_id' => $achievement->id,
                'user_id' => $this->user->id
            ]);
            Event::assertDispatchedOnce(AchievementUnlocked::class);
        }
    });

    it('returns success without dispatching event when there are no more achievements to earn', function () {
        //give all achievements to user
        foreach (AchievementSeeder::$salesAchievements as $salesAchievement) {
            $achievement = Achievement::query()
                ->where('name', $salesAchievement['name'])
                ->firstOrFail();

            UserAchievement::query()
                ->create([
                    'achievement_id' => $achievement->id,
                    'user_id' => $this->user->id
                ]);
        }

        //attempt to earn achievement
        foreach (AchievementSeeder::$salesAchievements as $salesAchievement) {
            Event::fake();
            $response = (new UnlockAchievements())->execute($this->user, $salesAchievement['threshold'], 0);

            expect($response->status)->toBeTrue()
                ->and($response->message)->toBe('No new achievements unlocked');

            Event::assertNotDispatched(AchievementUnlocked::class);
        }
    });

    it('returns success without dispatching event when the sale does not qualify for ', function () {
        $achievement = Achievement::query()
            ->where('name', AchievementSeeder::$salesAchievements[0]['name'])
            ->firstOrFail();

        UserAchievement::query()
            ->create([
                'achievement_id' => $achievement->id,
                'user_id' => $this->user->id
            ]);

        $totalSales = 4; //this is between First Sale and Getting Started so it should not trigger any achievement unlocking
        $response = (new UnlockAchievements())->execute($this->user, $totalSales, 0);

        expect($response->status)->toBeTrue();
        Event::assertNotDispatched(AchievementUnlocked::class);
    });

    it('records all the previous achievements including the last achievements for a user when it gets the highest achievement', function () {
        $highestAchievement = last(AchievementSeeder::$salesAchievements);
        $response = (new UnlockAchievements())->execute($this->user, $highestAchievement['threshold'], 0);
        expect($response->status)->toBeTrue();
        Event::assertDispatchedOnce(AchievementUnlocked::class);

        foreach (AchievementSeeder::$salesAchievements as $salesAchievement) {
            $achievement = Achievement::query()
                ->where('name', $salesAchievement['name'])
                ->firstOrFail();

            $this->assertDatabaseHas('user_achievements', [
                'achievement_id' => $achievement->id,
                'user_id' => $this->user->id
            ]);
        }
    });
});

describe('Price Achievement Test', function () {

    it('successfully unlocks new sales based achievements, dispatches event and return success', function () {
        foreach (AchievementSeeder::$priceAchievements as $priceAchievement) {
            Event::fake();
            $response = (new UnlockAchievements())->execute($this->user, 0, $priceAchievement['threshold']);

            expect($response->status)->toBeTrue();
            $achievement = Achievement::query()
                ->where('name', $priceAchievement['name'])
                ->firstOrFail();
            $this->assertDatabaseHas('user_achievements', [
                'achievement_id' => $achievement->id,
                'user_id' => $this->user->id
            ]);
            Event::assertDispatchedOnce(AchievementUnlocked::class);
        }
    });

    it('returns success without dispatching event when there are no more achievements to earn', function () {
        //give all achievements to user
        foreach (AchievementSeeder::$priceAchievements as $priceAchievement) {
            $achievement = Achievement::query()
                ->where('name', $priceAchievement['name'])
                ->firstOrFail();

            UserAchievement::query()
                ->create([
                    'achievement_id' => $achievement->id,
                    'user_id' => $this->user->id
                ]);
        }

        //attempt to earn achievement
        foreach (AchievementSeeder::$priceAchievements as $priceAchievement) {
            Event::fake();
            $response = (new UnlockAchievements())->execute($this->user, 0, $priceAchievement['threshold']);

            expect($response->status)->toBeTrue()
                ->and($response->message)->toBe('No new achievements unlocked');

            Event::assertNotDispatched(AchievementUnlocked::class);
        }
    });

    it('returns success without dispatching event when the sale does not qualify for ', function () {
        $achievement = Achievement::query()
            ->where('name', AchievementSeeder::$priceAchievements[0]['name'])
            ->firstOrFail();

        UserAchievement::query()
            ->create([
                'achievement_id' => $achievement->id,
                'user_id' => $this->user->id
            ]);

        $totalRevenue = 4000; //this is between First Sale and Getting Started so it should not trigger any achievement unlocking
        $response = (new UnlockAchievements())->execute($this->user, 0, $totalRevenue);

        expect($response->status)->toBeTrue();
        Event::assertNotDispatched(AchievementUnlocked::class);
    });

    it('records all the previous achievements including the last achievements for a user when it gets the highest achievement', function () {
        $highestAchievement = last(AchievementSeeder::$priceAchievements);
        $response = (new UnlockAchievements())->execute($this->user, 0, $highestAchievement['threshold']);
        expect($response->status)->toBeTrue();
        Event::assertDispatchedOnce(AchievementUnlocked::class);

        foreach (AchievementSeeder::$priceAchievements as $priceAchievement) {
            $achievement = Achievement::query()
                ->where('name', $priceAchievement['name'])
                ->firstOrFail();

            $this->assertDatabaseHas('user_achievements', [
                'achievement_id' => $achievement->id,
                'user_id' => $this->user->id
            ]);
        }
    });
});

