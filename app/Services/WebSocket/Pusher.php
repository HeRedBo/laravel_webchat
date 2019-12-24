<?php
/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2019-12-13
 * Time: 16:02
 */

namespace App\Services\WebSocket;


class Pusher
{
    /**
     * @var  \Swoole\Websocket\Server
     */
    protected $server;

    /**
     * @var int
     */
    protected $opcode;

    /**
     * @var int
     */
    protected $sender;

    /**
     * @var  array
     */
    protected $descriptors;

    /**
     * @var bool
     */
    protected $broadcast;

    /**
     * @var bool
     */
    protected $assigned;

    /**
     * @var string
     */
    protected $event;

    /**
     * @var  mixed|bool
     */
    protected $message;

    /**
     * Push constructor.
     * Pusher constructor.
     * @param int $opcode
     * @param int $sender
     * @param array $descriptors
     * @param bool $assigned
     * @param bool $broadcast
     * @param string $event
     * @param $server
     * @param null $message
     */
    public function __construct(
        int $opcode,
        int $sender,
        array $descriptors,
        bool $broadcast,
        bool $assigned,
        string $event,
        $server,
        $message = null
    )
    {
        $this->opcode = $opcode;
        $this->sender = $sender;
        $this->descriptors = $descriptors;
        $this->broadcast = $broadcast;
        $this->assigned = $assigned;
        $this->event = $event;
        $this->message = $message;
        $this->server = $server;
    }

    public static function make(array $data, $server)
    {
        return new static(
            $data['opcode'] ?? 1,
            $data['sender'] ?? 0,
            $data['fds'] ?? [],
            $data['broadcast'] ?? false,
            $data['assigned'] ?? false,
            $data['event'] ?? '',
            $server,
            $data['message'] ?? null
        );
    }

    /**
     * @return \Swoole\Websocket\Server
     */
    public function getServer(): \Swoole\Websocket\Server
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getOpcode(): int
    {
        return $this->opcode;
    }

    /**
     * @return int
     */
    public function getSender(): int
    {
        return $this->sender;
    }

    /**
     * @return array
     */
    public function getDescriptors(): array
    {
        return $this->descriptors;
    }

    /**
     * @param $descriptor
     * @return Pusher
     * @author Red-Bo
     * @date 2019-12-13 16:24
     */
    public function addDescriptor($descriptor):self
    {
        return $this->addDescriptors([$descriptor]);
    }

    /**
     * @param array $descriptors
     * @return Pusher
     * @author Red-Bo
     * @date 2019-12-13 16:24
     */
    public function addDescriptors(array $descriptors):self
    {
        $this->descriptors = array_values(
            array_unique(
                array_merge($this->descriptors,$descriptors)
            )
        );
        return $this;
    }

    /**
     * @param int $descriptor
     * @return bool
     * @author Red-Bo
     * @date 2019-12-13 16:27
     */
    public function hasDescriptor(int $descriptor): bool
    {
        return in_array($descriptor, $this->descriptors);
    }


    /**
     * @return bool
     */
    public function isBroadcast(): bool
    {
        return $this->broadcast;
    }

    /**
     * @return bool
     */
    public function isAssigned(): bool
    {
        return $this->assigned;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return bool|mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     *
     * @return bool
     * @author Red-Bo
     * @date 2019-12-13 16:33
     */
    public function shouldBroadcast():bool
    {
        return $this->broadcast && empty($this->descriptors) && ! $this->assigned;
    }

    /**
     *  Returns all descriptors that are websocket
     * @return array
     * @author Red-Bo
     * @date 2019-12-13 16:43
     */
    protected function getWebsocketConnections():array
    {
        return array_filter(iterator_to_array($this->server->connections), function ($fd){
           return $this->server->isEstablished($fd);
        });
    }

    /**
     * @param int $fd
     * @return bool
     * @author Red-Bo
     * @date 2019-12-13 16:52
     */
    public function shouldPushToDescriptor(int $fd): bool
    {
        if(! $this->server->isEstablished($fd))
        {
            return false;
        }
        return $this->broadcast ? $this->sender !== (int) $fd : true;
    }


    public function push($payload):void
    {
        // attach sender if not broadcast
        if(! $this->broadcast && $this->sender && $this->hasDescriptor($this->sender))
        {
            $this->addDescriptor($this->sender);
        }

        // check if to broadcast to other clients
        if($this->shouldBroadcast())
        {
            $this->addDescriptors($this->getWebsocketConnections());
        }

        // // push message to designated fds
        foreach ($this->descriptors as $descriptor) {
            if ( $this->shouldPushToDescriptor($descriptor)) {
                $this->server->push($descriptor, $payload, $this->opcode);
            }
        }

    }








}