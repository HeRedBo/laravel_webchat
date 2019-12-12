<?php

namespace App\Events;

use App\Models\Message;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Carbon;


class MessageReceived extends Event
{
    private $message;
    private $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message, $userId)
    {
        $this->message = $message;
        $this->userId  = $userId;
    }


    public function getData()
    {
        $model = new Message();

        $model->root_id = $this->message->room_id;
        $model->msg = $this->message->type == 'text' ? $this->message->content: '';
        $model->img = $this->message->type == 'image' ? $this->message->image: '';
        $model->user_id = $this->userId;
        $model->created_at = Carbon::now();
        return $model;
    }

}
