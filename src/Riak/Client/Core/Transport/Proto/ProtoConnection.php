<?php

namespace Riak\Client\Core\Transport\Proto;

use GuzzleHttp\Stream\Stream;
use Riak\Client\Core\Transport\RiakTransportException;

/**
 * RPB socket connection
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoConnection
{
    /**
     * @var \GuzzleHttp\Stream\Stream
     */
    private $stream;

    /**
     * @var string
     */
    private $host;

    /**
     * @var integer
     */
    private $port;

    /**
     * @var integer
     */
    private $timeout;

    /**
     * @param string $host
     * @param string $port
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param integer $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return \GuzzleHttp\Stream\Stream
     */
    public function getStream()
    {
        if ($this->stream != null && $this->stream->isReadable()) {
            return $this->stream;
        }

        return $this->stream = $this->createStream();
    }

    /**
     * @return \GuzzleHttp\Stream\Stream
     */
    public function createStream()
    {
        $errno    = null;
        $errstr   = null;
        $uri      = sprintf('tcp://%s:%s', $this->host, $this->port);
        $resource = stream_socket_client($uri, $errno, $errstr);

        if ( ! is_resource($resource)) {
            throw new RiakTransportException(sprintf('Fail to connect to : %s [%s %s]', $uri, $errno, $errstr));
        }

        if ($this->timeout !== null) {
            stream_set_timeout($resource, $this->timeout);
        }

        return Stream::factory($resource);
    }

    /**
     * @param string                    $payload
     * @param \GuzzleHttp\Stream\Stream $stream
     *
     * @return \GuzzleHttp\Stream\Stream
     */
    public function send($payload, Stream $stream = null)
    {
        $socket = $stream ?: $this->getStream();

        $socket->write($payload);

        return $socket;
    }

    /**
     * @param \GuzzleHttp\Stream\Stream $stream
     *
     * @return array
     */
    public function receive(Stream $stream = null)
    {
        $socket = $stream ?: $this->getStream();
        $length = $this->receiveLengthHeader($socket);
        $code   = $this->receiveMessageCode($socket);
        $body   = ($length > 1)
            ? $this->receiveMessageBody($socket, $length - 1)
            : null;

        return [$code, $body];
    }

    /**
     * @param string  $payload
     * @param integer $code
     *
     * @return string
     */
    public function encode($payload, $code)
    {
        return pack("NC", 1 + strlen($payload), $code) . $payload;
    }

    /**
     * @param \GuzzleHttp\Stream\Stream $socket
     *
     * @return integer
     */
    private function receiveLengthHeader(Stream $socket)
    {
        $header = $socket->read(4);

        if ($header === false) {
            throw new RiakTransportException('Fail to read response headers');
        }

        if (strlen($header) !== 4) {
            throw new RiakTransportException('Short read on header, read ' . strlen($header) . ', 4 bytes expected.');
        }

        $unpack = unpack("N", $header);
        $values = array_values($unpack);
        $length = isset($values[0]) ? $values[0] : 0;

        return $length;
    }

    /**
     * @param \GuzzleHttp\Stream\Stream $socket
     *
     * @return integer
     */
    private function receiveMessageCode(Stream $socket)
    {
        $codeBin = $socket->read(1);

        if ($codeBin === false) {
            throw new RiakTransportException('Fail to read response code');
        }

        $unpack = unpack("C", $codeBin);
        $values = array_values($unpack);
        $code   = isset($values[0]) ? $values[0] : 0;

        return $code;
    }

    /**
     * @param \GuzzleHttp\Stream\Stream $socket
     * @param integer                   $length
     *
     * @return \GuzzleHttp\Stream\Stream
     */
    private function receiveMessageBody(Stream $socket, $length)
    {
        $readSize   = 0;
        $bodyBuffer = Stream::factory();

        while ($readSize < $length) {
            $size     = min(8192, $length - $readSize);
            $part     = $socket->read($size);
            $readSize = $readSize + $size;

            if ($part === false) {
                throw new RiakTransportException('Fail to read socket response');
            }

            $bodyBuffer->write($part);
        }

        $bodyBuffer->seek(0);

        return $bodyBuffer;
    }
}
