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
        $content = new Content();

        $content->contentType  = $rpbcontent->getContentType()->get();
        $content->lastModified = $rpbcontent->getLastMod()->get();
        $content->vtag         = $rpbcontent->getVtag()->get();
        $content->value        = $rpbcontent->getValue();
        $content->indexes      = [];
        $content->metas        = [];

        /** @var $index \Riak\Client\ProtoBuf\RpbPair */
        foreach ($rpbcontent->getIndexesList() as $index) {
            $key   = $index->getKey();
            $value = $index->getValue()->get();

            $content->indexes[$key][] = $value;
        }

        /** @var $index \Riak\Client\ProtoBuf\RpbPair */
        foreach ($rpbcontent->getUsermetaList() as $meta) {
            $key   = $meta->getKey();
            $value = $meta->getValue()->get();

            $content->metas[$key] = $value;
        }

        /** @var $index \Riak\Client\ProtoBuf\RpbLink */
        foreach ($rpbcontent->getLinksList() as $link) {
            $content->links[] = [
                'bucket' => $link->bucket,
                'key'    => $link->key,
                'tag'    => $link->tag,
            ];
        }

        return $content;
    }

    /**
     * @param \Riak\Client\ProtoBuf\RpbContent[] $contentList
     *
     * @return \Riak\Client\Core\Message\Kv\Content[]
     */
    protected function createContentList(array $contentList)
    {
        return array_map([$this, 'createContent'], $contentList);
    }
}
