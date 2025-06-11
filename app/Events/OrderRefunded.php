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

class OrderRefunded implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;

    /**
     * The refund amount.
     *
     * @var float
     */
    public $amount;

    /**
     * The refund reason.
     *
     * @var string|null
     */
    public $reason;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Order $order
     * @param  float             $amount
     * @param  string|null       $reason
     * @return void
     */
    public function __construct(Order $order, float $amount, ?string $reason = null)
    {
        $this->order = $order;
        $this->amount = $amount;
        $this->reason = $reason;
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
            'refund_amount' => $this->amount,
            'refund_reason' => $this->reason,
            'refunded_at' => $this->order->updated_at,
        ];
    }
}
