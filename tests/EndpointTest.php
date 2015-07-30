<?php

namespace Crew\Unsplash\Tests;

use Crew\Unsplash;
use Mockery as m;
// use GuzzleHttp\Client;
// use GuzzleHttp\Handler\MockHandler;
// use GuzzleHttp\HandlerStack;
// use GuzzleHttp\Psr7\Response;
use \VCR\VCR;

class EndpointTest extends BaseTest
{
	public function setUp()
	{
		parent::setUp();

        Unsplash\HttpClient::init([
			'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none'
		], [
			'access_token' => getenv('ACCESS_TOKEN')
		]);
	}

	public function testRequest()
	{
		VCR::insertCassette('endpoint.yml');
		
		$res = Unsplash\Endpoint::__callStatic('get', ['categories/2', []]);
		
		VCR::eject();
		
		$body = json_decode($res->getBody());

		$this->assertEquals(2, $body->id);
	}

	public function testRequestWithBadMethod()
	{
		$res = Unsplash\Endpoint::__callStatic('back', ['categories/2', []]);

		$this->assertNull($res);
	}

	public function testParametersUpdate()
	{
		$endpoint = new Unsplash\Endpoint(['test' => 'mock', 'test_1' => 'mock_1']);
		$endpoint->update(['test' => 'mock_test']);

		$this->assertEquals('mock_test', $endpoint->test);
		$this->assertEquals('mock_1', $endpoint->test_1);
	}
}