<?php
/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2019-12-13
 * Time: 14:21
 */

namespace App\Services\WebSocket;


use Illuminate\Support\Facades\App;

abstract class Parser
{
    /**
     * Strategy classes need to implement handle method.
     * @var array
     */
    protected $strategies = [];


    /**
     * Execute strategies before decoding handle method
     *
     *  If return value is true will skip decoding.
     * @param \Swoole\WebSocket\Server  $server
     * @param \Swoole\WebSocket\Frame $frame
     * @return bool
     * @author Red-Bo
     * @date 2019-12-13 14:26
     */
    public function execute($server, $frame)
    {
        $skip = false;

        foreach ($this->strategies as $strategy) {
            $result = App::call(
                $strategy . '@handle',
                [
                    'server' => $server,
                    'frame' => $frame,
                ]
            );
            if ($result === true) {
                $skip = true;
                break;
            }
        }
        return $skip;
    }


    /**
     * Encode output payload fro websocket push
     * @param string $event
     * @param mixed $data
     * @return mixed
     * @author Red-Bo
     * @date 2019-12-13 14:33
     */
    abstract public function encode(string $event, $data);

    /**
     * Input message on websocket connected
     * Define and return name and payload dara here
     *
     * @param \Swoole\Websocket\Frame $frame
     *
     * @return array
     *
     * @author Red-Bo
     * @date 2019-12-13 14:34
     */
    abstract public function decode($frame);

}