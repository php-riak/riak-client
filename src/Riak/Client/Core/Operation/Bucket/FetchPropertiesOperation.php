<?php

namespace Riak\Client\Core\Operation\Bucket;

use Riak\Client\Command\Bucket\Response\FetchBucketPropertiesResponse;
use Riak\Client\Core\Query\Func\NamedJsFunction;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Core\Message\Bucket\GetResponse;
use Riak\Client\Core\Message\Bucket\GetRequest;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to fetch bucket properties from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchPropertiesOperation implements RiakOperation
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     */
    public function __construct(RiakNamespace $namespace)
    {
        $this->namespace  = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $getRequest  = $this->createGetRequest();
        $getResponse = $adapter->send($getRequest);

        $bucketProps = $this->createBucketProps($getResponse);
        $response    = new FetchBucketPropertiesResponse($this->namespace, $bucketProps);

        return $response;
    }

    /**
     * @return \Riak\Client\Core\Message\Bucket\GetRequest
     */
    private function createGetRequest()
    {
        $request = new GetRequest();

        $request->type   = $this->namespace->getBucketType();
        $request->bucket = $this->namespace->getBucketName();

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetResponse $response
     *
     * @return \Riak\Client\Core\Query\BucketProperties
     */
    private function createBucketProps(GetResponse $response)
    {
        $values = [];

        foreach ($response as $key => $value) {
            $values[$key] = $value;
        }

        if ($response->linkwalkFunction) {
            $values['linkwalkFunction'] = ErlangFunction::createFromArray($response->linkwalkFunction);
        }

        if ($response->chashKeyFunction) {
            $values['chashKeyFunction'] = ErlangFunction::createFromArray($response->chashKeyFunction);
        }

        if ($response->precommitHooks) {
            $values['precommitHooks'] = $this->parseHooks($response->precommitHooks);
        }

        if ($response->postcommitHooks) {
            $values['postcommitHooks'] = $this->parseHooks($response->postcommitHooks);
        }

        return new BucketProperties($values);
    }

    /**
     * @param array $hooks
     *
     * @return array
     */
    private function parseHooks(array $hooks)
    {
        $list = [];

        foreach ($hooks as $func) {
            if (isset($func['name'])) {
                $list[] = NamedJsFunction::createFromArray($func);

                continue;
            }

            $list[] = ErlangFunction::createFromArray($func);
        }

        return $list;
    }
}
