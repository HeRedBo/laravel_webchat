<?php

use Swoole\Http\Request;
use App\Services\WebSocket\WebSocket;
use App\Services\Websocket\Facades\Websocket as WebsocketProxy;
use App\Models\Count;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\User;

/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register websocket events for your application.
|
*/


WebsocketProxy::on('connect', function (WebSocket $websocket, Request $request) {
    // 发送欢迎信息
    $websocket->setSender($request->fd);
    $websocket->emit('connect', '欢迎访问聊天室');
});



WebsocketProxy::on('login', function (WebSocket $websocket, $data) {
    \Illuminate\Support\Facades\Log::info(\App\User::where('api_token', $data['token'])->first());
    if (!empty($data['token']) && ($user = \App\User::where('api_token', $data['token'])->first())) {
        $websocket->loginUsing($user);
        // 读取未读消息
        $room_counts = \App\Models\Count::where('user_id', $user->id)
            ->whereIn('room_id',\App\Models\Count::$ROOMLIST)
            ->select(['room_id','count'])
            ->get()
            ->toArray();
        $rooms = [];
        $room_counts = array_column($room_counts,'count','room_id');
        foreach (\App\Models\Count::$ROOMLIST as $room_id)
        {
            $count = isset($room_counts[$room_id]) ? $room_counts[$room_id] : 0;
            $rooms[$room_id] = $count;
        }
        $websocket->toUser($user)->emit('count', $rooms);
        $websocket->toUser($user)->emit('login', '登录成功');
    } else {
        $websocket->emit('login', '登录后才能进入聊天室');
    }
});

WebsocketProxy::on('room', function (WebSocket $websocket, $data) {
    Log::info('进入聊天room：');
    if (!empty($data['api_token']) && ($user = User::where('api_token', $data['api_token'])->first())) {
        // 从请求数据中获取房间ID
        if (empty($data['roomid'])) {
            return;
        }
        $roomId = $data['roomid'];
        // 重置用户与fd关联
        Redis::command('hset', ['socket_id', $user->id, $websocket->getSender()]);
        // 将该房间下用户未读消息清零
        $count = \App\Models\Count::where('user_id', $user->id)->where('room_id', $roomId)->first();
        if(!$count)
        {
            $count = new \App\Models\Count();
            $count->user_id =  $user->id;
            $count->room_id =  $roomId;
        }
        $count->count = 0;
        $count->save();
        // 将用户加入指定房间
        #$room = Count::$ROOMLIST[$roomId];
        $room = $roomId;
        $websocket->join($room);
        // 打印日志
        Log::info($user->name . '进入房间：' . $room);
        // 更新在线用户信息
        $roomUsersKey = 'online_users_' . $room;
        $onlineUsers = Cache::get($roomUsersKey);
        $user->src = $user->avatar;
        if ($onlineUsers) {
            $onlineUsers[$user->id] = $user;
            Cache::forever($roomUsersKey, $onlineUsers);
        } else {
            $onlineUsers = [
                $user->id => $user
            ];
            Cache::forever($roomUsersKey, $onlineUsers);
        }
        Log::info("在线用户数");
        Log::info($onlineUsers);
        // 广播消息给房间内所有用户
        $websocket->to($room)->emit('room', $onlineUsers);
    }
    else
    {
        $websocket->emit('login', '登录后才能进入聊天室');
    }
});

## 用户退出房间
WebsocketProxy::on('roomout', function (WebSocket $websocket, $data)
{
    roomout($websocket, $data);
});


WebsocketProxy::on('disconnect', function (WebSocket $websocket, $data) {
    roomout($websocket, $data);
});

function roomout(WebSocket $websocket, $data) {
    if (!empty($data['api_token']) && ($user = User::where('api_token', $data['api_token'])->first()))
    {
        if (empty($data['roomid'])) {
            return;
        }
        $roomId = $data['roomid'];
        # $room = Count::$ROOMLIST[$roomId];
        $room = $roomId;

        // 更新在线用户信息
        $roomUsersKey = 'online_users_' . $room;
        $onlineUsers = Cache::get($roomUsersKey);
        Log::info("退出房间前在线用户数");
        Log::info($onlineUsers);
        if (!empty($onlineUsers[$user->id])) {
            unset($onlineUsers[$user->id]);
            Cache::forever($roomUsersKey, $onlineUsers);
        }
        Log::info("退出房间后在线用户数");
        Log::info($onlineUsers);
        $websocket->to($room)->emit('roomout', $onlineUsers);
        Log::info($user->name . '退出房间: ' . $room);
        $websocket->leave([$room]);
    }
    else {
        $websocket->emit('login', '登录后才能进入聊天室');
    }
}