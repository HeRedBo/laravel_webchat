<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    //

    public function history(Request $request)
    {
        $roomId = (int) $request->get('roomid');
        $current = (int) $request->get('current');
        $total   = (int) $request->get('total');

        if ($roomId <= 0 || $current <= 0) {
            Log::error('无效的房间和页面信息');
            return response()->json([
                'data' => [
                    'errno' => 1,
                    'message' => '无效的房间和页面信息'
                ]
            ]);
        }

        // 获取消息总数
        $messageTotal = Message::where('room_id', $roomId)->count();
        $limit = 20;  // 每页显示20条消息
        $skip = ($current - 1) * 20;  // 从第多少条消息开始

        // 分页查询消息
        $messageData = Message::where('room_id', $roomId)->skip($skip)->take($limit)->orderBy('created_at', 'asc')->get();
        if($messageData)
        {
            $messageData = MessageResource::collection($messageData);
        }
        // 返回响应信息
        return response()->json([
            'data' => [
                'errno' => 0,
                'message' => '数据获取成功',
                'data' => $messageData,
                'total' => $messageTotal,
                'current' => $current
            ]
        ]);
    }
}
