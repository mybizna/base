<?php

namespace Modules\Base\Events;

use Illuminate\Queue\SerializesModels;

class ReservedUsernames

{

    use SerializesModels;

    public $username;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
