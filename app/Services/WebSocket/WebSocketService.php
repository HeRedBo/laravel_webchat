<?php
namespace App\Services\WebSocket;

use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebSocketService implements WebSocketHandlerInterface
{
    public function __construct()
    {
    }

    /**
     * 连接建立时触发
     * @param Server $server
     * @param Request $request
     * @author Red-Bo
     * @date 2019-12-03 20:45
     */
    public function onOpen(Server $server, Request $request)
    {
        // 在触发 WebSocket 连接建立事件之前，Laravel 应用初始化的生命周期已经结束，你可以在这里获取 Laravel 请求和会话数据
        // 调用 push 方法向客户端推送数据，fd 是客户端连接标识字段
        Log::info('WebSocket 连接建立');
        app('swoole')->wsTable->set('fd:' . $request->fd, ['value' => $request->fd]);
        $server->push($request->fd, 'Welcome to WebSocket Server built on LaravelS');
    }

    /**
     * 收到消息时触发
     * @param Server $server
     * @param Frame $frame
     * @author Red-Bo
     * @date 2019-12-03 20:45
     */
    public function onMessage(Server $server, Frame $frame)
    {
        foreach (app('swoole')->wsTable as $key => $row) {
            if (strpos($key, 'fd:') === 0 && $server->exist($row['value'])) {
                Log::info('Receive message from client: ' . $row['value']);
                // 调用 push 方法向客户端推送数据
                $server->push($row['value'], 'This is a message sent from WebSocket Server at ' . date('Y-m-d H:i:s'));
            }
        }
    }

    /**
     * 关闭连接时触发
     * @param Server $server
     * @param $fd
     * @param $reactorId
     * @author Red-Bo
     * @date 2019-12-03 20:47
     */
    public function onClose(Server $server, $fd, $reactorId)
    {

        Log::info('WebSocket 连接关闭');
    }
}