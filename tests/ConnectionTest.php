<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;

class ConnectionTest extends BaseTest
{
	private $conneciton = null;

	public function setUp()
	{
		parent::setUp();

		// If the class is not cloned, the mock method will get stick 
		// to it
		$provider = clone $this->provider;

		$provider->shouldReceive('getAuthorizationUrl')->times(1)->andReturn('http://api.staging.unsplash.com/oauth/authorize?client_id=mock_client_id&client_secret=mock_secret&redirect_uri=none');
		
		$provider->shouldReceive('getAccessToken')->times(1)->andReturn((object)[
			'accessToken' => 'mock_access_token_1',
			'refreshToken' => 'mock_refresh_token_1',
			'expires' => time() + 3600
		]);

		$this->connection  = new Unsplash\Connection($provider);
	}

	public function testConnectionUrlConstruction()
	{
		$url = $this->connection->getConnectionUrl();
		$testedUrl = 'http://api.staging.unsplash.com/oauth/authorize?client_id=mock_client_id&client_secret=mock_secret&redirect_uri=none';

		$this->assertEquals($testedUrl, $url);
	}

	public function testClientIdAsAuthorizationToken()
	{
		$this->assertEquals('Client-ID mock_client_id', $this->connection->getAuthorizationToken());
	}

	public function testAccessTokenAsAuthorizationToken()
	{
		$this->connection->setToken((object)[
			'accessToken' => 'mock_access_token',
			'refreshToken' => 'mock_refresh_token',
			'expires' => time() + 3600
		]);

		$this->assertEquals('Bearer mock_access_token', $this->connection->getAuthorizationToken());
	}

	public function testGenerateTokenWithGoodCode()
	{
		$token = $this->connection->generateToken('mock_code');

		$this->assertEquals($token, (object)[
			'accessToken' => 'mock_access_token_1',
			'refreshToken' => 'mock_refresh_token_1',
			'expires' => time() + 3600
		]);
	}

	public function testRegenerateToken()
	{
		$this->connection->setToken((object)[
			'accessToken' => 'mock_access_token',
			'refreshToken' => 'mock_refresh_token',
			'expires' => time() + 3600
		]);
		$token = $this->connection->refreshToken();

		$this->assertEquals($token, (object)[
			'accessToken' => 'mock_access_token_1',
			'refreshToken' => 'mock_refresh_token_1',
			'expires' => time() + 3600
		]);
	}

	public function testRegenerateTokenOnAuthorization()
	{
		$this->connection->setToken((object)[
			'accessToken' => 'mock_access_token',
			'refreshToken' => 'mock_refresh_token',
			'expires' => time() - 3600
		]);

		$this->assertEquals('Bearer mock_access_token_1', $this->connection->getAuthorizationToken());
	}
}