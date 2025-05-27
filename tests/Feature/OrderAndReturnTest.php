<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderAndReturnTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itCanCreateAnOrderWithDefaultFactory()
    {
        $order = Order::factory()->create();
        
        $this->assertInstanceOf(Order::class, $order);
        $this->assertInstanceOf(User::class, $order->user);
        $this->assertIsArray($order->shipping_address);
        $this->assertArrayHasKey('address', $order->shipping_address);
        $this->assertArrayHasKey('city', $order->shipping_address);
        $this->assertArrayHasKey('state', $order->shipping_address);
        $this->assertArrayHasKey('zip_code', $order->shipping_address);
        $this->assertArrayHasKey('country', $order->shipping_address);
    }

    #[Test]
    public function itCanCreateOrdersWithDifferentStatuses()
    {
        $pendingOrder = Order::factory()->pending()->create();
        $processingOrder = Order::factory()->processing()->create();
        $shippedOrder = Order::factory()->shipped()->create();
        $deliveredOrder = Order::factory()->delivered()->create();
        $cancelledOrder = Order::factory()->cancelled()->create();

        $this->assertEquals('pending', $pendingOrder->status);
        $this->assertEquals('processing', $processingOrder->status);
        $this->assertEquals('shipped', $shippedOrder->status);
        $this->assertEquals('delivered', $deliveredOrder->status);
        $this->assertEquals('cancelled', $cancelledOrder->status);
    }

    #[Test]
    public function itCanCreateOrdersWithDifferentValues()
    {
        $highValueOrder = Order::factory()->highValue()->create();
        $lowValueOrder = Order::factory()->lowValue()->create();

        $this->assertGreaterThanOrEqual(500, $highValueOrder->total);
        $this->assertLessThanOrEqual(100, $lowValueOrder->total);
    }

    #[Test]
    public function itCanCreateOrdersFromDifferentLocations()
    {
        $usOrder = Order::factory()->fromUS()->create();
        $canadaOrder = Order::factory()->fromCanada()->create();
        $ukOrder = Order::factory()->fromUK()->create();
        $californiaOrder = Order::factory()->fromCalifornia()->create();
        $nyOrder = Order::factory()->fromNewYork()->create();
        $texasOrder = Order::factory()->fromTexas()->create();

        $this->assertEquals('United States', $usOrder->shipping_address['country']);
        $this->assertEquals('Canada', $canadaOrder->shipping_address['country']);
        $this->assertEquals('United Kingdom', $ukOrder->shipping_address['country']);
        $this->assertEquals('California', $californiaOrder->shipping_address['state']);
        $this->assertEquals('New York', $nyOrder->shipping_address['state']);
        $this->assertEquals('Texas', $texasOrder->shipping_address['state']);
    }

    #[Test]
    public function itCanCreateAReturnRequestWithDefaultFactory()
    {
        $returnRequest = ReturnRequest::factory()->create();
        
        $this->assertInstanceOf(ReturnRequest::class, $returnRequest);
        $this->assertInstanceOf(Order::class, $returnRequest->order);
        $this->assertNotEmpty($returnRequest->reason);
        $this->assertNotEmpty($returnRequest->description);
    }

    #[Test]
    public function itCanCreateReturnRequestsWithDifferentStatuses()
    {
        $pendingReturn = ReturnRequest::factory()->pending()->create();
        $approvedReturn = ReturnRequest::factory()->approved()->create();
        $rejectedReturn = ReturnRequest::factory()->rejected()->create();
        $completedReturn = ReturnRequest::factory()->completed()->create();

        $this->assertEquals('pending', $pendingReturn->status);
        $this->assertEquals('approved', $approvedReturn->status);
        $this->assertEquals('rejected', $rejectedReturn->status);
        $this->assertEquals('completed', $completedReturn->status);
    }

    #[Test]
    public function itCanCreateReturnRequestsWithDifferentReasons()
    {
        $damagedReturn = ReturnRequest::factory()->damaged()->create();
        $wrongItemReturn = ReturnRequest::factory()->wrongItem()->create();
        $qualityIssuesReturn = ReturnRequest::factory()->qualityIssues()->create();
        $sizeColorIssuesReturn = ReturnRequest::factory()->sizeColorIssues()->create();
        $changedMindReturn = ReturnRequest::factory()->changedMind()->create();
        $duplicateOrderReturn = ReturnRequest::factory()->duplicateOrder()->create();
        $betterPriceReturn = ReturnRequest::factory()->betterPrice()->create();
        $notAsDescribedReturn = ReturnRequest::factory()->notAsDescribed()->create();

        $this->assertEquals('Product damaged', $damagedReturn->reason);
        $this->assertEquals('Wrong item received', $wrongItemReturn->reason);
        $this->assertEquals('Quality issues', $qualityIssuesReturn->reason);
        $this->assertEquals('Size/color not as expected', $sizeColorIssuesReturn->reason);
        $this->assertEquals('Changed mind', $changedMindReturn->reason);
        $this->assertEquals('Duplicate order', $duplicateOrderReturn->reason);
        $this->assertEquals('Better price found', $betterPriceReturn->reason);
        $this->assertEquals('Item not as described', $notAsDescribedReturn->reason);
    }

    #[Test]
    public function itCanCreateUrgentReturnRequests()
    {
        $urgentReturn = ReturnRequest::factory()->urgent()->create();
        
        $this->assertTrue(
            $urgentReturn->created_at->diffInDays(now()) <= 2
        );
    }

    #[Test]
    public function itCanCreateHighValueReturnRequests()
    {
        // Create a high-value order first
        $order = Order::factory()->highValue()->create();
        
        // Create a return request for this order
        $returnRequest = ReturnRequest::factory()->create(
            [
            'order_id' => $order->id
            ]
        );
        
        $this->assertGreaterThanOrEqual(500, $returnRequest->order->total);
    }

    #[Test]
    public function itCanCreateComplexOrderAndReturnScenarios()
    {
        // Create a high-value order from California with a quality issue return
        $order = Order::factory()
            ->highValue()
            ->fromCalifornia()
            ->create();

        $returnRequest = ReturnRequest::factory()
            ->qualityIssues()
            ->create(['order_id' => $order->id]);

        $this->assertGreaterThanOrEqual(500, $order->total);
        $this->assertEquals('California', $order->shipping_address['state']);
        $this->assertEquals('Quality issues', $returnRequest->reason);
        $this->assertEquals($order->id, $returnRequest->order_id);

        // Create an urgent return for a wrong item from New York
        $order = Order::factory()
            ->fromNewYork()
            ->create();

        $returnRequest = ReturnRequest::factory()
            ->wrongItem()
            ->urgent()
            ->create(['order_id' => $order->id]);

        $this->assertEquals('New York', $order->shipping_address['state']);
        $this->assertEquals('Wrong item received', $returnRequest->reason);
        $this->assertTrue($returnRequest->created_at->diffInDays(now()) <= 2);
    }
} 
