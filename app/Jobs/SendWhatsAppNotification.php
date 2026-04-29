<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;

    /** Maximum attempts before the job is marked as failed. */
    public int $tries = 3;

    /** Seconds to wait before retrying after a failure. */
    public int $backoff = 10;

    /**
     * @param  string      $type         'status' | 'ready_pickup' | 'completed'
     * @param  string      $phone        Raw phone number (will be normalised by the service)
     * @param  Order|null  $order        Required for 'ready_pickup'; optional for others
     * @param  array       $data         Extra payload (order_number, status_label, payment_method)
     */
    public function __construct(
        private readonly string $type,
        private readonly string $phone,
        private readonly ?Order $order = null,
        private readonly array $data = [],
    ) {
    }

    public function handle(WhatsAppService $wa): void
    {
        match ($this->type) {
            'status' => $wa->notifyOrderStatus(
                $this->phone,
                $this->data['order_number'],
                $this->data['status_label'],
            ),
            'ready_pickup' => $wa->notifyReadyForPickupWithInvoice(
                $this->phone,
                $this->order,
            ),
            'completed' => $wa->notifyOrderCompleted(
                $this->phone,
                $this->data['order_number'],
                $this->data['payment_method'],
            ),
            default => null,
        };
    }
}
