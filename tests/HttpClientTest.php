<?php

namespace Unsplash\Tests;

use \Unsplash as Unsplash;
use \League\OAuth2\Client\Token\AccessToken;
use \VCR\VCR;

/**
 * Class HttpClientTest
 * @package Unsplash\Tests
 */
class HttpClientTest extends BaseTest
{
    public $connection;

    public function setUp(): void
    {
        parent::setUp();

        $provider = clone $this->provider;
        $provider->shouldReceive('getAccessToken')->times(1)->andReturn(new AccessToken([
            'access_token' => 'mock_access_token_1',
            'refresh_token' => 'mock_refresh_token_1',
            'expires_in' => time() + 3600
        ]));

        $this->connection = new Unsplash\Connection($provider, $this->accessToken);
    }

    public function tearDown(): void
    {
        Unsplash\HttpClient::$connection = null;
    }

    public function testAssignStaticConnection()
    {
        Unsplash\HttpClient::$connection = $this->connection;

        $this->assertEquals($this->connection, Unsplash\HttpClient::$connection);
    }

    public function testInitConnection()
    {
        Unsplash\HttpClient::init([
            'applicationId' => 'mock_application_id',
            'utmSource' => 'test'
        ]);

        $this->assertInstanceOf('Unsplash\Connection', Unsplash\HttpClient::$connection);
        $this->assertEquals('Client-ID mock_application_id', Unsplash\HttpClient::$connection->getAuthorizationToken());
    }

    public function testInitConnectionWithAccessTokenArray()
    {
        Unsplash\HttpClient::init([
            'applicationId' => 'mock_application_id',
            'utmSource' => 'test'
        ], [
            'access_token'    => 'mock_access_token',
            'refresh_token' => 'mock_refresh_token_1',
            'expires_in' => time() + 3600
        ]);

        $this->assertEquals('Bearer mock_access_token', Unsplash\HttpClient::$connection->getAuthorizationToken());
    }

    public function testInitWithoutUtmSourceRaisesNotice()
    {
        $this->expectNotice(\PHPUnit\Framework\Error\Notice::class);

        Unsplash\HttpClient::init([
            'applicationId' => 'mock_application_id',
        ], [
            'access_token'    => 'mock_access_token',
            'refresh_token' => 'mock_refresh_token_1',
            'expires_in' => time() + 3600
        ]);
    }


    public function testInitConnectionWithAccessTokenObject()
    {
        Unsplash\HttpClient::init([
            'applicationId' => 'mock_application_id',
            'utmSource' => 'test'
        ], $this->accessToken);

        $this->assertEquals(
            'Bearer ' . getenv('ACCESS_TOKEN'),
            Unsplash\HttpClient::$connection->getAuthorizationToken()
        );
    }

    public function testInitConnectionWithWrongTypeOfToken()
    {
        Unsplash\HttpClient::init([
            'applicationId' => 'mock_application_id',
            'utmSource' => 'test'
        ], 'access_token');

        $this->assertEquals('Client-ID mock_application_id', Unsplash\HttpClient::$connection->getAuthorizationToken());
    }


    public function testRequestSendThroughClient()
    {
        Unsplash\HttpClient::$connection = $this->connection;

        VCR::insertCassette('collections.json');
        $response = (new Unsplash\HttpClient())->send("get", ['collections/300']);
        $body = json_decode($response->getBody(), true);
        VCR::eject();

        $this->assertEquals(300, $body['id']);
    }

    public function testConnectionFromHttpClient()
    {
        Unsplash\HttpClient::$connection = $this->connection;
        Unsplash\HttpClient::$connection->generateToken('mock_code');

        $this->assertEquals('Bearer mock_access_token_1', Unsplash\HttpClient::$connection->getAuthorizationToken());
    }
}
