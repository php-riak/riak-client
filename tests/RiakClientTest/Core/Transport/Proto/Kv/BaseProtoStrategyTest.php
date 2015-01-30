<?php

namespace RiakClientTest\Core\Transport\Proto\Kv;

use PhpOption\Option;
use RiakClientTest\TestCase;

class BaseProtoStrategyTest extends TestCase
{
     /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Kv\BaseProtoStrategy
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = $this->getMockForAbstractClass(
            'Riak\Client\Core\Transport\Proto\Kv\BaseProtoStrategy',
            [$this->client], '', true, true, true, ['send']
        );
    }

    public function testCreateContentList()
    {
        $rpbContent1 = $this->getMock('Riak\Client\ProtoBuf\RpbContent', [], [], '', false);
        $rpbContent2 = $this->getMock('Riak\Client\ProtoBuf\RpbContent', [], [], '', false);

        $rpbContent1->expects($this->once())
            ->method('getContentType')
            ->willReturn(Option::fromValue('application/json'));

        $rpbContent2->expects($this->once())
            ->method('getContentType')
            ->willReturn(Option::fromValue('application/json'));

        $rpbContent1->expects($this->once())
            ->method('getLastMod')
            ->willReturn(Option::fromValue(1420229384));

        $rpbContent2->expects($this->once())
            ->method('getLastMod')
            ->willReturn(Option::fromValue(1420229377));

        $rpbContent1->expects($this->once())
            ->method('getVtag')
            ->willReturn(Option::fromValue('vtag-hash'));

        $rpbContent2->expects($this->once())
            ->method('getVtag')
            ->willReturn(Option::fromValue('vtag-hash'));

        $rpbContent1->expects($this->once())
            ->method('getValue')
            ->willReturn('[1,1,1]');

        $rpbContent2->expects($this->once())
            ->method('getValue')
            ->willReturn('[2,2,2]');

        $rpbContent1->expects($this->once())
            ->method('getIndexesList')
            ->willReturn([]);

        $rpbContent2->expects($this->once())
            ->method('getIndexesList')
            ->willReturn([]);

        $rpbContent1->expects($this->once())
            ->method('getUsermetaList')
            ->willReturn([]);

        $rpbContent2->expects($this->once())
            ->method('getUsermetaList')
            ->willReturn([]);

        $rpbContent1->expects($this->once())
            ->method('getLinksList')
            ->willReturn([]);

        $rpbContent2->expects($this->once())
            ->method('getLinksList')
            ->willReturn([]);

        $contentList = $this->invokeMethod($this->instance, 'createContentList', [[$rpbContent1, $rpbContent2]]);

        $this->assertCount(2, $contentList);
        $this->assertEquals('[1,1,1]', $contentList[0]->value);
        $this->assertEquals('[2,2,2]', $contentList[1]->value);
        $this->assertEquals(1420229384, $contentList[0]->lastModified);
        $this->assertEquals(1420229377, $contentList[1]->lastModified);
        $this->assertEquals('application/json', $contentList[0]->contentType);
        $this->assertEquals('application/json', $contentList[1]->contentType);
    }
}