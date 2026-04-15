<?php

use App\Events\ItemPurchased;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->amount = 20_000;
    Event::fake();
});

it('purchases an item successfully', function () {

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/purchase', [
            'amount' => $this->amount,
        ]);

    expect($response)->assertStatus(200)
        ->and($response->json()['identifier'])->toBeString();

    $this->assertDatabaseHas('purchases', [
        'identifier' => $response->json()['identifier'],
        'email' => $this->user->email,
        'amount' => $this->amount,
    ]);

    Event::assertDispatched(ItemPurchased::class);
});

it('returns unauthenticated error when no user is attached', function () {
    $response = $this->postJson('/api/v1/purchase', [
        'amount' => $this->amount,
    ]);
    expect($response)->assertStatus(401);
});

it('returns validation error when no amount is provided', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/purchase');

    expect($response)->assertJsonValidationErrors(['amount']);
    $this->assertDatabaseMissing('purchases', [
        'email' => $this->user->email,
        'amount' => $this->amount,
    ]);
});

it('returns validation error when no amount is not numeric', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/purchase', [
            'amount' => 'string',
        ]);

    expect($response)->assertJsonValidationErrors(['amount']);
    $this->assertDatabaseMissing('purchases', [
        'email' => $this->user->email,
        'amount' => $this->amount,
    ]);
});

it('returns validation error when no amount is less than 1', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/purchase', [
            'amount' => 0.116,
        ]);

    expect($response)->assertJsonValidationErrors(['amount']);
    $this->assertDatabaseMissing('purchases', [
        'email' => $this->user->email,
        'amount' => $this->amount,
    ]);
});
