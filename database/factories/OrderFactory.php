<?php

namespace Database\Factories;

use App\Models\order;

use App\Models\product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['tenant_id' => $user->tenant_id]);

        return [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'tenant_id' => $user->tenant_id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'total_price' => $product->price * $this->faker->numberBetween(1, 5),
            'status' => 'pending',
        ];
    }
    // public function definition(): array
    // {
    //     $user = User::factory()->create();
    //     return [
    //         'product_id' => product::factory()->create(['tenant_id' => $user->tenant_id])->id,
    //         'user_id' => $user->id,
    //         'tenant_id' => $user->tenant_id, // التأكد أن tenant_id غير فارغ
    //         'quantity' => $this->faker->numberBetween(1, 5),
    //         'total_price' => function (array $attributes) {
    //             return product::find($attributes['product_id'])->price * $attributes['quantity'];
    //         },
    //         'status' => 'pending',
    //     ];
    // }
}
