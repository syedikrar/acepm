<?php

namespace App\Events;

use App\Models\Shop;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifySlack
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $shop;

    /**
     * Create a new event instance.
     *
     * @param Shop   $shop
     * @param string $type
     */
    public function __construct(Shop $shop, $type)
    {
        $this->shop = $shop;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
