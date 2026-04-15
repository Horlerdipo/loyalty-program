<?php

namespace Database\Seeders;

use App\Enums\AchievementType;
use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allAchievements = Achievement::all();
        $achievements = [
            // Sales-based achievements (15) and the threshold = number of items sold
            ['name' => 'First Sale',          'type' => AchievementType::SALES, 'threshold' => 1],
            ['name' => 'Getting Started',     'type' => AchievementType::SALES, 'threshold' => 5],
            ['name' => 'Ten Down',            'type' => AchievementType::SALES, 'threshold' => 10],
            ['name' => 'Quarter Century',     'type' => AchievementType::SALES, 'threshold' => 25],
            ['name' => 'Half Century',        'type' => AchievementType::SALES, 'threshold' => 50],
            ['name' => 'Hustler',             'type' => AchievementType::SALES, 'threshold' => 75],
            ['name' => 'Triple Digits',       'type' => AchievementType::SALES, 'threshold' => 100],
            ['name' => 'Rising Seller',       'type' => AchievementType::SALES, 'threshold' => 150],
            ['name' => 'Double Century',      'type' => AchievementType::SALES, 'threshold' => 200],
            ['name' => 'Power Seller',        'type' => AchievementType::SALES, 'threshold' => 300],
            ['name' => 'Elite Seller',        'type' => AchievementType::SALES, 'threshold' => 400],
            ['name' => 'High Roller',         'type' => AchievementType::SALES, 'threshold' => 500],
            ['name' => 'Market Dominator',    'type' => AchievementType::SALES, 'threshold' => 750],
            ['name' => 'Four Figure Club',    'type' => AchievementType::SALES, 'threshold' => 1000],
            ['name' => 'Legendary Seller',    'type' => AchievementType::SALES, 'threshold' => 1500],

            // Price-based achievements (15) and threshold = total revenue in Naira
            ['name' => 'First Thousand',      'type' => AchievementType::PRICE, 'threshold' => 1000],
            ['name' => 'Five Grand',          'type' => AchievementType::PRICE, 'threshold' => 5000],
            ['name' => 'Ten Grand',           'type' => AchievementType::PRICE, 'threshold' => 10000],
            ['name' => 'Twenty-Five Grand',   'type' => AchievementType::PRICE, 'threshold' => 25000],
            ['name' => 'Fifty Grand',         'type' => AchievementType::PRICE, 'threshold' => 50000],
            ['name' => 'Six Figure Start',    'type' => AchievementType::PRICE, 'threshold' => 100000],
            ['name' => 'Two Hundred K',       'type' => AchievementType::PRICE, 'threshold' => 200000],
            ['name' => 'Three Hundred K',     'type' => AchievementType::PRICE, 'threshold' => 300000],
            ['name' => 'Half a Million',      'type' => AchievementType::PRICE, 'threshold' => 500000],
            ['name' => 'Three Quarters',      'type' => AchievementType::PRICE, 'threshold' => 750000],
            ['name' => 'One Million Naira',   'type' => AchievementType::PRICE, 'threshold' => 1000000],
            ['name' => 'Two Million Naira',   'type' => AchievementType::PRICE, 'threshold' => 2000000],
            ['name' => 'Five Million Naira',  'type' => AchievementType::PRICE, 'threshold' => 5000000],
            ['name' => 'Seven-Five Million',  'type' => AchievementType::PRICE, 'threshold' => 7500000],
            ['name' => 'Ten Million Naira',   'type' => AchievementType::PRICE, 'threshold' => 10000000],
        ];

        $achievements = collect($achievements)->reject(function ($badge) use ($allAchievements) {
            return $allAchievements->contains('name', $badge['name']);
        })->toArray();

        DB::table('achievements')->insert($achievements);
    }
}
