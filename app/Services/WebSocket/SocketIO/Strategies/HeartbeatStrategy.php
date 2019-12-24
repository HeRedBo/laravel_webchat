<?php
/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2019-12-13
 * Time: 15:33
 */
namespace App\Services\WebSocket\SocketIO\Strategies;

use App\Services\WebSocket\SocketIO\Packet;

class HeartbeatStrategy
{
    /**
     * if return value is true will skip decoding
     *
     * @param  \Swoole\WebSocket\Server $server
     * @param \Swoole\WebSocket\Frame $frame
     * @return boolean
     * @author Red-Bo
     * @date 2019-12-13 15:34
     */
    public function handle($server, $frame)
    {
        $packet = $frame->data;
        $packetLength = strlen($packet);
        $payload = '';

        if(Packet::getPayload($packet))
        {
            return false;
        }

        if($isPing = Packet::isSocketType($packet,'ping'))
        {
            $payload .= Packet::PONG;
        }

        if($isPing && $packetLength > 1)
        {
            $payload .= substr($packet, 1, $packetLength -1);
        }

        if($isPing)
        {
            $server->push($frame->id, $payload);
        }

        return true;
    }


}