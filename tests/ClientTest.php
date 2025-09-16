<?php

declare(strict_types=1);

namespace pvcTests\http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use pvc\http\Client;
use pvc\http\err\ClientRuntimeException;
use pvc\http\err\InvalidConnectionTimeoutException;
use pvc\http\err\InvalidHttpVerbException;
use pvc\interfaces\http\UrlInterface;

class ClientTest extends TestCase
{
    protected \PHPUnit\Framework\MockObject\MockObject $guzzleClient;
    protected Client $client;

    public function setUp(): void
    {
        $this->guzzleClient = $this->createMock(GuzzleClient::class);
        $this->client = new Client($this->guzzleClient);
    }

    /**
     * @return void
     * @covers \pvc\http\Client::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(Client::class, $this->client);
    }

    /**
     * @return void
     * @throws InvalidConnectionTimeoutException
     * @covers \pvc\http\Client::setConnectionTimeout
     * @covers \pvc\http\Client::getConnectionTimeout
     */
    public function testSetGetConnectionTimeout(): void
    {
        /**
         * confirm there is a default
         * @phpstan-ignore-next-line
         */
        self::assertIsInt($this->client->getConnectionTimeout());

        /**
         * test set / get
         */
        $newTimeout = 5;
        $this->client->setConnectionTimeout($newTimeout);
        self::assertSame($newTimeout, $this->client->getConnectionTimeout());

        /**
         * test exception for bad argument
         */
        $badTimeout = -3;
        self::expectException(InvalidConnectionTimeoutException::class);
        $this->client->setConnectionTimeout($badTimeout);
    }

    /**
     * @return void
     * @throws \pvc\http\err\ClientRuntimeException
     * @covers \pvc\http\Client::validateHttpVerb
     */
    public function testBadHttpVerbThrowsException(): void
    {
        $goodUrl = $this->createMock(UrlInterface::class);
        $badVerb = 'FOO';
        self::expectException(InvalidHttpVerbException::class);
        $this->client->request($badVerb, $goodUrl);
    }

    /**
     * @return void
     * @throws \pvc\http\err\ClientRuntimeException
     * @covers \pvc\http\Client::request
     * @covers \pvc\http\Client::validateHttpVerb
     */
    public function testClientRequestSucceeds(): void
    {
        $url = $this->createMock(UrlInterface::class);
        $httpVerb = 'GET';

        $httpStatus = 200; // OK
        $headers = [];
        $body = 'Hello World!';
        $testResponse = new Response($httpStatus, $headers, $body);

        $mockHandler = new MockHandler([$testResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);
        $client = new Client($guzzleClient);

        $actualResponse = $client->request($httpVerb, $url);
        self::assertSame($testResponse, $actualResponse);
    }

    /**
     * @return void
     * @throws ClientRuntimeException
     * @covers \pvc\http\Client::request
     */
    public function testClientRequestFails(): void
    {
        $url = $this->createMock(UrlInterface::class);
        $httpVerb = 'GET';

        $httpStatus = 404; // not found
        $headers = [];
        $body = 'not Found';
        $testResponse = new Response($httpStatus, $headers, $body);

        $mockHandler = new MockHandler([$testResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);
        $client = new Client($guzzleClient);

        self::expectException(ClientRuntimeException::class);
        $actualResponse = $client->request($httpVerb, $url);
        self::assertSame($httpStatus, $actualResponse->getStatusCode());
    }
}
