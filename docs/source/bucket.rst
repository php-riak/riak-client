======================
Buckets & Bucket Types
======================


For more information on how bucket types work and how to managing bucket types see : `Basho Bucket Types Docs`_.


In versions of Riak prior to 2.0, all queries are made to a bucket/key pair,
as in the following example read request:

.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\FetchValue;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('<bucket_type>', '<bucket_name>');
    $location  = new RiakLocation($namespace, '<key>');

    // fetch object
    $fetch  = FetchValue::builder($location)
        ->withNotFoundOk(true)
        ->withR(1)
        ->build();

    /** @var $result \Riak\Client\Command\Kv\Response\FetchValueResponse */
    $result = $client->execute($fetch);


To modify the properties of a bucket in Riak:

.. code-block:: php

    <?php
    use Riak\Client\Command\Bucket\StoreBucketProperties;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('<bucket_type>', '<bucket_name>');
    $store     =StoreBucketProperties::builder($namespace)
        ->withLinkwalkFunction($linkwalkFunction)
        ->withChashkeyFunction($chashkeyFunction)
        ->withPostcommitHook($postcommitFunction)
        ->withPrecommitHook($precommitFunction)
        ->withSearchIndex($searchIndex)
        ->withBasicQuorum($basicQuorum)
        ->withLastWriteWins($wins)
        ->withBackend($backend)
        ->withAllowMulti($allow)
        ->withNotFoundOk($ok)
        ->withSmallVClock($smallVClock)
        ->withYoungVClock($youngVClock)
        ->withOldVClock($ldVClock)
        ->withBigVClock($bigVClock)
        ->withNVal($nVal)
        ->withRw($rw)
        ->withDw($dw)
        ->withPr($pr)
        ->withPw($pw)
        ->withW($w)
        ->withR($w);

    $this->client->execute($store);


We can simply retrieve the bucket properties :

.. code-block:: php

    <?php
    use Riak\Client\Command\Bucket\FetchBucketProperties;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('<bucket_type>', '<bucket_name>');
    $fetch     = FetchBucketProperties::builder()
            ->withNamespace($namespace)
            ->build();

    /** @var $response \Riak\Client\Command\Bucket\Response\FetchBucketPropertiesResponse */
    /** @var $props \Riak\Client\Core\Query\BucketProperties */
    $response = $this->client->execute($fetch);
    $props    = $response->getProperties();

    echo $props->getNVal();
    // 3

.. _`Basho Bucket Types Docs`: http://docs.basho.com/riak/latest/dev/advanced/bucket-types


This tutorial documentation its based on the `Basho Bucket Types Docs`_.
