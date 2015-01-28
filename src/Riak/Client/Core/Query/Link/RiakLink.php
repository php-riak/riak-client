<?php

namespace Riak\Client\Core\Query\Link;

/**
 * Represents a link from one object to another in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakLink
{
    /**
     * @var string
     */
    private $bucket;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $tag;

    /**
     * @param string $bucket
     * @param string $key
     * @param string $tag
     */
    public function __construct($bucket, $key, $tag)
    {
        $this->bucket = $bucket;
        $this->key    = $key;
        $this->tag    = $tag;
    }

    /**
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param string $bucket
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'bucket' => $this->bucket,
            'key'    => $this->key,
            'tag'    => $this->tag,
        ];
    }

    /**
     * @param array $values
     *
     * @return \Riak\Client\Core\Query\Link\RiakLink
     */
    public static function fromArray(array $values)
    {
        $bucket = isset($values['bucket']) ? $values['bucket'] : null;
        $key    = isset($values['key']) ? $values['key'] : null;
        $tag    = isset($values['tag']) ? $values['tag'] : null;

        return new RiakLink($bucket, $key, $tag);
    }
}
