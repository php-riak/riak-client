<?php

namespace Riak\Client\Core\Query;

use Riak\Client\Core\Query\Link\RiakLink;
use Riak\Client\Core\Query\Index\RiakIndex;
use Riak\Client\Core\Query\Link\RiakLinkList;
use Riak\Client\Core\Query\Meta\RiakUsermeta;
use Riak\Client\Core\Query\Index\RiakIndexList;

/**
 * Represents the data and metadata stored in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakObject
{
    /**
     * The default content type assigned when storing in Riak if one is not
     */
    const DEFAULT_CONTENT_TYPE = "application/octet-stream";

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $vtag;

    /**
     * @var boolean
     */
    private $isDeleted;

    /**
     * @var boolean
     */
    private $isModified;

    /**
     * @var \Riak\Client\Core\Query\VClock
     */
    private $vClock;

    /**
     * @var integer
     */
    private $lastModified;

    /**
     * @var \Riak\Client\Core\Query\Index\RiakIndexList
     */
    private $indexes;

    /**
     * @var \Riak\Client\Core\Query\Link\RiakLinkList
     */
    private $links;

    /**
     * @var \Riak\Client\Core\Query\Meta\RiakUsermeta
     */
    private $meta;

    /**
     * @param string                         $value
     * @param string                         $contentType
     * @param \Riak\Client\Core\Query\VClock $vClock
     */
    public function __construct($value = null, $contentType = null, VClock $vClock = null)
    {
        $this->value       = $value;
        $this->vClock      = $vClock;
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return \Riak\Client\Core\Query\Index\RiakIndexList
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @return \Riak\Client\Core\Query\Link\RiakLinkList
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @return \Riak\Client\Core\Query\Meta\RiakUsermeta
     */
    public function getUserMeta()
    {
        return $this->meta;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getVtag()
    {
        return $this->vtag;
    }

    /**
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @return boolean
     */
    public function getIsModified()
    {
        return $this->isModified;
    }

    /**
     * @return string
     */
    public function getVClock()
    {
        return $this->vClock;
    }

    /**
     * @return integer
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param \Riak\Client\Core\Query\Index\RiakIndexList $indexes
     */
    public function setIndexes(RiakIndexList $indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * @param \Riak\Client\Core\Query\Index\RiakIndex $index
     */
    public function addIndex(RiakIndex $index)
    {
        if ($this->indexes === null) {
            $this->indexes = new RiakIndexList();
        }

        $this->indexes->addIndex($index);
    }

    /**
     * @param \Riak\Client\Core\Query\Link\RiakLinkList $links
     */
    public function setLinks(RiakLinkList $links)
    {
        $this->links = $links;
    }

    /**
     * @param \Riak\Client\Core\Query\Link\RiakLink $link
     */
    public function addLink(RiakLink $link)
    {
        if ($this->links === null) {
            $this->links = new RiakLinkList();
        }

        $this->links->addLink($link);
    }

    /**
     * @param \Riak\Client\Core\Query\Meta\RiakUsermeta $meta
     */
    public function setUserMeta(RiakUsermeta $meta)
    {
        $this->meta = $meta;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addMeta($key, $value)
    {
        if ($this->meta === null) {
            $this->meta = new RiakUsermeta();
        }

        $this->meta->put($key, $value);
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @param string $vtag
     */
    public function setVtag($vtag)
    {
        $this->vtag = $vtag;
    }

    /**
     * @param boolean $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @param boolean $isModified
     */
    public function setIsModified($isModified)
    {
        $this->isModified = $isModified;
    }

    /**
     * @param string $vClock
     */
    public function setVClock($vClock)
    {
        $this->vClock = $vClock;
    }

    /**
     * @param integer $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }
}
