<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnRequestApiTest extends TestCase
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
    public function itCanListReturnRequests()
    {
        // Create some test return requests
        $returnRequests = ReturnRequest::factory()->count(3)->create();

        $response = $this->getJson('/api/return-requests');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(
                [
                'data' => [
                    '*' => [
                        'id',
                        'order_id',
                        'status',
                        'reason',
                        'is_urgent',
                        'created_at',
                        'updated_at',
                        'order' => [
                            'id',
                            'order_number',
                            'status',
                            'total',
                            'location'
                        ]
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
    public function itCanCreateAReturnRequest()
    {
        $order = Order::factory()->create();
        $returnData = [
            'order_id' => $order->id,
            'status' => 'pending',
            'reason' => 'damaged',
            'is_urgent' => false
        ];

        $response = $this->postJson('/api/return-requests', $returnData);

        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                'data' => [
                    'id',
                    'order_id',
                    'status',
                    'reason',
                    'is_urgent',
                    'created_at',
                    'updated_at'
                ]
                ]
            );

        $this->assertDatabaseHas('return_requests', $returnData);
    }

    /**
     *
     *
     * @test
     */
    public function itCanShowAReturnRequest()
    {
        $returnRequest = ReturnRequest::factory()->create();

        $response = $this->getJson("/api/return-requests/{$returnRequest->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                'data' => [
                    'id',
                    'order_id',
                    'status',
                    'reason',
                    'is_urgent',
                    'created_at',
                    'updated_at',
                    'order'
                ]
                ]
            );
    }

    /**
     *
     *
     * @test
     */
    public function itCanUpdateAReturnRequest()
    {
        $returnRequest = ReturnRequest::factory()->create();
        $updateData = [
            'status' => 'approved',
            'reason' => 'wrong_size'
        ];

        $response = $this->putJson("/api/return-requests/{$returnRequest->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('return_requests', $updateData);
    }

    /**
     *
     *
     * @test
     */
    public function itCanDeleteAReturnRequest()
    {
        $returnRequest = ReturnRequest::factory()->create();

        $response = $this->deleteJson("/api/return-requests/{$returnRequest->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('return_requests', ['id' => $returnRequest->id]);
    }

    /**
     *
     *
     * @test
     */
    public function itValidatesRequiredFieldsWhenCreatingReturnRequest()
    {
        $response = $this->postJson('/api/return-requests', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id', 'status', 'reason']);
    }

    /**
     *
     *
     * @test
     */
    public function itValidatesOrderExistsWhenCreatingReturnRequest()
    {
        $returnData = [
            'order_id' => 99999, // Non-existent order ID
            'status' => 'pending',
            'reason' => 'damaged'
        ];

        $response = $this->postJson('/api/return-requests', $returnData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id']);
    }

    /**
     *
     *
     * @test
     */
    public function itValidatesUrgentReturnForHighValueOrders()
    {
        // Create a high value order
        $order = Order::factory()->create(
            [
            'total' => 1000.00
            ]
        );

        $returnData = [
            'order_id' => $order->id,
            'status' => 'pending',
            'reason' => 'damaged',
            'is_urgent' => false
        ];

        $response = $this->postJson('/api/return-requests', $returnData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_urgent']);
    }
}
