<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-03-30
 * Time: 13:26
 */

namespace MyLib\WebSocket\Client;

/**
 * Class InteractiveClient
 *  Interactive Terminal environment
 * @package MyLib\WebSocket\Client
 */
class InteractiveClient
{
    const CMD_PREFIX = ':';
    const DEFAULT_CMD = 'send';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * command callbacks
     * @var array
     */
    protected $callbacks = [];

    /**
     * WebSocket constructor.
     * @param string $url `ws://127.0.0.1:9501/chat`
     * @param array $options
     */
    public function __construct(string $url, array $options = [])
    {
        $this->client = ClientFactory::make('ws://127.0.0.1:9501');

        $this->client->on(AbstractClient::ON_OPEN, [$this, 'onOpen']);
        $this->client->on(AbstractClient::ON_MESSAGE, [$this, 'onMessage']);
        $this->client->on(AbstractClient::ON_CLOSE, [$this, 'onClose']);
    }

    /**
     *
     */
    public function start()
    {
        $this->client->start();
    }

    /**
     */
    public function onOpen()
    {

    }

    /**
     */
    public function onMessage()
    {

    }

    /**
     */
    public function onClose()
    {

    }

    /**
     * @param $command
     * @param callable $cb
     * @return $this
     */
    public function add($command, callable $cb): self
    {


        return $this;
    }

    /**
     * `@send`
     *
     */
    public function sendCommand()
    {

    }

    /**
     * `@help` `?`
     *
     */
    public function helpCommand()
    {

    }

    /**
     * `@close`
     */
    public function closeCommand()
    {

    }

}
