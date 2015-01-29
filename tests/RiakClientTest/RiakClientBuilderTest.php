<?php

namespace RiakClientTest;

use Riak\Client\RiakClientBuilder;

class RiakClientBuilderTest extends TestCase
{
    /**
     * @var \Riak\Client\RiakClientBuilder
     */
    private $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new RiakClientBuilder();
    }

    public function testBuildWithHttpNode()
    {
        $client = $this->builder
            ->withNodeUri('http://localhost:8098')
            ->build();

        $this->assertInstanceOf('Riak\Client\RiakClient', $client);

        $cluster = $client->getCluster();
        $config  = $client->getConfig();
        $nodes   = $cluster->getNodes();

        $this->assertCount(1, $nodes);
        $this->assertInstanceOf('Riak\Client\RiakConfig', $config);
        $this->assertSame($config, $cluster->getRiakConfig());
        $this->assertInstanceOf('Riak\Client\Core\RiakNode', $nodes[0]);
        $this->assertInstanceOf('Riak\Client\Core\RiakCluster', $cluster);
        $this->assertInstanceOf('Riak\Client\Core\RiakHttpTransport', $nodes[0]->getAdapter());
        $this->assertInstanceOf('Riak\Client\Converter\ConverterFactory', $config->getConverterFactory());
        $this->assertInstanceOf('Riak\Client\Converter\Hydrator\DomainHydrator', $config->getDomainHydrator());
        $this->assertInstanceOf('Riak\Client\Converter\RiakObjectConverter', $config->getRiakObjectConverter());
        $this->assertInstanceOf('Riak\Client\Converter\Hydrator\DomainMetadataReader', $config->getDomainMetadataReader());
    }

    public function testBuildWithNode()
    {
        $node                 = $this->getMock('Riak\Client\Core\RiakNode', [], [], '', false);
        $cluster              = $this->getMock('Riak\Client\Core\RiakCluster', [], [], '', false);
        $converterFactory     = $this->getMock('Riak\Client\Converter\ConverterFactory', [], [], '', false);
        $objectConverter      = $this->getMock('Riak\Client\Converter\RiakObjectConverter', [], [], '', false);
        $domainHydrator       = $this->getMock('Riak\Client\Converter\Hydrator\DomainHydrator', [], [], '', false);
        $domainMetadataReader = $this->getMock('Riak\Client\Converter\Hydrator\DomainMetadataReader', [], [], '', false);

        $cluster->expects($this->once())
            ->method('setNodes')
            ->with($this->equalTo([$node]));

        $client = $this->builder
            ->withDomainMetadataReader($domainMetadataReader)
            ->withRiakObjectConverter($objectConverter)
            ->withConverterFactory($converterFactory)
            ->withDomainHydrator($domainHydrator)
            ->withCluster($cluster)
            ->withNode($node)
            ->build();

        $this->assertInstanceOf('Riak\Client\RiakClient', $client);

        $riakCluster = $client->getCluster();
        $riakConfig  = $client->getConfig();

        $this->assertSame($cluster, $riakCluster);
        $this->assertSame($riakConfig, $riakConfig);
        $this->assertSame($domainHydrator, $riakConfig->getDomainHydrator());
        $this->assertSame($converterFactory, $riakConfig->getConverterFactory());
        $this->assertSame($objectConverter, $riakConfig->getRiakObjectConverter());
        $this->assertSame($domainMetadataReader, $riakConfig->getDomainMetadataReader());
    }

    public function testBuildWithConflictResolver()
    {
        $resolver = $this->getMock('Riak\Client\Resolver\ConflictResolver');
        $client   = $this->builder
            ->withConflictResolver('stdClass', $resolver)
            ->withNodeUri('http://localhost:8098')
            ->build();

        $this->assertInstanceOf('Riak\Client\RiakClient', $client);

        $config  = $client->getConfig();
        $factory = $config->getResolverFactory();

        $this->assertInstanceOf('Riak\Client\Resolver\ResolverFactory', $factory);
        $this->assertSame($resolver, $factory->getResolver('stdClass'));
    }

    public function testBuildWithConverter()
    {
        $converter = $this->getMock('Riak\Client\Converter\Converter');
        $client    = $this->builder
            ->withConverter('stdClass', $converter)
            ->withNodeUri('http://localhost:8098')
            ->build();

        $this->assertInstanceOf('Riak\Client\RiakClient', $client);

        $config  = $client->getConfig();
        $factory = $config->getConverterFactory();

        $this->assertInstanceOf('Riak\Client\Converter\ConverterFactory', $factory);
        $this->assertSame($converter, $factory->getConverter('stdClass'));
    }

    public function testBuildWithCrdtResponseConverter()
    {
        $converter = $this->getMock('Riak\Client\Converter\CrdtResponseConverter');
        $client    = $this->builder
            ->withCrdtResponseConverter($converter)
            ->withNodeUri('http://localhost:8098')
            ->build();

        $this->assertInstanceOf('Riak\Client\RiakClient', $client);

        $config = $client->getConfig();
        $result = $config->getCrdtResponseConverter();

        $this->assertInstanceOf('Riak\Client\Converter\CrdtResponseConverter', $result);
        $this->assertSame($converter, $result);
    }

    public function testBuildWithResolverFactory()
    {
        $factory = $this->getMock('Riak\Client\Resolver\ResolverFactory');
        $client  = $this->builder
            ->withResolverFactory($factory)
            ->withNodeUri('http://localhost:8098')
            ->build();

        $this->assertInstanceOf('Riak\Client\RiakClient', $client);

        $config = $client->getConfig();
        $result = $config->getResolverFactory();

        $this->assertInstanceOf('Riak\Client\Resolver\ResolverFactory', $result);
        $this->assertSame($factory, $result);
    }

    public function testBuildWithConfig()
    {
        $bulder  = new RiakClientBuilder();
        $config  = $bulder->getConfig();
        $client  = $this->builder
            ->withConfig($config)
            ->withNodeUri('http://localhost:8098')
            ->build();

        $this->assertInstanceOf('Riak\Client\RiakClient', $client);
        $this->assertSame($config, $client->getConfig());
    }
}
