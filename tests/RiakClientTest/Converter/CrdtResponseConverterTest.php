<?php

namespace RiakClientTest\Converter;

use RiakClientTest\TestCase;
use Riak\Client\Core\Message\DataType\Response;
use Riak\Client\Converter\CrdtResponseConverter;

class CrdtResponseConverterTest extends TestCase
{
    /**
     * @var \Riak\Client\Converter\ConverterFactory
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = new CrdtResponseConverter();
    }

    public function testConvert()
    {
        $setResponse     = new Response();
        $mapResponse     = new Response();
        $nullResponse    = new Response();
        $counterResponse = new Response();

        $setResponse->type      = 'set';
        $mapResponse->type      = 'map';
        $nullResponse->type     = null;
        $counterResponse->type  = 'counter';

        $setResponse->value     = [];
        $mapResponse->value     = [];
        $nullResponse->value    = null;
        $counterResponse->value = 0;

        $setResult      = $this->instance->convert($setResponse);
        $mapResult      = $this->instance->convert($mapResponse);
        $nullResult     = $this->instance->convert($nullResponse);
        $counterResult  = $this->instance->convert($counterResponse);

        $this->assertNull($nullResult);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakSet', $setResult);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakMap', $mapResult);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakCounter', $counterResult);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown crdt type : UNKNOWN
     */
    public function testUnknownCrdtException()
    {
        $response = new Response();

        $response->type = 'UNKNOWN';

        $this->instance->convert($response);
    }
}