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
        $messageData = Message::where('room_id', $roomId)
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy('created_at', 'DESC')->get();

        if($messageData)
        {
            ## 数据反转 便于前端渲染
            $messageData = $messageData->reverse();
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

    public function count(Request $request)
    {
        // 读取未读消息
        $room_counts = \App\Models\Count::where('user_id', 6)
            ->whereIn('room_id',array_keys(\App\Models\Count::$ROOMLIST))
            ->select(['room_id','count'])
            ->get()
            ->toArray();
        $rooms = [];
        $room_counts = array_column($room_counts,'count','room_id');
        dd($room_counts);
    }
}
