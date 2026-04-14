<?php

namespace App\Actions\User\Auth;

use App\Actions\BaseAction;
use App\DTOs\BaseResponseDto;
use App\DTOs\User\Auth\RegisterUserDto;
use App\Models\User;

class RegisterUser extends BaseAction
{
    public function execute(RegisterUserDto $data): BaseResponseDto
    {
        try {

            if (User::query()->where('email', $data->email)->exists()) {
                return $this->errorResponse('Email already exists', 422);
            }

            $user = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
            ]);

            return $this->successResponse('User created successfully');
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
