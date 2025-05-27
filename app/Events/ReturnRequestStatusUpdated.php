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

class ReturnRequestStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The return request instance.
     *
     * @var \App\Models\ReturnRequest
     */
    public $returnRequest;

    /**
     * The previous status.
     *
     * @var string
     */
    public $previousStatus;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @param  string                    $previousStatus
     * @return void
     */
    public function __construct(ReturnRequest $returnRequest, string $previousStatus)
    {
        $this->returnRequest = $returnRequest;
        $this->previousStatus = $previousStatus;
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
            'previous_status' => $this->previousStatus,
            'new_status' => $this->returnRequest->status,
            'updated_at' => $this->returnRequest->updated_at,
        ];
    }
} 
