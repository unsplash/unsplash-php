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

        $connection = new Unsplash\Connection($this->provider, $this->accessToken);
		Unsplash\HttpClient::$connection = $connection;
	}

	public function testRequest()
	{
		VCR::insertCassette('endpoint.yml');
		
		$res = Unsplash\Endpoint::__callStatic('get', ['categories/2', []]);
		
		VCR::eject();
		
		$body = json_decode($res);

		$this->assertEquals(2, $body->id);
	}

	public function testRequestWithBadMethod()
	{
		$res = Unsplash\Endpoint::__callStatic('back', ['categories/2', []]);

		$this->assertNull($res);
	}

	public function testGoodRequest()
	{
		$response = m::mock('Guzzle\Http\Message\Response');
        $response->shouldReceive('getStatusCode')->times(2)->andReturn(200);

        $this->assertTrue(Unsplash\Endpoint::goodRequest($response));
	}

	public function testBadRequest()
	{
		$response = m::mock('Guzzle\Http\Message\Response');
        $response->shouldReceive('getStatusCode')->times(2)->andReturn(404);

        $this->assertFalse(Unsplash\Endpoint::goodRequest($response));
	}

	public function testParametersUpdate()
	{
		$endpoint = new Unsplash\Endpoint(['test' => 'mock', 'test_1' => 'mock_1']);
		$endpoint->update(['test' => 'mock_test']);

		$this->assertEquals('mock_test', $endpoint->test);
		$this->assertEquals('mock_1', $endpoint->test_1);
	}
}