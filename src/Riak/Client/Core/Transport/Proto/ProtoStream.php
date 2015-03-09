<?php

namespace Riak\Client\Core\Transport\Proto;

/**
 * RPB socket stream
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoStream
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Finds whether a resource is a resource
     *
     * @return boolean
     */
    public function isResource()
    {
        return is_resource($this->resource);
    }

    /**
     * Seek to a position in the stream
     *
     * @param integer $offset Stream offset
     * @param integer $whence Where the offset is applied
     *
     * @return boolean
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->resource, $offset, $whence) === 0;
    }

    /**
     * Read data from the stream
     *
     * @param integer $length Up to length number of bytes read.
     *
     * @return string|boolean
     */
    public function read($length)
    {
        return fread($this->resource, $length);
    }

    /**
     * Write data to the stream
     *
     * @param string $string The string that is to be written.
     *
     * @return integer|boolean
     */
    public function write($string)
    {
        return fwrite($this->resource, $string);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if ( ! $this->resource) {
            return '';
        }

        $this->seek(0);

        return (string) stream_get_contents($this->resource);
    }
}
