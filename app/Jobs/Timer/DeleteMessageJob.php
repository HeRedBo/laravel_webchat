<?php

namespace App\Jobs\Timer;


use App\Models\Message;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Illuminate\Support\Facades\DB;

class DeleteMessageJob extends CronJob
{

    // 该方法可类比为 Swoole 定时器中的回调方法
    public function run()
    {
        // 删除最新所有聊天室的所有信息
        DB::table('messages')->truncate();
        //$this->stop();
    }

    // 每隔 1000ms 执行一次任务
    public function interval()
    {
        return 1000 * (3600 * 1);   // 定时器间隔，单位为 ms  1小时执行一次 删除相关数据
    }

    // 是否在设置之后立即触发 run 方法执行
    public function isImmediate()
    {
        return false;
    }

}
