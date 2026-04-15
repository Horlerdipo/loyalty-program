<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);
beforeEach(function () {
    $this->password = 'password';
    $this->user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);
});

it('logins a user in successfully', function () {
    // Arrange:
    $data = [
        'email' => $this->user->email,
        'password' => $this->password,
    ];

    // Act:
    $response = $this->postJson('/api/v1/auth/login', $data);

    // Assert:
    expect($response)->assertStatus(200);
    expect($response->json()['auth_details']['token'])->not->toBeNull();
});

it('returns an error when the password is invalid', function () {
    // Arrange:
    $data = [
        'email' => $this->user->email,
        'password' => 'Wrong password',
    ];

    // Act:
    $response = $this->postJson('/api/v1/auth/login', $data);

    // Assert:
    expect($response)->assertStatus(400);
    expect($response->json()['message'])->toBe('The provided credentials are incorrect.');
});

it('returns an error when the email is invalid', function () {
    // Arrange:
    $data = [
        'email' => 'umar@example.com',
        'password' => $this->password,
    ];

    // Act:
    $response = $this->postJson('/api/v1/auth/login', $data);

    // Assert:
    expect($response)->assertJsonValidationErrors(['email']);
});

it('prevents logging in with invalid credentials', function () {

    // Arrange:
    $data = [
        'email' => 'not-a-valid-email',
    ];

    // Act:
    $response = $this->postJson('/api/v1/auth/login', $data);

    // Assert:
    expect($response)->assertJsonValidationErrors(['password', 'email']);
});
