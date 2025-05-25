<?php

namespace Database\Factories;

use App\Models\ReturnRequest;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReturnRequest>
 */
class ReturnRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReturnRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reason' => fake()->randomElement(['damaged', 'wrong_item', 'not_as_described', 'quality_issue']),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'completed']),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fn (array $attributes) => $attributes['created_at'],
        ];
    }

    /**
     * Indicate that the return request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the return request is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the return request is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Indicate that the return request is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the return request is for a damaged product.
     */
    public function damaged(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'damaged',
            'description' => 'Product arrived damaged or defective',
        ]);
    }

    /**
     * Indicate that the return request is for a wrong item.
     */
    public function wrongItem(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'wrong_item',
            'description' => 'Received different item than ordered',
        ]);
    }

    /**
     * Indicate that the return request is recent (within last 30 days).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Indicate that the return request is for quality issues.
     */
    public function qualityIssues(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'Quality issues',
            'description' => $this->faker->paragraph() . ' The product quality is not up to the expected standards. The material feels cheap and the workmanship is poor.',
        ]);
    }

    /**
     * Indicate that the return request is for size/color issues.
     */
    public function sizeColorIssues(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'Size/color not as expected',
            'description' => $this->faker->paragraph() . ' The size/color of the item does not match what was shown on the website. The actual color is different from the product images.',
        ]);
    }

    /**
     * Indicate that the return request is for a changed mind.
     */
    public function changedMind(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'Changed mind',
            'description' => $this->faker->paragraph() . ' I have decided that this item is not what I need. I would like to return it for a refund.',
        ]);
    }

    /**
     * Indicate that the return request is for a duplicate order.
     */
    public function duplicateOrder(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'Duplicate order',
            'description' => $this->faker->paragraph() . ' This item was ordered twice by mistake. I would like to return one of them.',
        ]);
    }

    /**
     * Indicate that the return request is for a better price found.
     */
    public function betterPrice(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'Better price found',
            'description' => $this->faker->paragraph() . ' I found the same item at a lower price elsewhere. I would like to return this item.',
        ]);
    }

    /**
     * Indicate that the return request is for an item not as described.
     */
    public function notAsDescribed(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'Item not as described',
            'description' => $this->faker->paragraph() . ' The product description on the website does not accurately reflect the actual item received.',
        ]);
    }

    /**
     * Indicate that the return request is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-2 days', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-2 days', 'now'),
        ]);
    }

    /**
     * Indicate that the return request is for a high-value order.
     */
    public function highValue(): static
    {
        return $this->state(function (array $attributes) {
            $order = Order::find($attributes['order_id']);
            if ($order) {
                $order->update(['total' => $this->faker->randomFloat(2, 500, 2000)]);
            }
            return [];
        });
    }
} 