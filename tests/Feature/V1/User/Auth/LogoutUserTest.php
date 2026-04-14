<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(\Tests\TestCase::class, RefreshDatabase::class);
beforeEach(function () {
    $this->user = User::factory()->create();
});

it('logs out successfully', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/auth/logout');

    expect($response)->assertStatus(204);
});

it('returns an error when the user is not logged in', function () {
    $response = $this->postJson('/api/v1/auth/logout');
    expect($response)->assertStatus(401);
});
