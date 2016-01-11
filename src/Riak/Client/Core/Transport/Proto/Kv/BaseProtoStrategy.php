<?php

namespace Riak\Client\Core\Transport\Proto\Kv;

use Riak\Client\ProtoBuf\RpbContent;
use Riak\Client\Core\Message\Kv\Content;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;

/**
 * Base rpb strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class BaseProtoStrategy extends ProtoStrategy
{
    /**
     * @param \Riak\Client\ProtoBuf\RpbContent $rpbcontent
     *
     * @return \Riak\Client\Core\Message\Kv\Content
     */
    private function createContent(RpbContent $rpbcontent)
    {
        $content  = new Content();
        $indexes  = $rpbcontent->getIndexesList() ?: [];
        $usermeta = $rpbcontent->getUsermetaList() ?: [];
        $links    = $rpbcontent->getLinksList() ?: [];

        $content->contentType  = $rpbcontent->getContentType();
        $content->lastModified = $rpbcontent->getLastMod();
        $content->value        = $rpbcontent->getValue();
        $content->vtag         = $rpbcontent->getVtag();
        $content->indexes      = [];
        $content->metas        = [];

        /** @var $index \Riak\Client\ProtoBuf\RpbPair */
        foreach ($indexes as $index) {
            $key   = $index->getKey()->getContents();
            $value = $index->getValue()->getContents();

            $content->indexes[$key][] = $value;
        }

        /** @var $index \Riak\Client\ProtoBuf\RpbPair */
        foreach ($usermeta as $meta) {
            $key   = $meta->getKey()->getContents();
            $value = $meta->getValue()->getContents();

            $content->metas[$key] = $value;
        }

        /** @var $index \Riak\Client\ProtoBuf\RpbLink */
        foreach ($links as $link) {
            $content->links[] = [
                'bucket' => $link->getBucket()->getContents(),
                'key'    => $link->getKey()->getContents(),
                'tag'    => $link->getTag()->getContents()
            ];
        }

        return $content;
    }

    /**
     * @param \Riak\Client\ProtoBuf\RpbContent[] $contentList
     *
     * @return \Riak\Client\Core\Message\Kv\Content[]
     */
    protected function createContentList($contentList)
    {
        $result = [];

        foreach ($contentList as $value) {
            $result[] = $this->createContent($value);
        }

        return $result;
    }
}
