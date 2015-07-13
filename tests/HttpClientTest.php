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
		$this->testMode = Unsplash\HttpClient::TEST_MODE;
	}

	public function tearDown()
	{
		// Since we change the ENV_MODE for some test
		// We set the test mode back to be sure all the further 
		// tests work
		putenv("ENV_MODE=test");
	}

	public function testAssignStaticConnection()
	{
		Unsplash\HttpClient::$connection = $this->connection;

		$this->assertEquals($this->connection, Unsplash\HttpClient::$connection);
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

	public function testHostInTestMode()
	{
		putenv("ENV_MODE={$this->testMode}");

		Unsplash\HttpClient::$connection = $this->connection;
		$client = new Unsplash\HttpClient();

		$this->assertEquals(getenv('API_HOST'), $client->getHost());
	}

	public function testSchemeInTestMode()
	{
		putenv("ENV_MODE={$this->testMode}");

		Unsplash\HttpClient::$connection = $this->connection;
		$client = new Unsplash\HttpClient();

		$this->assertEquals(getenv('API_SCHEME'), $client->getScheme());
	}

	public function testHostInStagingMode()
	{
		putenv("ENV_MODE=production");

		Unsplash\HttpClient::$connection = $this->connection;
		$client = new Unsplash\HttpClient();

		$this->assertEquals('api.unsplash.com', $client->getHost());
	}

	public function testSchemeInProductionMode()
	{
		putenv("ENV_MODE=production");

		Unsplash\HttpClient::$connection = $this->connection;
		$client = new Unsplash\HttpClient();

		$this->assertEquals('https', $client->getScheme());
	}
}