<?php

namespace App\Actions\User\Auth;

use App\Actions\BaseAction;
use App\DTOs\BaseResponseDto;
use App\DTOs\User\Auth\LoginUserDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginUser extends BaseAction
{
    public function execute(LoginUserDto $data, int $tokenTtl = 20): BaseResponseDto
    {
        try {

            $user = User::query()
                ->where('email', $data->email)
                ->first();

            if (is_null($user)) {
                return $this->errorResponse('The provided credentials are incorrect.', 400);
            }

            if (! Hash::check($data->password, $user->password)) {
                return $this->errorResponse('The provided credentials are incorrect.', 400);
            }

            $days = now()->addDays($tokenTtl);
            $token = $user->createToken(name: 'auth_token', expiresAt: $days);

            return $this->successResponse('User created successfully', [
                'user' => $user->toArray(),
                'auth_details' => [
                    'token' => $token->plainTextToken,
                    'ttl' => $days->toDateTimeString(),
                ],
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
