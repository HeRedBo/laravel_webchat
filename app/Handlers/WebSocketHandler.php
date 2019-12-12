<?php
namespace App\Handlers;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2019-12-11
 * Time: 11:28
 */

class WebSocketHandler implements WebSocketHandlerInterface
{
    public function __construct()
    {
    }

    public function onOpen(Server $server, Request $request)
    {
        // TODO: Implement onOpen() method.
        Log::info('WebSocket 连接建立:' . $request->fd);
    }

    public function onMessage(Server $server, Frame $frame)
    {
        Log::info("从 {$frame->fd} 接收到的数据: {$frame->data}");
        foreach($server->connections as $fd){
            if (!$server->isEstablished($fd)) {
                // 如果连接不可用则忽略
                continue;
            }
            $server->push($fd , $frame->data); // 服务端通过 push 方法向所有连接的客户端发送数据
        }
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        // TODO: Implement onClose() method.
        Log::info('WebSocket 连接关闭:' . $fd);
    }


}