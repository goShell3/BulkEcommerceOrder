<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id,
            'name' => fake()->words(3, true),
            'slug' => fn (array $attributes) => str()->slug($attributes['name']),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 50, 500),
            'stock' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
} 