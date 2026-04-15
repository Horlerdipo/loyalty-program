<?php

namespace App\Actions\User;

use App\Actions\BaseAction;
use App\DTOs\BaseResponseDto;
use App\Events\ItemPurchased;
use App\Models\User;
use Illuminate\Support\Str;

class PurchaseItem extends BaseAction
{
    public function execute(string $userId, float $amount): BaseResponseDto
    {
        try {

            $user = User::query()
                ->where('id', $userId)
                ->first(['id', 'email']);

            if (is_null($user)) {
                return $this->errorResponse('Unknown user', 401);
            }

            $identifier = Str::uuid();
            $purchase = $user->purchases()->create([
                'identifier' => $identifier,
                'email' => $user->email,
                'amount' => $amount,
            ]);

            ItemPurchased::dispatch($purchase);

            return $this->successResponse('Item purchased successfully', [
                'identifier' => $identifier,
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
