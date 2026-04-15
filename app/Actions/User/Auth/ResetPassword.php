<?php

namespace App\Actions\User\Auth;

use App\Actions\BaseAction;
use App\DTOs\BaseResponseDto;
use App\Enums\OtpPurpose;
use App\Models\User;
use Horlerdipo\SimpleOtp\Facades\SimpleOtp;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends BaseAction
{
    public function execute(string $email, string $password, string $otp): BaseResponseDto
    {
        try {

            $user = User::query()->where('email', $email)->first();
            if (is_null($user)) {
                return $this->errorResponse('User does not exist', 400);
            }

            $response = SimpleOtp::verify($email, OtpPurpose::FORGOT_PASSWORD->value, $otp);

            if (! $response->status) {
                return $this->errorResponse("Invalid OTP:$response->message", 400);
            }

            $user->update([
                'password' => Hash::make($password),
            ]);

            return $this->successResponse('Otp verified successfully');
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
