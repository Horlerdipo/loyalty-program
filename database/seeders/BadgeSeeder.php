<?php

namespace Database\Seeders;

use App\Enums\BadgeName;
use App\Models\Badge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allBadges = Badge::all();
        $badges = array_map(fn (BadgeName $badge) => [
            'name' => $badge->value,
            'required_count' => $badge->requiredCount(),
            'order' => $badge->order(),
        ], BadgeName::cases());

        $badges = collect($badges)->reject(function ($badge) use ($allBadges) {
            return $allBadges->contains('name', $badge['name']);
        })->toArray();

        DB::table('badges')->insert($badges);
    }
}
