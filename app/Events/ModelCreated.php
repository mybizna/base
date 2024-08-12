<?php

namespace Modules\Base\Events;

use Illuminate\Queue\SerializesModels;

class ModelCreated
{

    use SerializesModels;

    public $table_name;
    public $model;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($table_name, $model)
    {
        $this->table_name = $table_name;
        $this->model = $model;
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
