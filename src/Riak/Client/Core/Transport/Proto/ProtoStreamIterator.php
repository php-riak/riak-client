<?php

namespace Riak\Client\Core\Transport\Proto;

use GuzzleHttp\Stream\StreamInterface;
use Riak\Client\Core\RiakIterator;
use RuntimeException;

/**
 * RPB streaming iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoStreamIterator extends RiakIterator
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
    public function rewind()
    {
        if ($this->current != null) {
            throw new RuntimeException('A streaming iterator cannot be rewinded.');
        }

        parent::rewind();
    }
}
