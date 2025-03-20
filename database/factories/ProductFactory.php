<?php

namespace Database\Factories;

use App\Models\tenant;
use App\Models\product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\product>
 */
class ProductFactory extends Factory
{
    protected $model = product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
            'tenant_id' => Tenant::factory(),
        ];
    }
    // public function definition(): array
    // {
    //     return [
    //         'name' => $this->faker->word,
    //         'description' => $this->faker->sentence,
    //         'price' => $this->faker->randomFloat(2, 10, 1000),
    //         'stock_quantity' => $this->faker->numberBetween(1, 100),
    //         // 'tenant_id' => User::factory()->create()->tenant_id,
    //         'tenant_id' => tenant::factory(),
    //     ];
    // }
}
