<?php

namespace App\Actions\User\Auth;

use App\Actions\BaseAction;
use App\DTOs\BaseResponseDto;
use App\Enums\OtpPurpose;
use App\Models\User;
use Horlerdipo\SimpleOtp\Facades\SimpleOtp;

class SendForgotPasswordToken extends BaseAction
{
    public function execute(string $email): BaseResponseDto
    {
        try {

            $user = User::query()->where('email', $email)->first();
            if (is_null($user)) {
                return $this->errorResponse('User does not exist', 400);
            }

            SimpleOtp::send($email, OtpPurpose::FORGOT_PASSWORD->value);

            return $this->successResponse('Otp sent successfully');
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
