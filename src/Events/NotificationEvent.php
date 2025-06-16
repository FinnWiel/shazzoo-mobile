<?php

namespace FinnWiel\ShazzooMobile\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class NotificationEvent implements ShouldBroadcast
{
    public $type;
    public $data;

    public function __construct(string $type, array $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function broadcastAs()
    {
        return $this->type . '.notification';
    }

    public function broadcastOn()
{
    return new Channel('notifications.' . $this->type);
}

    public function broadcastWith()
    {
        return $this->data;
    }
}
