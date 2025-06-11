<?php

namespace App\Listeners;

use Illuminate\Events\Dispatcher;
use App\Events\ReturnRequestCreated;
use App\Events\ReturnRequestStatusUpdated;
use App\Services\NotificationService;
use App\Services\InventoryService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;

class ReturnRequestEventSubscriber
{
    /**
     * The notification service instance.
     *
     * @var \App\Services\NotificationService
     */
    protected $notificationService;

    /**
     * The inventory service instance.
     *
     * @var \App\Services\InventoryService
     */
    protected $inventoryService;

    /**
     * The payment service instance.
     *
     * @var \App\Services\PaymentService
     */
    protected $paymentService;

    /**
     * Create the event listener.
     *
     * @param  \App\Services\NotificationService $notificationService
     * @param  \App\Services\InventoryService    $inventoryService
     * @param  \App\Services\PaymentService      $paymentService
     * @return void
     */
    public function __construct(
        NotificationService $notificationService,
        InventoryService $inventoryService,
        PaymentService $paymentService
    ) {
        $this->notificationService = $notificationService;
        $this->inventoryService = $inventoryService;
        $this->paymentService = $paymentService;
    }

    /**
     * Handle return request created events.
     *
     * @param  \App\Events\ReturnRequestCreated $event
     * @return void
     */
    public function handleReturnRequestCreated(ReturnRequestCreated $event)
    {
        try {
            // Send return request confirmation
            $this->notificationService->sendReturnRequestConfirmation($event->returnRequest);

            // Notify admin about new return request
            $this->notificationService->notifyAdminAboutReturnRequest($event->returnRequest);

            Log::info(
                'Return request created successfully',
                [
                'return_request_id' => $event->returnRequest->id
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Error handling return request created event',
                [
                'return_request_id' => $event->returnRequest->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Handle return request status updated events.
     *
     * @param  \App\Events\ReturnRequestStatusUpdated $event
     * @return void
     */
    public function handleReturnRequestStatusUpdated(ReturnRequestStatusUpdated $event)
    {
        try {
            // Send status update notification
            $this->notificationService->sendReturnRequestStatusUpdate($event->returnRequest);

            // Handle specific status updates
            switch ($event->returnRequest->status) {
                case 'approved':
                    $this->handleApprovedStatus($event->returnRequest);
                    break;
                case 'rejected':
                    $this->handleRejectedStatus($event->returnRequest);
                    break;
                case 'completed':
                    $this->handleCompletedStatus($event->returnRequest);
                    break;
            }

            Log::info(
                'Return request status updated',
                [
                'return_request_id' => $event->returnRequest->id,
                'status' => $event->returnRequest->status
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Error handling return request status update',
                [
                'return_request_id' => $event->returnRequest->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Handle approved status.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    protected function handleApprovedStatus($returnRequest)
    {
        // Generate return shipping label
        $this->notificationService->sendReturnShippingLabel($returnRequest);
    }

    /**
     * Handle rejected status.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    protected function handleRejectedStatus($returnRequest)
    {
        $this->notificationService->sendReturnRequestRejection($returnRequest);
    }

    /**
     * Handle completed status.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    protected function handleCompletedStatus($returnRequest)
    {
        // Process refund if applicable
        if ($returnRequest->refund_required) {
            $this->paymentService->processRefund($returnRequest->order);
        }

        // Update inventory
        $this->inventoryService->restoreStockForReturn($returnRequest);

        // Send completion notification
        $this->notificationService->sendReturnRequestCompletion($returnRequest);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     * @return void
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            ReturnRequestCreated::class,
            [ReturnRequestEventSubscriber::class, 'handleReturnRequestCreated']
        );

        $events->listen(
            ReturnRequestStatusUpdated::class,
            [ReturnRequestEventSubscriber::class, 'handleReturnRequestStatusUpdated']
        );
    }
}
