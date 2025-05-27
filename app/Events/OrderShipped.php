<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderShipped implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;

    /**
     * The tracking number.
     *
     * @var string
     */
    public $trackingNumber;

    /**
     * The shipping carrier.
     *
     * @var string
     */
    public $carrier;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Order $order
     * @param  string            $trackingNumber
     * @param  string            $carrier
     * @return void
     */
    public function __construct(Order $order, string $trackingNumber, string $carrier)
    {
        $this->order = $order;
        $this->trackingNumber = $trackingNumber;
        $this->carrier = $carrier;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders'),
            new PrivateChannel('user.' . $this->order->user_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'status' => $this->order->status,
            'tracking_number' => $this->trackingNumber,
            'carrier' => $this->carrier,
            'shipped_at' => $this->order->updated_at,
        ];
    }
} 
