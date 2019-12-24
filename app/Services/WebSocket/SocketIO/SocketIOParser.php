<?php
/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2019-12-13
 * Time: 14:36
 */

namespace App\Services\WebSocket\SocketIO;


use App\Services\WebSocket\Parser;

class SocketIOParser extends Parser
{
    /**
     * Strategy classes need to implement handle method.
     * @var array
     */
    protected $strategies = [

    ];

    /**
     * Encode output payload for websocket push.
     * @param string $event
     * @param mixed $data
     * @return mixed
     * @author Red-Bo
     * @date 2019-12-13 14:41
     */
    public function encode(string $event, $data)
    {
        $packet = Packet::MESSAGE. Packet::EVENT;
        $shouldEncode = is_array($data) || is_object($data);
        $data = $shouldEncode ? json_encode($data) : $data;
        $format = $shouldEncode ?  '["%s",%s]' : '["%s","%s"]';
        return $packet . sprintf($format, $event, $data);
    }

    /**
     * Decode message from websocket client.
     * Define and return payload here.
     *
     * @param $frame
     * @return mixed
     * @author Red-Bo
     * @date 2019-12-13 15:00
     */
    public function decode($frame)
    {
        $payload = Packet::getPayload($frame->data);

        return [
            'event' => $payload['event'] ?? null,
            'data' => $payload['data'] ?? null,
        ];
    }
}