<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Laravel\Passport\Client;
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

    $this->user = User::factory()->create();
    Passport::actingAs($this->user);
});

test('user can view all orders', function () {
    Order::factory()->count(3)->create([
        'tenant_id' => $this->user->tenant_id,
        'user_id' => $this->user->id
    ]);

    $response = $this->getJson('/api/orders');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'tenant_name',
            'orders' => [
                '*' => ['id', 'user_id', 'tenant_id', 'product', 'quantity', 'total_price', 'status', 'created_at']
            ]
        ]);
});

test('user can place an order', function () {
    $product = product::factory()->create([
        'tenant_id' => $this->user->tenant_id,
        'stock_quantity' => 10
    ]);

    $response = $this->postJson('/api/orders', [
        'product_id' => $product->id,
        'quantity' => 2
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'order' => ['id', 'product_id', 'user_id', 'quantity', 'total_price', 'status', 'tenant_id']
        ]);
});

test('user cannot order a product with insufficient stock', function () {
    $product = product::factory()->create([
        'tenant_id' => $this->user->tenant_id,
        'stock_quantity' => 1
    ]);

    $response = $this->postJson('/api/orders', [
        'product_id' => $product->id,
        'quantity' => 5
    ]);

    $response->assertStatus(400)
        ->assertJson(['error' => 'Insufficient stock']);
});

test('user can cancel an order', function () {
    $order = Order::factory()->create([
        'tenant_id' => $this->user->tenant_id,
        'user_id' => $this->user->id
    ]);

    $response = $this->deleteJson("/api/orders/{$order->id}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Order canceled successfully']);
});

test('user cannot cancel a non-existent order', function () {
    $response = $this->deleteJson('/api/orders/999');

    $response->assertStatus(404)
        ->assertJson(['error' => 'Order not found']);
});
