<?php

namespace RiakClientTest\Core\Transport\Proto\DataType;

use Riak\Client\Core\Transport\Proto\DataType\CrdtOpConverter;
use Riak\Client\ProtoBuf\MapField\MapFieldType;
use Riak\Client\Core\Query\Crdt\Op;
use RiakClientTest\TestCase;
use Riak\Client\ProtoBuf;

class CrdtOpConverterTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\DataType\CrdtOpConverter
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = new CrdtOpConverter();
    }

    public function testConvertFlag()
    {
        $op     = new Op\FlagOp(true);
        $result = $this->invokeMethod($this->instance, 'convertFlag', [$op]);

        $this->assertEquals(ProtoBuf\MapUpdate\FlagOp::ENABLE(), $result);
    }

    public function testConvertCounter()
    {
        $op     = new Op\CounterOp(10);
        $result = $this->invokeMethod($this->instance, 'convertCounter', [$op]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\CounterOp', $result);
        $this->assertEquals(10, $result->getIncrement());
    }

    public function testConvertSet()
    {
        $op     = new Op\SetOp(['1','2'], ['3','4']);
        $result = $this->invokeMethod($this->instance, 'convertSet', [$op]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\SetOp', $result);
        $this->assertCount(2, $result->getRemovesList());
        $this->assertCount(2, $result->getAddsList());

        $this->assertEquals('3', (string) $result->getRemovesList()[0]);
        $this->assertEquals('4', (string) $result->getRemovesList()[1]);

        $this->assertEquals('1', (string) $result->getAddsList()[0]);
        $this->assertEquals('2', (string) $result->getAddsList()[1]);
    }

    public function testConvertMap()
    {
        $updates = [
            'register'  => [
                'register_update' => new Op\RegisterOp('Register Value')
            ],
            'counter'   => [
                'counter_update' => new Op\CounterOp(10)
            ],
            'flag'      => [
                'flag_update' => new Op\FlagOp(true)
            ],
            'set'       => [
                'set_update' => new Op\SetOp([1,2], [3,4])
            ],
            'map'       => [
                'map_update' => new Op\MapOp([], [])
            ],
        ];

        $removes = [
            'register'  => [
                'register_remove' => 'register_remove'
            ],
            'counter'   => [
                'counter_remove' => 'counter_remove'
            ],
            'flag'      => [
                'flag_remove' => 'flag_remove'
            ],
            'set'       => [
                'set_remove' => 'set_remove'
            ],
            'map'       => [
                'map_remove' => 'map_remove'
            ],
        ];

        $op      = new Op\MapOp($updates, $removes);
        $result  = $this->invokeMethod($this->instance, 'convertMap', [$op]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapOp', $result);
        $this->assertCount(5, $result->getUpdatesList());
        $this->assertCount(5, $result->getRemovesList());

        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapUpdate', $result->getUpdatesList()[0]);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapUpdate', $result->getUpdatesList()[1]);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapUpdate', $result->getUpdatesList()[2]);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapUpdate', $result->getUpdatesList()[3]);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapUpdate', $result->getUpdatesList()[4]);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapField', $result->getRemovesList()[0]);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapField', $result->getRemovesList()[1]);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapField', $result->getRemovesList()[2]);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapField', $result->getRemovesList()[3]);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapField', $result->getRemovesList()[4]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapField', $result->getUpdatesList()[0]->getField());
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapField', $result->getUpdatesList()[1]->getField());
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapField', $result->getUpdatesList()[2]->getField());
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapField', $result->getUpdatesList()[3]->getField());
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapField', $result->getUpdatesList()[4]->getField());

        $this->assertEquals('map_update', $result->getUpdatesList()[0]->getField()->getName());
        $this->assertEquals('set_update', $result->getUpdatesList()[1]->getField()->getName());
        $this->assertEquals('flag_update', $result->getUpdatesList()[2]->getField()->getName());
        $this->assertEquals('counter_update', $result->getUpdatesList()[3]->getField()->getName());
        $this->assertEquals('register_update', $result->getUpdatesList()[4]->getField()->getName());

        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapOp', $result->getUpdatesList()[0]->getMapOp());
        $this->assertInstanceOf('Riak\Client\ProtoBuf\SetOp', $result->getUpdatesList()[1]->getSetOp());
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapUpdate\FlagOp', $result->getUpdatesList()[2]->getFlagOp());
        $this->assertInstanceOf('Riak\Client\ProtoBuf\CounterOp', $result->getUpdatesList()[3]->getCounterOp());
        $this->assertInstanceOf('Protobuf\Stream', $result->getUpdatesList()[4]->getRegisterOp());

        $this->assertEquals(1, (int) (string) $result->getUpdatesList()[1]->getSetOp()->getAddsList()[0]);
        $this->assertEquals(2, (int) (string) $result->getUpdatesList()[1]->getSetOp()->getAddsList()[1]);

        $this->assertEquals(3, (int) (string) $result->getUpdatesList()[1]->getSetOp()->getRemovesList()[0]);
        $this->assertEquals(4, (int) (string) $result->getUpdatesList()[1]->getSetOp()->getRemovesList()[1]);

        $this->assertEquals(ProtoBuf\MapUpdate\FlagOp::ENABLE(), $result->getUpdatesList()[2]->getFlagOp());
        $this->assertEquals(10, $result->getUpdatesList()[3]->getCounterOp()->getIncrement());
        $this->assertEquals('Register Value', $result->getUpdatesList()[4]->getRegisterOp());

        $this->assertEquals('map_remove', $result->getRemovesList()[0]->getName());
        $this->assertEquals('set_remove', $result->getRemovesList()[1]->getName());
        $this->assertEquals('flag_remove', $result->getRemovesList()[2]->getName());
        $this->assertEquals('counter_remove', $result->getRemovesList()[3]->getName());
        $this->assertEquals('register_remove', $result->getRemovesList()[4]->getName());
    }

    public function testConvert()
    {
        $setResult      = $this->instance->toProtoBuf(new Op\SetOp([],[]));
        $mapResult      = $this->instance->toProtoBuf(new Op\MapOp([],[]));
        $counterResult  = $this->instance->toProtoBuf(new Op\CounterOp(0));

        $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $setResult);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $mapResult);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $counterResult);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\SetOp', $setResult->getSetOp());
        $this->assertInstanceOf('Riak\Client\ProtoBuf\MapOp', $mapResult->getmapOp());
        $this->assertInstanceOf('Riak\Client\ProtoBuf\CounterOp', $counterResult->getcounterOp());
    }

    public function testConvertMapEntry()
    {
        $setEntry = new ProtoBuf\MapEntry();
        $setField = new ProtoBuf\MapField();

        $mapEntry = new ProtoBuf\MapEntry();
        $mapField = new ProtoBuf\MapField();

        $flagEntry = new ProtoBuf\MapEntry();
        $flagField = new ProtoBuf\MapField();

        $counterEntry = new ProtoBuf\MapEntry();
        $counterField = new ProtoBuf\MapField();

        $registerEntry = new ProtoBuf\MapEntry();
        $registerField = new ProtoBuf\MapField();

        $mapEntryValue = new ProtoBuf\MapEntry();
        $mapEntryValue->setField(new ProtoBuf\MapField());
        $mapEntryValue->getField()->setName('sub_map_field');
        $mapEntryValue->getField()->setType(MapFieldType::REGISTER());
        $mapEntryValue->setRegisterValue('sub-map-register-val');

        $mapEntry->setField($mapField);
        $mapEntry->addMapValue($mapEntryValue);
        $mapField->setName('map_field');
        $mapField->setType(MapFieldType::MAP());


        $setEntry->addSetValue('1');
        $setEntry->addSetValue('2');
        $setEntry->addSetValue('3');
        $setEntry->setField($setField);
        $setField->setName('set_field');
        $setField->setType(MapFieldType::SET());

        $flagEntry->setField($flagField);
        $flagEntry->setFlagValue(ProtoBuf\MapUpdate\FlagOp::ENABLE());
        $flagField->setName('flag_field');
        $flagField->setType(MapFieldType::FLAG());

        $counterEntry->setField($counterField);
        $counterEntry->setCounterValue(10);
        $counterField->setName('counter_field');
        $counterField->setType(MapFieldType::COUNTER());

        $registerEntry->setField($registerField);
        $registerEntry->setRegisterValue('register-val');
        $registerField->setName('register_field');
        $registerField->setType(MapFieldType::REGISTER());

        $mapResult      = $this->instance->convertMapEntry($mapEntry);
        $setResult      = $this->instance->convertMapEntry($setEntry);
        $flagResult     = $this->instance->convertMapEntry($flagEntry);
        $counterResult  = $this->instance->convertMapEntry($counterEntry);
        $registerResult = $this->instance->convertMapEntry($registerEntry);

        $this->assertTrue($flagResult);
        $this->assertEquals(10, $counterResult);
        $this->assertEquals('register-val', $registerResult);
        $this->assertArrayHasKey('sub_map_field', $mapResult);
        $this->assertEquals('sub-map-register-val', $mapResult['sub_map_field']);

        $this->assertEquals(1, (int) (string) $setResult[0]);
        $this->assertEquals(2, (int) (string) $setResult[1]);
        $this->assertEquals(3, (int) (string) $setResult[2]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown crdt field type : UNKNOWN
     */
    public function testConvertUnknownMapEntryException()
    {
        $entry = new ProtoBuf\MapEntry();
        $field = new ProtoBuf\MapField();

        $entry->setField($field);
        $field->setType(new MapFieldType('UNKNOWN', 1));

        $this->instance->convertMapEntry($entry);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConvertUnknownDataTypeOpException()
    {
        $crdtOp = $this->getMock('Riak\Client\Core\Query\Crdt\Op\CrdtOp');

        $this->instance->toProtoBuf($crdtOp);
    }
}