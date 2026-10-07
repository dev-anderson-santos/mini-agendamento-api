<?php

use App\Models\User;

it('revoga o token ao fazer logout', function () {
    // Arrange: Um usuário com token real
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    // Act: chama a API de logout com o token
    $response = $this->withToken($token)->postJson('/api/logout');

    // Assert: respondeu com sucesso e o token foi revogado
    $response->assertOk();
    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('não aceita o token depois do logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    // Logout
    $this->withToken($token)->postJson('/api/logout')->assertOk();

    $this->app['auth']->forgetGuards();
    // Tenta acessar um endpoint protegido com o mesmo token
    $this->withToken($token)->getJson('/api/user')
        ->assertUnauthorized();
});