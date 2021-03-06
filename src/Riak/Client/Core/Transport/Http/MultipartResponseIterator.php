<?php

namespace Riak\Client\Core\Transport\Http;

use InvalidArgumentException;
use Riak\Client\Core\RiakIterator;
use GuzzleHttp\Message\MessageParser;
use GuzzleHttp\Message\MessageFactory;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Message\MessageFactoryInterface;

/**
 * Multipart stream parser iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MultipartResponseIterator extends RiakIterator
{
    /**
     * @var \GuzzleHttp\Message\ResponseInterface
     */
    private $response;

    /**
     * @var \Riak\Client\Core\Transport\Http\MultipartStreamIterator
     */
    private $iterator;

    /**
     * @var \GuzzleHttp\Message\MessageParser
     */
    private $parser;

    /**
     * @var \GuzzleHttp\Message\MessageFactoryInterface
     */
    private $factory;

    /**
     * @param \GuzzleHttp\Message\ResponseInterface       $response
     * @param \GuzzleHttp\Message\MessageParser           $parser
     * @param \GuzzleHttp\Message\MessageFactoryInterface $messageFactory
     */
    public function __construct(ResponseInterface $response, MessageParser $parser = null, MessageFactoryInterface $messageFactory = null)
    {
        $matches = null;
        $body    = $response->getBody();
        $header  = $response->getHeader('Content-Type');

        if ( ! preg_match('/boundary=(.*)$/', $header, $matches) || ! isset($matches[1])) {
            throw new InvalidArgumentException("Unable to parse boundary from content type : '$header'");
        }

        $this->response = $response;
        $this->parser   = $parser ?: new MessageParser();
        $this->factory  = $messageFactory ?: new MessageFactory();
        $this->iterator = new MultipartStreamIterator($body, $matches[1]);
    }

    /**
     * {@inheritdoc}
     */
    protected function readNext()
    {
        if ( ! $this->iterator->valid()) {
            return;
        }

        $message  = $this->iterator->current();
        $code     = $this->response->getStatusCode();
        $content  = sprintf("HTTP/1.1 300\r\n %s", $message);
        $element  = $this->parser->parseResponse($content);
        $current  = $this->factory->createResponse($code, $element['headers'], $element['body']);

        return $current;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();

        parent::next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();

        parent::rewind();
    }
}
