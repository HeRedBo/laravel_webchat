<?php

namespace App\Listeners;

use Hhxsv5\LaravelS\Swoole\Events\WorkerStartInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Server;


class WorkerStartEventListener implements WorkerStartInterface
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
    public function handle(Server $server, $wokerId)
    {
        Log::info("Worker/Task[{$wokerId}] Process Started");
    }
}
