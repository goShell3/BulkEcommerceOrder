<?php

namespace App\Events;

use App\Models\ReturnRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReturnRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The return request instance.
     *
     * @var \App\Models\ReturnRequest
     */
    public $returnRequest;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    public function __construct(ReturnRequest $returnRequest)
    {
        $this->returnRequest = $returnRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('return-requests'),
            new PrivateChannel('user.' . $this->returnRequest->user_id),
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
            'return_request_id' => $this->returnRequest->id,
            'order_id' => $this->returnRequest->order_id,
            'user_id' => $this->returnRequest->user_id,
            'status' => $this->returnRequest->status,
            'reason' => $this->returnRequest->reason,
            'created_at' => $this->returnRequest->created_at,
        ];
    }
} 
