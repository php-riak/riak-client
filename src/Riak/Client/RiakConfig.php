<?php

namespace Riak\Client;

use Riak\Client\Resolver\ResolverFactory;
use Riak\Client\Converter\ConverterFactory;
use Riak\Client\Converter\RiakObjectConverter;
use Riak\Client\Converter\CrdtResponseConverter;
use Riak\Client\Converter\Hydrator\DomainHydrator;
use Riak\Client\Converter\Hydrator\DomainMetadataReader;

/**
 * Riak client config.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakConfig
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
     * @param \Riak\Client\Converter\ConverterFactory              $converterFactory
     * @param \Riak\Client\Resolver\ResolverFactory                $resolverFactory
     * @param \Riak\Client\Converter\RiakObjectConverter           $riakObjectConverter
     * @param \Riak\Client\Converter\CrdtResponseConverter         $crdtResponseConverter
     * @param \Riak\Client\Converter\Hydrator\DomainMetadataReader $domainMetadataReader
     * @param \Riak\Client\Converter\ConverterFactory              $domainHydrator
     */
    public function __construct(
        ConverterFactory      $converterFactory,
        ResolverFactory       $resolverFactory,
        RiakObjectConverter   $riakObjectConverter,
        CrdtResponseConverter $crdtResponseConverter,
        DomainMetadataReader  $domainMetadataReader,
        DomainHydrator        $domainHydrator
    ) {
        $this->converterFactory      = $converterFactory;
        $this->resolverFactory       = $resolverFactory;
        $this->riakObjectConverter   = $riakObjectConverter;
        $this->crdtResponseConverter = $crdtResponseConverter;
        $this->domainMetadataReader  = $domainMetadataReader;
        $this->domainHydrator        = $domainHydrator;
    }

    /**
     * @return \Riak\Client\Converter\RiakObjectConverter
     */
    public function getRiakObjectConverter()
    {
        return $this->riakObjectConverter;
    }

    /**
     * @return \Riak\Client\Converter\CrdtResponseConverter
     */
    public function getCrdtResponseConverter()
    {
        return $this->crdtResponseConverter;
    }

    /**
     * @return \Riak\Client\Converter\Hydrator\DomainHydrator
     */
    public function getDomainHydrator()
    {
        return $this->domainHydrator;
    }

    /**
     * @return \Riak\Client\Converter\Hydrator\DomainMetadataReader
     */
    public function getDomainMetadataReader()
    {
        return $this->domainMetadataReader;
    }

    /**
     * @return \Riak\Client\Converter\ConverterFactory
     */
    public function getConverterFactory()
    {
        return $this->converterFactory;
    }

    /**
     * @return \Riak\Client\Resolver\ResolverFactory
     */
    public function getResolverFactory()
    {
        return $this->resolverFactory;
    }
}
