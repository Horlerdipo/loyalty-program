<?php

namespace App\Models;

use Database\Factories\PurchaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperPurchase
 */
class Purchase extends Model
{
    /** @use HasFactory<PurchaseFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'identifier',
        'email',
        'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
