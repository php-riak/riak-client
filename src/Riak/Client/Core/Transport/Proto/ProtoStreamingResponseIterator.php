<?php

namespace Riak\Client\Core\Transport\Proto;

use Iterator;
use GuzzleHttp\Stream\StreamInterface;

/**
 * RPB response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class ProtoStreamingResponseIterator implements Iterator
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    protected $client;

    /**
     * @var \GuzzleHttp\Stream\StreamInterface
     */
    protected $stream;

    /**
     * @var integer
     */
    protected $count = 0;

    /**
     * @var \DrSlump\Protobuf\Message
     */
    protected $current;

    /**
     * @var integer
     */
    protected $messageCode;

    /**
     * @param \Riak\Client\Core\Transport\Proto\ProtoClient $client
     * @param \GuzzleHttp\Stream\Stream                     $stream
     * @param integer                                       $messageCode
     */
    public function __construct(ProtoClient $client, StreamInterface $stream, $messageCode)
    {
        $this->client      = $client;
        $this->stream      = $stream;
        $this->messageCode = $messageCode;
    }

    /**
     * @return \DrSlump\Protobuf\Message
     */
    protected function readNext()
    {
        return $this->client->receiveMessage($this->stream, $this->messageCode);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->count   = $this->count + 1;
        $this->current = $this->readNext();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if ($this->current != null) {
            throw new \RuntimeException('A streaming iterator cannot be rewind');
        }

        $this->count   = 0;
        $this->current = $this->readNext();
    }
}
