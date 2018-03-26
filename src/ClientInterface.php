<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-04-01
 * Time: 12:41
 */

namespace MyLib\WebSocket\Client;

use MyLib\WebSocket\Util\WebSocketInterface;

/**
 * Interface ClientInterface
 * @package MyLib\WebSocket\Client
 */
interface ClientInterface extends WebSocketInterface
{
    const TIMEOUT = 3.2;

    /**
     * @return bool
     */
    public static function isSupported(): bool;

    public function start();

    /**
     * @param string $event
     * @param callable $cb
     * @param bool $replace
     * @return mixed
     */
    public function on(string $event, callable $cb, bool $replace = false);

    /**
     * @param float $timeout
     * @param int $flag
     * @return void
     */
    public function connect($timeout = 0.1, $flag = 0);

    /**
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * @return resource
     */
    public function getSocket();

    /**
     * 用于获取客户端socket的本地host:port，必须在连接之后才可以使用
     * @param resource $socket
     * @return array
     */
    public function getSockName($socket): array;

    /**
     * 获取对端(远端)socket的IP地址和端口
     * 函数必须在$client->receive() 之后调用
     * @param resource $socket
     * @return mixed
     */
    public function getPeerName($socket);

    /**
     * 获取服务器端证书信息
     * @return mixed
     */
//    public function getPeerCert();

    /**
     * @param string $data
     * @param null|int $flag
     * @return mixed
     */
    public function send($data, $flag = null);

    /*
     * @param string $ip
     * @param int $port
     * @param string $data
     * @return mixed
     */
    //public function sendTo(string $ip, int $port, string $data);

    public function sendFile(string $filename);

    public function receive($size = 65535, $flag = null);

    public function close(bool $force = false);

//    public function sleep();
//
//    public function wakeUp();

//    public function enableSSL();
}
