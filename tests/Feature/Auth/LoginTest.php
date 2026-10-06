<?php

use App\Models\User;

it('faz login com credenciais válidas e devolve o token', function () {
    // Arrange: um usuário que existe no banco
    $user = User::factory()->create(['password' => 'senha-segura-123']);

    // Act: chama a API
    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'senha-segura-123',
    ]);

    // Assert: confere a resposta
    $response->assertOk()
        ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);
});

it('rejeita login com senha errada', function () {
    $user = User::factory()->create(['password' => 'senha-segura-123']);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'senha-errada',
    ])->assertUnprocessable();   // ou assertUnauthorized(), conforme o que você escolher abaixo
});

it('exige email e senha', function () {
    $this->postJson('/api/login', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});
