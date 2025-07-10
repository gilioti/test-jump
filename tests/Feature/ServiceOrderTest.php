<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('cria uma ordem de serviço com sucesso', function () {
    $user = User::factory()->create();

    $payload = [
        'vehiclePlate' => 'ABC1234',
        'entryDateTime' => now()->toISOString(),
        'exitDateTime' => now()->addHours(2)->toISOString(),
        'priceType' => 'hora',
        'price' => 100.50,
        'userId' => $user->id,
    ];

    $response = $this->postJson('/api/service-orders', $payload);

    $response->assertStatus(200)
             ->assertJson([
                'status' => 'success',
                'message' => 'Ordem criada com sucesso',
                'data' => [
                    'vehiclePlate' => 'ABC1234',
                    'price' => 100.50,
                    'userId' => $user->id,
                ],
             ]);

    $this->assertDatabaseHas('service_orders', [
        'vehiclePlate' => 'ABC1234',
        'userId' => $user->id,
    ]);
});

it('retorna erro de validação ao enviar dados inválidos', function () {
    $payload = [
        'vehiclePlate' => '123',
        'entryDateTime' => 'not-a-date',
        'price' => 'free',
        'userId' => 9999,
    ];

    $response = $this->postJson('/api/service-orders', $payload);

    $response->assertStatus(422)
             ->assertJsonStructure([
                'status',
                'errors' => ['vehiclePlate', 'entryDateTime', 'price', 'userId'],
             ])
             ->assertJsonFragment([
                'status' => 'error',
             ]);
});
