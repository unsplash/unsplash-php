<?php

namespace Crew\Unsplash\Tests;

use Crew\Unsplash;
use Mockery as m;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class EndpointTest extends BaseTest
{
	public function setUp()
	{
		parent::setUp();

        $mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], '[{"name": "mock_name", "other_info": "moke_info"}, {"name": "mock_name_1", "other_info": "moke_info_3"}]'),
		]);

		$handler = HandlerStack::create($mock);

        $this->endpoint = new Unsplash\Endpoint($this->provider);
        $this->endpoint->setHttpClient(new Client(['handler' => $handler]));
	}

	public function testRequest()
	{
		$res = $this->executePrivateMethod($this->endpoint, 'get', ['path', []]);

		$waitedRes = [['name'=>'mock_name', 'other_info'=>'moke_info'], ['name'=>'mock_name_1', 'other_info'=>'moke_info_3']];

		$this->assertEquals($waitedRes, $res);
	}

	public function testStatusCodeAfterRequest()
	{
		$res = $this->executePrivateMethod($this->endpoint, 'get', ['path', []]);

		$this->assertEquals(200, $this->endpoint->getStatusCode());
	}

	public function testHeadersAfterRequest()
	{
		$res = $this->executePrivateMethod($this->endpoint, 'get', ['path', []]);

		$this->assertEquals('Bar', $this->endpoint->getHeaders('X-Foo'));
	}
}