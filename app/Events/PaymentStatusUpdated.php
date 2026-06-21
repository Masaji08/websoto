<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class PaymentStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public string $orderNumber;
    public string $status;

    public function __construct(string $orderNumber, string $status)
    {
        $this->orderNumber = $orderNumber;
        $this->status = $status;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('order-' . $this->orderNumber),
        ];
    }

    public function broadcastAs(): string
    {
        return 'payment.updated';
    }
}
