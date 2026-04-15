<?php

use App\Enums\OtpPurpose;
use App\Models\User;
use Horlerdipo\SimpleOtp\DTOs\VerifyOtpResponse;
use Horlerdipo\SimpleOtp\Enums\ChannelType;
use Horlerdipo\SimpleOtp\Facades\SimpleOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);
beforeEach(function () {
    $this->user = User::factory()->create();
    Config::set(['otp.default_channel' => ChannelType::BLACKHOLE]);
});

describe('Forgot password', function () {
    it('initiates forgot password successfully', function () {
        // Arrange:
        SimpleOtp::expects('send')
            ->zeroOrMoreTimes()
            ->withAnyArgs()
            ->andReturn();

        $data = [
            'email' => $this->user->email,
        ];

        // Act:
        $response = $this->postJson('/api/v1/auth/password/forgot', $data);
        expect($response)->assertStatus(204);
    });

    it('returns an error when the user does not exist', function () {
        // Arrange:
        $data = [
            'email' => 'umar@umar.com',
        ];

        // Act:
        $response = $this->postJson('/api/v1/auth/password/forgot', $data);
        expect($response)->assertJsonValidationErrors(['email']);
    });

    it('returns an error when the email is not an email', function () {
        // Arrange:
        $data = [
            'email' => 'umarumar.com',
        ];

        // Act:
        $response = $this->postJson('/api/v1/auth/password/forgot', $data);
        expect($response)->assertJsonValidationErrors(['email']);
    });
});

describe('Reset password', function () {
    it('returns error when OTP is wrong', function () {
        SimpleOtp::expects('verify')
            ->with($this->user->email, OtpPurpose::FORGOT_PASSWORD->value, 'wrong-thing')
            ->andReturn(new VerifyOtpResponse(status: false, message: 'This OTP is incorrect'));

        $response = $this->postJson('/api/v1/auth/password/reset', [
            'email' => $this->user->email,
            'token' => 'wrong-thing',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(400);
        expect($response->json()['message'])->toBe('Invalid OTP:This OTP is incorrect');
    });

    it('resets password successfully', function () {
        SimpleOtp::expects('verify')
            ->with($this->user->email, OtpPurpose::FORGOT_PASSWORD->value, '123456')
            ->andReturn(new VerifyOtpResponse(status: true, message: 'Password Reset!'));

        $response = $this->postJson('/api/v1/auth/password/reset', [
            'email' => $this->user->email,
            'token' => '123456',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(204);
        $this->user->refresh();

        expect(Hash::check('new-password', $this->user->password))->toBeTrue()
            ->and(Hash::check('password', $this->user->password))->toBeFalse();
    });
});
