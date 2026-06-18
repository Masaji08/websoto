<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class PaymentStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public string $orderId;
    public string $status;

    public function __construct(string|int $orderId, string $status)
    {
        $this->orderId = (string) $orderId;
        $this->status = $status;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('order.' . $this->orderId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'payment.updated';
    }
}
