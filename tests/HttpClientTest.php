<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;

class HttpClientTest extends BaseTest
{
	public $connection;

	public function setUp()
	{
		parent::setUp();

		$provider = clone $this->provider;
		$provider->shouldReceive('getAccessToken')->times(1)->andReturn((object)[
			'accessToken' => 'mock_access_token_1',
			'refreshToken' => 'mock_refresh_token_1',
			'expires' => time() + 3600
		]);

		$this->connection = new Unsplash\Connection($provider, $this->accessToken);
	}

	public function tearDown()
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
		]);

		$this->assertInstanceOf('Crew\Unsplash\Connection', Unsplash\HttpClient::$connection);
		$this->assertEquals('Client-ID mock_application_id', Unsplash\HttpClient::$connection->getAuthorizationToken());
	}

	public function testInitConnectionWithAccessTokenArray()
	{
		Unsplash\HttpClient::init([
			'applicationId' => 'mock_application_id',
		], [
			'access_token'  => 'mock_access_token'
		]);

		$this->assertEquals('Bearer mock_access_token', Unsplash\HttpClient::$connection->getAuthorizationToken());
	}

	public function testInitConnectionWithAccessTokenObject()
	{
		Unsplash\HttpClient::init([
			'applicationId' => 'mock_application_id',
		], $this->accessToken);

		$this->assertEquals('Bearer ' . getenv('ACCESS_TOKEN'), Unsplash\HttpClient::$connection->getAuthorizationToken());
	}


	public function testRequestSendThroughClient()
	{
		Unsplash\HttpClient::$connection = $this->connection;

		VCR::insertCassette('categories.yml');

		$response = (new Unsplash\HttpClient())->send("get", ['categories/2']);
		$body = json_decode($response->getBody(), true);

		VCR::eject();

		$this->assertEquals(2, $body['id']);
	}

	public function testConnectionFromHttpClient()
	{
		Unsplash\HttpClient::$connection = $this->connection;

		$token = Unsplash\HttpClient::$connection->generateToken('mock_code');

		$this->assertEquals('Bearer mock_access_token_1', Unsplash\HttpClient::$connection->getAuthorizationToken());
	}

}