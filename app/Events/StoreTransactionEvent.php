<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class StoreTransactionEvent implements ShouldBroadcast
{

    use SerializesModels;

    public function broadcastOn()
    {
        return new Channel('transaction-create-channel');
    }

    public function broadcastWith()
    {
        return [
            'data' => __("transaction_message.success"),
        ];
    }
}
