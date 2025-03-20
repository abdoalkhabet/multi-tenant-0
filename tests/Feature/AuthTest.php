<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Tenant;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    // إنشاء أو التأكد من وجود Personal Access Client
    Client::firstOrCreate(
        ['personal_access_client' => true], // البحث بناءً على personal_access_client
        [
            'name' => 'Personal Access Client',
            'secret' => Str::random(40),
            'redirect' => 'http://localhost:8000',
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
        ]
    );
});

test('user can register successfully', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'tenant_name' => 'Test Tenant',
        'owner_name' => 'Owner Name',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email', 'tenant_id'],
            'token'
        ]);
});

test('user cannot register with duplicate email', function () {
    $tenant = Tenant::factory()->create();

    User::factory()->create([
        'email' => 'test@example.com',
        'tenant_id' => $tenant->id,
    ]);

    $response = $this->postJson('/api/register', [
        'name' => 'Another User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'tenant_name' => 'Another Tenant',
        'owner_name' => 'Another Owner',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('user can login with correct credentials', function () {
    $tenant = Tenant::factory()->create();

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
        'tenant_id' => $tenant->id,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email', 'tenant_id'],
            'token'
        ]);
});

test('user cannot login with incorrect credentials', function () {
    $tenant = Tenant::factory()->create();

    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
        'tenant_id' => $tenant->id,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson(['error' => 'Invalid credentials']);
});

test('user can logout successfully', function () {
    $tenant = Tenant::factory()->create();

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
        'tenant_id' => $tenant->id,
    ]);

    $token = $user->createToken('authToken')->accessToken;

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token"
    ])->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Logout successful']);
});
