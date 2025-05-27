<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;

    /**
     * The previous status.
     *
     * @var string|null
     */
    public $previousStatus;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Order $order
     * @param  string|null       $previousStatus
     * @return void
     */
    public function __construct(Order $order, ?string $previousStatus = null)
    {
        $this->order = $order;
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
            ->subject('Order Status Update - Order #' . $this->order->id)
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your order status has been updated.');

        if ($this->previousStatus) {
            $message->line('Previous Status: ' . $this->previousStatus);
        }

        $message->line('Current Status: ' . $this->order->status)
            ->line('Order Total: $' . number_format($this->order->total, 2))
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('Thank you for shopping with us!');

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
            'order_id' => $this->order->id,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->order->status,
            'updated_at' => $this->order->updated_at,
        ];
    }
}
