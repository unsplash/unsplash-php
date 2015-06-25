<?php

namespace Crew\Unsplash\Tests;

use Mockery as m;

class BaseTest extends \PHPUnit_Framework_TestCase
{
	protected $provider = null;

	public function setUp()
	{
		$this->provider = m::mock('alias:Crew\Unsplash\Provider\Unsplash', [
			'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none'
		]);
		$this->provider->client_id = 'mock_client_id';
		$this->provider->shouldReceive('getAuthorizationUrl')->times(1)->andReturn('http://api.staging.unsplash.com/oauth/authorize?client_id=mock_client_id&client_secret=mock_secret&redirect_uri=none');
		
		$this->provider->shouldReceive('getAccessToken')->times(1)->andReturn((object)[
			'accessToken' => 'mock_access_token_1',
			'refreshToken' => 'mock_refresh_token_1',
			'expires' => time() + 3600
		]);
	}
}