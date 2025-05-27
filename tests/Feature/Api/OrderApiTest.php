<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /**
     *
     *
     * @test
     */
    public function itCanListOrders()
    {
        // Create some test orders
        $orders = Order::factory()->count(3)->create();

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(
                [
                'data' => [
                    '*' => [
                        'id',
                        'order_number',
                        'status',
                        'total',
                        'location',
                        'created_at',
                        'updated_at'
                    ]
                ]
                ]
            );
    }

    /**
     *
     *
     * @test
     */
    public function itCanCreateAnOrder()
    {
        $orderData = [
            'order_number' => 'ORD-123456',
            'status' => 'pending',
            'total' => 100.00,
            'location' => 'New York'
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                'data' => [
                    'id',
                    'order_number',
                    'status',
                    'total',
                    'location',
                    'created_at',
                    'updated_at'
                ]
                ]
            );

        $this->assertDatabaseHas('orders', $orderData);
    }

    /**
     *
     *
     * @test
     */
    public function itCanShowAnOrder()
    {
        $order = Order::factory()->create();

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                'data' => [
                    'id',
                    'order_number',
                    'status',
                    'total',
                    'location',
                    'created_at',
                    'updated_at'
                ]
                ]
            );
    }

    /**
     *
     *
     * @test
     */
    public function itCanUpdateAnOrder()
    {
        $order = Order::factory()->create();
        $updateData = [
            'status' => 'completed',
            'total' => 150.00
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('orders', $updateData);
    }

    /**
     *
     *
     * @test
     */
    public function itCanDeleteAnOrder()
    {
        $order = Order::factory()->create();

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    /**
     *
     *
     * @test
     */
    public function itValidatesRequiredFieldsWhenCreatingOrder()
    {
        $response = $this->postJson('/api/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_number', 'status', 'total', 'location']);
    }

    /**
     *
     *
     * @test
     */
    public function itValidatesNumericTotalField()
    {
        $orderData = [
            'order_number' => 'ORD-123456',
            'status' => 'pending',
            'total' => 'not-a-number',
            'location' => 'New York'
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['total']);
    }
}
