<?php

namespace App\Notifications;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReturnRequestStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The return request instance.
     *
     * @var \App\Models\ReturnRequest
     */
    public $returnRequest;

    /**
     * The previous status.
     *
     * @var string|null
     */
    public $previousStatus;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @param  string|null               $previousStatus
     * @return void
     */
    public function __construct(ReturnRequest $returnRequest, ?string $previousStatus = null)
    {
        $this->returnRequest = $returnRequest;
        $this->previousStatus = $previousStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage())
            ->subject('Return Request Status Update - Request #' . $this->returnRequest->id)
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your return request status has been updated.');

        if ($this->previousStatus) {
            $message->line('Previous Status: ' . $this->previousStatus);
        }

        $message->line('Current Status: ' . $this->returnRequest->status)
            ->line('Order Number: #' . $this->returnRequest->order_id)
            ->line('Reason: ' . $this->returnRequest->reason)
            ->action('View Return Request', url('/return-requests/' . $this->returnRequest->id))
            ->line('Thank you for your patience!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'return_request_id' => $this->returnRequest->id,
            'order_id' => $this->returnRequest->order_id,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->returnRequest->status,
            'updated_at' => $this->returnRequest->updated_at,
        ];
    }
}
