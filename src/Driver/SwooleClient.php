<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-04-01
 * Time: 12:46
 */

namespace PhpComp\WebSocket\Client\Driver;

use PhpComp\WebSocket\Client\AbstractClient;
use Swoole\Client;

/**
 * Class SwooleDriver
 * power by `swoole` extension
 * @package PhpComp\WebSocket\Client\Driver
 */
class SwooleClient extends AbstractClient
{
    /**
     * @var string
     */
    const DRIVER_NAME = 'swoole';

    /**
     * @var Client
     */
    private $swClient;

    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        return \extension_loaded('swoole');
    }

    /**
     * @return array
     */
    public function appendDefaultConfig(): array
    {
        return array_merge(parent::appendDefaultConfig(), [
            'swoole' => [
                // 结束符检测
                'open_eof_check' => true,
                'package_eof' => self::HEADER_END,
                'package_max_length' => 1024 * 1024 * 2, //协议最大长度

                // 长度检测
                // 'open_length_check'     => 1,
                // 'package_length_type'   => 'N',
                // 'package_length_offset' => 0,       //第N个字节是包长度的值
                // 'package_body_offset'   => 4,       //第几个字节开始计算长度

                // Socket缓存区尺寸
                'socket_buffer_size' => 1024 * 1024 * 2, //2M缓存区
            ]
        ]);
    }

    /**
     * @param float $timeout
     * @param int $flags
     */
    protected function doConnect($timeout = 2.2, $flags = 0)
    {
        $type = SWOOLE_SOCK_TCP;

        if ($this->getValue('enable_ssl')) {
            $type |= SWOOLE_SSL;
        }

        $this->swClient = new Client($type);

        if ($keyFile = $this->getValue('ssl_key_file')) {
            $this->swClient->set([
                'ssl_key_file' => $keyFile,
                'ssl_cert_file' => $this->getValue('ssl_cert_file')
            ]);
        }

        if (!$this->swClient->connect($this->getHost(), $this->getPort(), $timeout)) {
            $this->cliOut->error("connect failed. Error: {$this->getErrorMsg()}", -404);
        }
    }

    /**
     * @param int $length
     * @return string
     */
    public function readResponseHeader(int $length = 0): string
    {
        $headerBuffer = '';

        while (true) {
            $_tmp = $this->swClient->recv();

            if (!$_tmp) {
                return '';
            }

            $headerBuffer .= $_tmp;

            if (strpos($headerBuffer, self::HEADER_END) !== false) {
                break;
            }
        }

        return $headerBuffer;
    }

    /**
     * @param string $data
     * @return bool|int
     * @throws ConnectionException
     */
    protected function write(string $data)
    {
        $written = $this->swClient->send($data);

        if ($written < ($dataLen = \strlen($data))) {
            throw new ConnectionException("Could only write $written out of $dataLen bytes.");
        }

        return $written;
    }

    /**
     * @param string $message
     * @param null $flag
     * @return bool|int
     */
    public function send($message, $flag = null)
    {
        return $this->swClient->send($message, $flag);
    }

    public function sendFile(string $filename)
    {
        return $this->swClient->sendfile($filename);
    }

    /**
     * @param null $size
     * @param null $flag
     * @return mixed
     */
    public function receive($size = null, $flag = null)
    {
        return $this->swClient->recv($size, $flag);
    }

    /**
     * @param bool $force
     */
    public function close(bool $force = false)
    {
        $this->swClient->close($force);

        parent::close($force);
    }

    /**
     * @return resource
     */
    public function getSocket(): resource
    {
        return $this->swClient->sock;
    }

    /**
     * @param resource $socket
     * @return array
     */
    public function getSockName($socket)
    {
        return $this->swClient->getsockname();
    }

    /**
     * @param resource $socket
     * @return mixed
     */
    public function getPeerName($socket)
    {
        return $this->swClient->getpeername();
    }

    /**
     * @return Client
     */
    public function getSwClient(): Client
    {
        return $this->swClient;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setClientOption($name, $value)
    {
        $this->setClientOptions([$name => $value]);
    }

    /**
     * @param array $options
     */
    public function setClientOptions(array $options)
    {
        $this->swClient->set($options);
    }

    /**
     * @return int
     */
    public function getErrorNo()
    {
        return $this->swClient->errCode;
    }

    /**
     * @return string
     */
    public function getErrorMsg()
    {
        return socket_strerror($this->swClient->errCode);
    }

    /**
     * @param $method
     * @param array $args
     * @return mixed
     * @throws UnknownCalledException
     */
    public function __call($method, array $args = [])
    {
        if (method_exists($this->swClient, $method)) {
            return \call_user_func_array([$this->swClient, $method], $args);
        }

        throw new UnknownCalledException("Call the method [$method] not exists!");
    }
}
