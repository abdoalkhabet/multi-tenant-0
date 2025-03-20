<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Tenant;
use Laravel\Passport\Passport;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    // إنشاء Personal Access Client
    Client::create([
        'id' => 1,
        'name' => 'Personal Access Client',
        'secret' => Str::random(40),
        'redirect' => 'http://localhost:8000',
        'personal_access_client' => true,
        'password_client' => false,
        'revoked' => false,
    ]);

    $this->tenant = Tenant::factory()->create();
    $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Passport::actingAs($this->user);
});

it('authenticated user can list products', function () {
    product::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/products');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'products');
});

it('authenticated user can create a product', function () {
    $data = [
        'name' => 'Test Product',
        'description' => 'This is a test product',
        'price' => 99.99,
        'stock_quantity' => 10,
    ];

    $response = $this->postJson('/api/products', $data);

    $response->assertStatus(201)
        ->assertJsonFragment(['message' => 'Product added successfully']);
});

it('authenticated user can update a product', function () {
    $product = product::factory()->create(['tenant_id' => $this->tenant->id]);

    $updatedData = ['name' => 'Updated Product Name'];

    $response = $this->putJson("/api/products/{$product->id}", $updatedData);

    $response->assertStatus(200)
        ->assertJsonFragment(['message' => 'Product updated successfully']);
});

it('authenticated user can delete a product', function () {
    $product = product::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->deleteJson("/api/products/{$product->id}");

    $response->assertStatus(200)
        ->assertJsonFragment(['message' => 'Product deleted successfully']);
});
