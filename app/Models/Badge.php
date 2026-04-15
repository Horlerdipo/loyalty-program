<?php

namespace App\Models;

use App\Enums\BadgeName;
use Database\Factories\BadgeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    /** @use HasFactory<BadgeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'required_count',
        'order',
    ];

    protected $casts = [
        'name' => BadgeName::class,
    ];
}
