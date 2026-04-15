<?php

namespace App\Models;

use App\Enums\AchievementType;
use Database\Factories\AchievementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    /** @use HasFactory<AchievementFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'threshold',
    ];

    protected $casts = [
        'type' => AchievementType::class,
    ];
}
