Merging siblings
================

Siblings_ are an important feature in Riak,
for this purpose we have the interface ``Riak\Client\Resolver\ConflictResolver``,
you implement this interface to resolve siblings into a single object.


Below is a simple example that will just merge the content of siblings :

Assuming we have the following domain :

.. code-block:: php

    <?php
    use Riak\Client\Annotation as Riak;

    class MyDomainObject
    {
        /**
         * @var string
         *
         * @Riak\ContentType
         */
        private $contentType = 'application/json';

        /**
         * @var string
         *
         * @Riak\VClock
         */
        private $vClock;

        /**
         * @var string
         */
        private $value;

        // getters and setters
    }


By implementing the interface ``Riak\Client\Resolver\ConflictResolver`` we can merge siblings,

The method ``resolve($siblings)`` will be called every time we have siblings and invoke ``Riak\Client\Command\Kv\Response\FetchValueResponse#getValue('MyDomainObject')`` :

.. code-block:: php

    <?php
    use \Riak\Client\Resolver\ConflictResolver;
    use \Riak\Client\Core\Query\RiakObject;
    use \Riak\Client\Core\Query\RiakList;

    class MySimpleResolver implements ConflictResolver
    {
        /**
         * {@inheritdoc}
         */
        public function resolve(RiakList $siblings)
        {
            $result  = new MyDomainObject();
            $content = "";

            /** @var $object \MyDomainObject */
            foreach ($siblings as $object) {
                $content .= $object->getValue();
            }

            $result->setValue($content);

            return $result;
        }
    }


Register your resolver during the application start up :

.. code-block:: php

    <?php
    /** @var $builder \Riak\Client\RiakClientBuilder */
    $client = $builder
        ->withConflictResolver('MyDomainObject' new MySimpleResolver())
        ->withNodeUri('http://localhost:8098')
        ->build();


Finally whe can fetch the object with siblings and resolve any possible conflict :

.. code-block:: php

    <?php
    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $location  = new RiakLocation($namespace, 'object_key');
    $fetch     = FetchValue::builder($location)
        ->withNotFoundOk(true)
        ->build();

    /** @var $domain \MyDomainObject */
    $result = $client->execute($fetch);
    $domain = $result->getValue('MyDomainObject');

    echo $result->getNumberOfValues();
    // 2

See Siblings_ for more details on conflict resolution on riak

.. _Siblings: http://docs.basho.com/riak/latest/dev/using/conflict-resolution/

