<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'total' => 0, // Will be calculated after items are added
            'location' => fake()->address(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fn (array $attributes) => $attributes['created_at'],
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            // Update the total based on order items
            $order->update([
                'total' => $order->items->sum(function ($item) {
                    return $item->price * $item->quantity;
                })
            ]);
        });
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the order is processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }

    /**
     * Indicate that the order is shipped.
     */
    public function shipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'shipped',
        ]);
    }

    /**
     * Indicate that the order is delivered.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the order was created recently.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'updated_at' => fn (array $attributes) => $attributes['created_at'],
        ]);
    }

    /**
     * Indicate that the order is old (more than 30 days).
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-31 days'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', '-31 days'),
        ]);
    }

    /**
     * Indicate that the order has a high value (over $500).
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'total' => $this->faker->randomFloat(2, 500, 2000),
        ]);
    }

    /**
     * Indicate that the order has a low value (under $100).
     */
    public function lowValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'total' => $this->faker->randomFloat(2, 10, 100),
        ]);
    }

    /**
     * Indicate that the order is from a specific country.
     */
    public function fromCountry(string $country): static
    {
        return $this->state(fn (array $attributes) => [
            'shipping_address' => array_merge($attributes['shipping_address'], [
                'country' => $country,
            ]),
        ]);
    }

    /**
     * Indicate that the order is from the United States.
     */
    public function fromUS(): static
    {
        return $this->fromCountry('United States');
    }

    /**
     * Indicate that the order is from Canada.
     */
    public function fromCanada(): static
    {
        return $this->fromCountry('Canada');
    }

    /**
     * Indicate that the order is from the United Kingdom.
     */
    public function fromUK(): static
    {
        return $this->fromCountry('United Kingdom');
    }

    /**
     * Indicate that the order is from a specific state in the US.
     */
    public function fromUSState(string $state): static
    {
        return $this->state(fn (array $attributes) => [
            'shipping_address' => array_merge($attributes['shipping_address'], [
                'country' => 'United States',
                'state' => $state,
            ]),
        ]);
    }

    /**
     * Indicate that the order is from California.
     */
    public function fromCalifornia(): static
    {
        return $this->fromUSState('California');
    }

    /**
     * Indicate that the order is from New York.
     */
    public function fromNewYork(): static
    {
        return $this->fromUSState('New York');
    }

    /**
     * Indicate that the order is from Texas.
     */
    public function fromTexas(): static
    {
        return $this->fromUSState('Texas');
    }

    /**
     * Indicate that the order is from a specific city.
     */
    public function fromCity(string $city): static
    {
        return $this->state(fn (array $attributes) => [
            'shipping_address' => array_merge($attributes['shipping_address'], [
                'city' => $city,
            ]),
        ]);
    }

    /**
     * Indicate that the order is from Los Angeles.
     */
    public function fromLosAngeles(): static
    {
        return $this->fromCity('Los Angeles');
    }

    /**
     * Indicate that the order is from New York City.
     */
    public function fromNewYorkCity(): static
    {
        return $this->fromCity('New York');
    }

    /**
     * Indicate that the order is from Chicago.
     */
    public function fromChicago(): static
    {
        return $this->fromCity('Chicago');
    }
} 