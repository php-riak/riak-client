<?php

namespace Riak\Client\Converter;

use Riak\Client\Core\Query\VClock;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\Message\Kv\Content;
use Riak\Client\Core\Query\Link\RiakLink;
use Riak\Client\Core\Query\RiakObjectList;
use Riak\Client\Core\Query\Index\RiakIndex;
use Riak\Client\Core\Query\Meta\RiakUsermeta;
use Riak\Client\Core\Query\Link\RiakLinkList;
use Riak\Client\Core\Query\Index\RiakIndexList;

/**
 * Riak object convertert.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakObjectConverter
{
    /**
     * @param array  $siblingsList
     * @param string $vClockString
     *
     * @return \Riak\Client\Core\Query\RiakObjectList
     */
    public function convertToRiakObjectList(array $siblingsList, $vClockString)
    {
        $list   = [];
        $vClock = new VClock($vClockString);

        foreach ($siblingsList as $content) {
            $list[] = $this->convertToRiakObject($content, $vClock);
        }

        return new RiakObjectList($list);
    }

    /**
     * @param \Riak\Client\Core\Message\Kv\Content $content
     * @param \Riak\Client\Core\Query\VClock       $vClock
     *
     * @return \Riak\Client\Core\Query\RiakObject
     */
    private function convertToRiakObject(Content $content, VClock $vClock)
    {
        $object = new RiakObject();

        $object->setVClock($vClock);
        $object->setVtag($content->vtag);
        $object->setValue($content->value);
        $object->setContentType($content->contentType);
        $object->setIsDeleted((bool) $content->deleted);
        $object->setLastModified($content->lastModified);

        if ($content->indexes) {
            $object->setIndexes($this->createRiakIndexList($content->indexes));
        }

        if ($content->metas) {
            $object->setUserMeta(new RiakUsermeta($content->metas));
        }

        if ($content->links) {
            $object->setLinks($this->createRiakLinkList($content->links));
        }

        return $object;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakObject $riakObject
     *
     * @return array
     */
    public function convertToRiakContent(RiakObject $riakObject)
    {
        $content = new Content();
        $metas   = $riakObject->getUserMeta();
        $indexes = $riakObject->getIndexes();
        $links   = $riakObject->getLinks();

        $content->contentType  = $riakObject->getContentType() ?: RiakObject::DEFAULT_CONTENT_TYPE;
        $content->lastModified = $riakObject->getLastModified();
        $content->isDeleted    = $riakObject->getIsDeleted();
        $content->value        = $riakObject->getValue();
        $content->vtag         = $riakObject->getVtag();
        $content->indexes      = [];
        $content->metas        = [];

        if ($indexes != null) {
            $content->indexes = $indexes->toFullNameArray();
        }

        if ($metas != null) {
            $content->metas = $metas->toArray();
        }

        if ($links != null) {
            $content->links = $links->toArray();
        }

        return $content;
    }

    /**
     * @param array $indexes
     *
     * @return \Riak\Client\Core\Query\Index\RiakIndexList
     */
    private function createRiakIndexList(array $indexes)
    {
        $list = [];

        foreach ($indexes as $fullName => $values) {
            $list[] = RiakIndex::fromFullname($fullName, $values);
        }

        return new RiakIndexList($list);
    }

    /**
     * @param array $links
     *
     * @return \Riak\Client\Core\Query\Link\RiakLinkList
     */
    private function createRiakLinkList(array $links)
    {
        return new RiakLinkList(array_map(function (array $l) {
            return RiakLink::fromArray($l);
        }, $links));
    }
}
