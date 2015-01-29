<?php

namespace RiakClientTest\Command\DataType;

use RiakClientTest\TestCase;
use Riak\Client\Command\DataType\MapUpdate;

class MapUpdateTest extends TestCase
{
    public function testCreateFromArray()
    {
        $update = MapUpdate::createFromArray([
            'counter_key'   => 10,
            'flag_key'      => true,
            'set_key'       => [1,2,3],
            'register_key'  => "Register Val",
            'map_key'       => [
                'sub_counter_key'   => 10,
                'sub_flag_key'      => true,
                'sub_set_key'       => [1,2,3],
                'sub_register_key'  => "Register Val",
            ],
        ]);

        $this->assertInstanceOf('Riak\Client\Command\DataType\MapUpdate', $update);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\Op\MapOp', $update->getOp());

        $op              = $update->getOp();
        $mapUpdates      = $op->getMapUpdates();
        $setUpdates      = $op->getSetUpdates();
        $flagUpdates     = $op->getFlagUpdates();
        $counterUpdates  = $op->getCounterUpdates();
        $registerUpdates = $op->getRegisterUpdates();

        $this->assertCount(1, $mapUpdates);
        $this->assertCount(1, $setUpdates);
        $this->assertCount(1, $flagUpdates);
        $this->assertCount(1, $counterUpdates);
        $this->assertCount(1, $registerUpdates);

        $this->assertArrayHasKey('map_key', $mapUpdates);
        $this->assertArrayHasKey('set_key', $setUpdates);
        $this->assertArrayHasKey('flag_key', $flagUpdates);
        $this->assertArrayHasKey('counter_key', $counterUpdates);
        $this->assertArrayHasKey('register_key', $registerUpdates);

        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\Op\MapOp', $mapUpdates['map_key']);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\Op\SetOp', $setUpdates['set_key']);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\Op\FlagOp', $flagUpdates['flag_key']);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\Op\CounterOp', $counterUpdates['counter_key']);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\Op\RegisterOp', $registerUpdates['register_key']);

        $this->assertEquals("Register Val", $registerUpdates['register_key']->getValue());
        $this->assertEquals(10, $counterUpdates['counter_key']->getIncrement());
        $this->assertEquals([1,2,3], $setUpdates['set_key']->getAdds());
        $this->assertTrue($flagUpdates['flag_key']->isEnabled());

        $mapKeyOp              = $mapUpdates['map_key'];
        $mapKeySetUpdates      = $mapKeyOp->getSetUpdates();
        $mapKeyFlagUpdates     = $mapKeyOp->getFlagUpdates();
        $mapKeyCounterUpdates  = $mapKeyOp->getCounterUpdates();
        $mapKeyRegisterUpdates = $mapKeyOp->getRegisterUpdates();

        $this->assertCount(1, $mapKeySetUpdates);
        $this->assertCount(1, $mapKeyFlagUpdates);
        $this->assertCount(1, $mapKeyCounterUpdates);
        $this->assertCount(1, $mapKeyRegisterUpdates);

        $this->assertArrayHasKey('sub_set_key', $mapKeySetUpdates);
        $this->assertArrayHasKey('sub_flag_key', $mapKeyFlagUpdates);
        $this->assertArrayHasKey('sub_counter_key', $mapKeyCounterUpdates);
        $this->assertArrayHasKey('sub_register_key', $mapKeyRegisterUpdates);

        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\Op\SetOp', $mapKeySetUpdates['sub_set_key']);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\Op\FlagOp', $mapKeyFlagUpdates['sub_flag_key']);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\Op\CounterOp', $mapKeyCounterUpdates['sub_counter_key']);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\Op\RegisterOp', $mapKeyRegisterUpdates['sub_register_key']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Map element "invalid_key" must be of the type (boolean, string, integer, or an array), "stdClass" given.
     */
    public function testCreateWithInvalidArgumentException()
    {
        MapUpdate::createFromArray([
            'invalid_key' => new \stdClass(),
        ]);
    }
}