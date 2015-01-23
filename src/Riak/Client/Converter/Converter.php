<?php

namespace Riak\Client\Converter;


/**
 * The Converter acts as a bridge between the core and the user level API.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface Converter
{
    /**
     * Convert from a riak objet object reference to a domain object reference.
     *
     * @param \Riak\Client\Converter\DomainObjectReference $reference
     *
     * @return object
     */
    public function toDomain(RiakObjectReference $reference);

    /**
     * Convert from a domain object reference to a riak objet object reference.
     *
     * @param \Riak\Client\Converter\RiakObjectReference $reference
     *
     * @return \Riak\Client\Core\Query\RiakObject
     */
    public function fromDomain(DomainObjectReference $reference);
}
