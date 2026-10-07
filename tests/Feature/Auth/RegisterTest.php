<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('criar registro de usuário', function () {
    $payload = [
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'password' => 'senha-segura-123',
        'password_confirmation' => 'senha-segura-123',
    ];

    $response = $this->postJson('/api/register', $payload);
    
    $response->assertCreated();
    $this->assertDatabaseHas('users', [
        'email' => $payload['email'],
    ]);

    $user = User::where('email', $payload['email'])->first();
    expect(Hash::check('senha-segura-123', $user->password))->toBeTrue();
});

it('recusa email já cadastrado', function () {
    User::factory()->create(['email' => 'maria@example.com']);

    $payload = [
        'name' => 'Outra Maria',
        'email' => 'maria@example.com',
        'password' => 'senha-segura-123',
        'password_confirmation' => 'senha-segura-123',
    ];
    
    $this->postJson('/api/register', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('recusa cadastro com campos obrigatórios vazios', function () {
    $this->postJson('/api/register', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password', 'password_confirmation']);

    $this->assertDatabaseCount('users', 0);
});

it('recusa cadastro com confirmação de senha diferente', function () {
    $payload = [
        'name' => 'João da Silva',
        'email' => 'joao@example.com',
        'password' => 'senha-segura-123',
        'password_confirmation' => 'senha-segura-456',
    ];

    $response = $this->postJson('/api/register', $payload);

    $response->assertUnprocessable();
    $this->assertDatabaseMissing('users', [
        'email' => $payload['email'],
    ]);

    $response->assertUnprocessable()
    ->assertJsonValidationErrors(['password']);

});

it('recusa cadastro com senha fraca', function () {
    $payload = [
        'name' => 'João da Silva',
        'email' => 'joao@example.com',
        'password' => '123',
        'password_confirmation' => '123',
    ];

    $response = $this->postJson('/api/register', $payload);

    $response->assertUnprocessable();
    $this->assertDatabaseMissing('users', [
        'email' => $payload['email'],
    ]);

    $response->assertUnprocessable()
    ->assertJsonValidationErrors(['password']);

});

it('recusa cadastro com email inválido', function () {
    $payload = [
        'name' => 'João da Silva',
        'email' => 'J@example.com',
        'password' => 'senha-segura-123',
        'password_confirmation' => 'senha-segura-123',
    ];

    $response = $this->postJson('/api/register', $payload);

    $response->assertUnprocessable();
    $this->assertDatabaseMissing('users', [
        'email' => $payload['email'],
    ]);

    $response->assertUnprocessable()
    ->assertJsonValidationErrors(['email']);

});

it('recusa cadastro com campo desconhecido', function () {
    $payload = [
        'name' => 'João da Silva',
        'email' => 'joao@example.com',
        'password' => 'senha-segura-123',
        'password_confirmation' => 'senha-segura-123',
        'unknown_field' => 'value',
    ];

    $response = $this->postJson('/api/register', $payload);

    $response->assertUnprocessable();
    $this->assertDatabaseMissing('users', [
        'email' => $payload['email'],
    ]);

    $response->assertUnprocessable()
    ->assertJsonValidationErrors(['unknown_field']);

});

it('recusa cadastro com email inválido 2', function () {
    $payload = [
        'name' => 'João da Silva',
        'email' => 'isso-nao-e-um-email',
        'password' => 'senha-segura-123',
        'password_confirmation' => 'senha-segura-123',
    ];

    $this->postJson('/api/register', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);

    $this->assertDatabaseMissing('users', ['name' => 'João da Silva']);
});
