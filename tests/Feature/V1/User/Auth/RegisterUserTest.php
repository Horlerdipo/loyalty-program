<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->payload = [
        'name' => fake()->name(),
        'email' => fake()->email(),
        'password' => fake()->password(12),
    ];
});

it('registers a user successfully', function () {
    // Arrange:
    $payload = [
        ...$this->payload,
        'password_confirmation' => $this->payload['password'],
    ];

    // Act:
    $response = $this->postJson('/api/v1/auth/register', $payload);
    expect($response)->assertStatus(204);

    $this->assertDatabaseHas('users', [
        'name' => $this->payload['name'],
        'email' => $this->payload['email'],
        'email_verified_at' => null,
    ]);
});

it('returns an error when the data is invalid', function () {
    // Arrange:
    $user = [
    ];

    // Act:
    $response = $this->postJson('/api/v1/auth/register', $user);

    // Assert:
    expect($response)->assertJsonValidationErrors(['name', 'email', 'password']);
    $this->assertDatabaseMissing('users', [
        'name' => $this->payload['name'],
        'email' => $this->payload['email'],
    ]);
});

it('prevents registering with an existing email', function () {

    // Arrange:
    User::factory()->create([
        'email' => $this->payload['email'],
    ]);

    $user = [
        ...$this->payload,
        'password_confirmation' => $this->payload['password'],
    ];

    // Act:
    $response = $this->postJson('/api/v1/auth/register', $user);

    // Assert:
    expect($response)->assertJsonValidationErrors(['email']);
});
