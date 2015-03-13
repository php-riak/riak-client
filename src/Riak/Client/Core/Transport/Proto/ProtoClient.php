<?php

namespace Riak\Client\Core\Transport\Proto;

use DrSlump\Protobuf\Message;
use DrSlump\Protobuf\Protobuf;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Transport\Proto\ProtoStream;
use Riak\Client\Core\Transport\RiakTransportException;

/**
 * RPB socket connection
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoClient
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoConnection
     */
    private $connection;

    /**
     * Mapping of message code to PB response class names
     *
     * @var array
     */
    private static $respClassMap = [
        RiakMessageCodes::DT_FETCH_RESP             => 'Riak\Client\ProtoBuf\DtFetchResp',
        RiakMessageCodes::DT_UPDATE_RESP            => 'Riak\Client\ProtoBuf\DtUpdateResp',
        RiakMessageCodes::ERROR_RESP                => 'Riak\Client\ProtoBuf\RpbErrorResp',
        RiakMessageCodes::GET_BUCKET_RESP           => 'Riak\Client\ProtoBuf\RpbGetBucketResp',
        RiakMessageCodes::GET_RESP                  => 'Riak\Client\ProtoBuf\RpbGetResp',
        RiakMessageCodes::GET_SERVER_INFO_RESP      => 'Riak\Client\ProtoBuf\RpbGetServerInfoResp',
        RiakMessageCodes::LIST_BUCKETS_RESP         => 'Riak\Client\ProtoBuf\RpbListBucketsResp',
        RiakMessageCodes::LIST_KEYS_RESP            => 'Riak\Client\ProtoBuf\RpbListKeysResp',
        RiakMessageCodes::PUT_RESP                  => 'Riak\Client\ProtoBuf\RpbPutResp',
        RiakMessageCodes::INDEX_RESP                => 'Riak\Client\ProtoBuf\RpbIndexResp',
        RiakMessageCodes::MAPRED_RESP               => 'Riak\Client\ProtoBuf\RpbMapRedResp',
        RiakMessageCodes::SEARCH_QUERY_RESP         => 'Riak\Client\ProtoBuf\RpbSearchQueryResp',
        RiakMessageCodes::YOKOZUNA_INDEX_GET_RESP   => 'Riak\Client\ProtoBuf\RpbYokozunaIndexGetResp',
        RiakMessageCodes::YOKOZUNA_SCHEMA_GET_RESP  => 'Riak\Client\ProtoBuf\RpbYokozunaSchemaGetResp'
    ];

    /**
     * @param \Riak\Client\Core\Transport\Proto\ProtoConnection $connection
     */
    public function __construct(ProtoConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Send a Protobuf message and receive the response
     *
     * @param \DrSlump\Protobuf\Message $message
     * @param integer                   $messageCode
     * @param integer                   $expectedResponseCode
     *
     * @return \DrSlump\Protobuf\Message
     */
    public function send(Message $message, $messageCode, $expectedResponseCode)
    {
        $payload  = $this->encodeMessage($message, $messageCode);
        $stream   = $this->connection->send($payload);

        return $this->receiveMessage($stream, $expectedResponseCode);
    }

    /**
     * Send a Protobuf message using a new stream and return it for future usage
     *
     * @param \DrSlump\Protobuf\Message $message
     * @param integer                   $messageCode
     *
     * @return \Riak\Client\Core\Transport\Proto\ProtoStream
     */
    public function emit(Message $message, $messageCode)
    {
        $payload = $this->encodeMessage($message, $messageCode);
        $stream  = $this->connection->createStream();

        return $this->connection->send($payload, $stream);
    }

    /**
     * Receive a protobuf reponse message
     *
     * @param \Riak\Client\Core\Transport\Proto\ProtoStream $stream
     * @param integer                                       $messageCode
     *
     * @return \DrSlump\Protobuf\Message
     */
    public function receiveMessage(ProtoStream $stream, $messageCode)
    {
        $response = $this->connection->receive($stream);
        $class    = $this->classForCode($messageCode);
        $respCode = $response[0];
        $respBody = $response[1];

        if ($respCode != $messageCode) {
            throw $this->createResponseException($respCode, $respBody);
        }

        if ($class == null) {
            return;
        }

        return Protobuf::decode($class, (string) $respBody);
    }

    /**
     * @param integer $actualCode
     * @param string  $respBody
     *
     * @return \Riak\Client\Core\Transport\RiakTransportException
     */
    protected function createResponseException($actualCode, $respBody)
    {
        $exceptionCode    = $actualCode;
        $exceptionMessage = "Unexpected protobuf response code: " . $actualCode;

        if ($actualCode === RiakMessageCodes::ERROR_RESP) {
            $errorClass   = self::$respClassMap[$actualCode];
            $errorMessage = Protobuf::decode($errorClass, (string) $respBody);

            if ($errorMessage->hasErrmsg()) {
                $exceptionMessage  = $errorMessage->getErrmsg();
            }

            if ($errorMessage->hasErrcode()) {
                $exceptionCode = $errorMessage->getErrcode();
            }
        }

        return new RiakTransportException($exceptionMessage, $exceptionCode);
    }

    /**
     * @param \DrSlump\Protobuf\Message $message
     * @param integer                   $code
     *
     * @return string
     */
    private function encodeMessage(Message $message, $code)
    {
        return $this->connection->encode(Protobuf::encode($message), $code);
    }

    /**
     * @param string $code
     *
     * @return string
     */
    protected function classForCode($code)
    {
        if (isset(self::$respClassMap[$code])) {
            return self::$respClassMap[$code];
        }

        return null;
    }
}
