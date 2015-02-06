<?php

namespace Riak\Client\Core\Transport\Http;

use GuzzleHttp\Stream\Stream;
use Riak\Client\Core\RiakIterator;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Multipart stream iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MultipartStreamIterator extends RiakIterator
{
    /**
     * @var \GuzzleHttp\Stream\StreamInterface
     */
    private $stream;

    /**
     * @var string
     */
    private $boundaryItem;

    /**
     * @var string
     */
    private $boundaryEnd;

    /**
     * @param \GuzzleHttp\Stream\StreamInterface $stream
     * @param string                             $boundary
     */
    public function __construct(StreamInterface $stream, $boundary)
    {
        if ($boundary == null) {
            throw new \InvalidArgumentException("Boundary cannot be null");
        }

        $this->stream       = $stream;
        $this->boundaryItem = "--$boundary";
        $this->boundaryEnd  = "--$boundary--";
    }

    /**
     * @return string
     */
    private function readLine()
    {
        $buffer = '';

        while ( ! $this->stream->eof()) {
            $buffer .= $this->stream->read(1);

            if (substr($buffer, -2) !== "\r\n") {
                continue;
            }

            return substr($buffer, 0, -2);
        }

        return $buffer;
    }

    /**
     * @param string $line
     *
     * @return boolean
     */
    private function isBoundary($line)
    {
        return ($line === $this->boundaryItem);
    }

    /**
     * @param string $line
     *
     * @return boolean
     */
    private function isLastBoundary($line)
    {
        return ($line === $this->boundaryEnd);
    }

    /**
     * Move to next boundary
     */
    private function moveToNext()
    {
        while ( ! $this->stream->eof()) {
            if ($this->isBoundary($line = $this->readLine())) {
                break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function readNext()
    {
        $line   = null;
        $stream = Stream::factory();

        while ( ! $this->stream->eof()) {
            $line = $this->readLine();

            if ($this->isBoundary($line)) {
                break;
            }

            if ($this->isLastBoundary($line)) {
                // read last bytes
                $this->stream->read(2);
                break;
            }

            $stream->write("\r\n" . $line);
        }

        if ($line === null) {
            return;
        }

        $stream->seek(0);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->stream->seek(0);

        $this->moveToNext();

        parent::rewind();
    }
}
