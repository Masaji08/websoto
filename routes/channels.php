<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('kasir-orders', function ($user) {
    \Log::debug('Broadcast auth for kasir-orders', ['user_id' => $user?->id, 'role' => $user?->role]);
    return $user && in_array($user->role, ['cashier', 'admin']);
});

Broadcast::channel('order-{orderNumber}', function ($user, $orderNumber) {
    return in_array($user->role, ['cashier', 'admin']);
});
