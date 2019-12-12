<?php

namespace App\Listeners;

use Hhxsv5\LaravelS\Swoole\Task\Listener;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use Illuminate\Support\Facades\Log;

class TestEventListener extends Listener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Event $event)
    {
        Log::info(__CLASS__ . ': 开始处理', [$event->getData()]);
        sleep(3);// 模拟耗时代码的执行
        Log::info(__CLASS__ . ': 处理完毕');
    }
}
