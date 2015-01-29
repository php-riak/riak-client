<?php

namespace Riak\Client;

use Doctrine\Common\Annotations\AnnotationReader;
use Riak\Client\Core\RiakNode;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Converter\Converter;
use Riak\Client\Core\RiakNodeBuilder;
use Riak\Client\Resolver\ResolverFactory;
use Riak\Client\Resolver\ConflictResolver;
use Riak\Client\Converter\ConverterFactory;
use Riak\Client\Converter\RiakObjectConverter;
use Riak\Client\Converter\CrdtResponseConverter;
use Riak\Client\Converter\Hydrator\DomainHydrator;
use Riak\Client\Converter\Hydrator\DomainMetadataReader;

/**
 * Build a riak client
 *
 * @todo split into smaller builders
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakClientBuilder
{
    /**
     * @var \Riak\Client\Converter\RiakObjectConverter
     */
    private $riakObjectConverter;

    /**
     * @var \Riak\Client\Converter\CrdtResponseConverter
     */
    private $crdtResponseConverter;

    /**
     * @var \Riak\Client\Converter\Hydrator\DomainHydrator
     */
    private $domainHydrator;

    /**
     * @var \Riak\Client\Converter\Hydrator\DomainMetadataReader
     */
    private $domainMetadataReader;

    /**
     * @var \Riak\Client\Converter\ConverterFactory
     */
    private $converterFactory;

    /**
     * @var \Riak\Client\Resolver\ResolverFactory
     */
    private $resolverFactory;

    /**
     * @var \Riak\Client\Core\RiakCluster
     */
    private $cluster;

    /**
     * @var \Riak\Client\RiakConfig
     */
    private $config;

    /**
     * @var array
     */
    private $nodes = [];

    /**
     * @var \Riak\Client\Resolver\ConflictResolver[]
     */
    private $resolvers = [];

    /**
     * @var \Riak\Client\Converter\Converter[]
     */
    private $converters = [];

    /**
     * @return \Riak\Client\Converter\RiakObjectConverter
     */
    private function getRiakObjectConverter()
    {
        if ($this->riakObjectConverter === null) {
            $this->riakObjectConverter = new RiakObjectConverter();
        }

        return $this->riakObjectConverter;
    }

    /**
     * @param \Riak\Client\Converter\RiakObjectConverter $converter
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withRiakObjectConverter(RiakObjectConverter $converter)
    {
        $this->riakObjectConverter = $converter;

        return $this;
    }

    /**
     * @return \Riak\Client\Converter\CrdtResponseConverter
     */
    public function getCrdtResponseConverter()
    {
        if ($this->crdtResponseConverter === null) {
            $this->crdtResponseConverter = new CrdtResponseConverter();
        }

        return $this->crdtResponseConverter;
    }

    /**
     * @param \Riak\Client\Converter\CrdtResponseConverter $converter
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withCrdtResponseConverter(CrdtResponseConverter $converter)
    {
        $this->crdtResponseConverter = $converter;

        return $this;
    }

    /**
     * @return \Riak\Client\Converter\Hydrator\DomainHydrator
     */
    public function getDomainHydrator()
    {
        if ($this->domainHydrator === null) {
            $this->domainHydrator = new DomainHydrator($this->getDomainMetadataReader());
        }

        return $this->domainHydrator;
    }

    /**
     * @param \Riak\Client\Converter\Hydrator\DomainHydrator $hydrator
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withDomainHydrator(DomainHydrator $hydrator)
    {
        $this->domainHydrator = $hydrator;

        return $this;
    }

    /**
     * @return \Riak\Client\Converter\Hydrator\DomainMetadataReader
     */
    public function getDomainMetadataReader()
    {
        if ($this->domainMetadataReader === null) {
            $this->domainMetadataReader = new DomainMetadataReader(new AnnotationReader());
        }

        return $this->domainMetadataReader;
    }

    /**
     * @param \Riak\Client\Converter\Hydrator\DomainMetadataReader $reader
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withDomainMetadataReader(DomainMetadataReader $reader)
    {
        $this->domainMetadataReader = $reader;

        return $this;
    }

    /**
     * @return \Riak\Client\Converter\ConverterFactory
     */
    public function getConverterFactory()
    {
        if ($this->converterFactory === null) {
            $this->converterFactory = new ConverterFactory($this->getDomainHydrator());
        }

        return $this->converterFactory;
    }

    /**
     * @param \Riak\Client\Converter\ConverterFactory $factory
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withConverterFactory(ConverterFactory $factory)
    {
        $this->converterFactory = $factory;

        return $this;
    }

    /**
     * @return \Riak\Client\Resolver\ResolverFactory
     */
    public function getResolverFactory()
    {
        if ($this->resolverFactory === null) {
            $this->resolverFactory = new ResolverFactory();
        }

        return $this->resolverFactory;
    }

    /**
     * @param \Riak\Client\Resolver\ResolverFactory $factory
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withResolverFactory(ResolverFactory $factory)
    {
        $this->resolverFactory = $factory;

        return $this;
    }

    /**
     * @param \Riak\Client\Core\RiakCluster $cluster
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withCluster(RiakCluster $cluster)
    {
        $this->cluster = $cluster;

        return $this;
    }

    /**
     * @return \Riak\Client\Core\RiakCluster
     */
    public function getCluster()
    {
        if ($this->cluster === null) {
            $this->cluster = new RiakCluster($this->getConfig());
        }

        return $this->cluster;
    }

    /**
     * @param \Riak\Client\RiakConfig $config
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withConfig(RiakConfig $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return \Riak\Client\RiakConfig
     */
    public function getConfig()
    {
        if ($this->config === null) {
            $this->config = new RiakConfig(
                $this->getConverterFactory(),
                $this->getResolverFactory(),
                $this->getRiakObjectConverter(),
                $this->getCrdtResponseConverter(),
                $this->getDomainMetadataReader(),
                $this->getDomainHydrator()
            );
        }

        return $this->config;
    }

    /**
     * Adds a RiakNode to this cluster.
     *
     * @param \Riak\Client\Core\RiakNode[] $node
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withNode(RiakNode $node)
    {
        $this->nodes[] = $node;

        return $this;
    }

    /**
     * Creates a RiakNode base on the given URI and add it to this cluster.
     *
     * @param string $uri
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withNodeUri($uri)
    {
        $builder = new RiakNodeBuilder();
        $node    = $builder
            ->withProtocol(parse_url($uri, PHP_URL_SCHEME))
            ->withHost(parse_url($uri, PHP_URL_HOST))
            ->withPort(parse_url($uri, PHP_URL_PORT))
            ->withUser(parse_url($uri, PHP_URL_USER))
            ->withPass(parse_url($uri, PHP_URL_PASS))
            ->build();

        return $this->withNode($node);
    }

    /**
     * @param string                                 $type
     * @param \Riak\Client\Resolver\ConflictResolver $resolver
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withConflictResolver($type, ConflictResolver $resolver)
    {
        $this->resolvers[$type] = $resolver;

        return $this;
    }

    /**
     * @param string                           $type
     * @param \Riak\Client\Converter\Converter $converter
     *
     * @return \Riak\Client\RiakClientBuilder
     */
    public function withConverter($type, Converter $converter)
    {
        $this->converters[$type] = $converter;

        return $this;
    }

    /**
     * Create a riak client
     *
     * @return \Riak\Client\RiakClient
     */
    public function build()
    {
        $config           = $this->getConfig();
        $cluster          = $this->getCluster();
        $resolverFactory  = $config->getResolverFactory();
        $converterFactory = $config->getConverterFactory();

        $cluster->setNodes($this->nodes);
        $resolverFactory->setResolvers($this->resolvers);
        $converterFactory->setConverters($this->converters);

        return new RiakClient($config, $cluster);
    }
}
