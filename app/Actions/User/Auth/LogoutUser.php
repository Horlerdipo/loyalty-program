<?php

namespace App\Actions\User\Auth;

use App\Actions\BaseAction;
use App\DTOs\BaseResponseDto;
use App\Models\User;

class LogoutUser extends BaseAction
{
    public function execute(int|string $userId): BaseResponseDto
    {
        try {

            $user = User::query()
                ->where('id', $userId)
                ->first(['id', 'email', 'first_name', 'last_name']);

            if (is_null($user)) {
                return $this->errorResponse('Unknown user', 400);
            }

            $user->tokens()->delete();

            return $this->successResponse('User logout successfully');
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
