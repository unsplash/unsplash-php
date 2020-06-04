<?php

namespace Unsplash\Tests;

use Unsplash;
use VCR\VCR;

/**
 * Class EndpointTest
 * @package Unsplash\Tests
 */
class EndpointTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        Unsplash\HttpClient::init(
            [
                'clientId' => 'mock_client_id',
                'clientSecret' => 'mock_secret',
                'redirectUri' => 'none',
                'utmSource' => 'test'
            ],
            [
                'access_token' => getenv('ACCESS_TOKEN'),
                'refresh_token' => 'mock_refresh_token_1',
                'expires_in' => time() + 3600
            ]
        );
    }

    public function testRequest()
    {
        VCR::insertCassette('endpoint.json');
        $res = Unsplash\Endpoint::__callStatic('get', ['collections/300', []]);
        VCR::eject();
        $body = json_decode($res->getBody());

        $this->assertEquals(300, $body->id);
    }

    public function testRequestWithBadMethod()
    {
        $res = Unsplash\Endpoint::__callStatic('back', ['collections/300', []]);
        $this->assertNull($res);
    }

    public function testParametersUpdate()
    {
        $endpoint = new Unsplash\Endpoint(['test' => 'mock', 'test_1' => 'mock_1']);
        $endpoint->update(['test' => 'mock_test']);

        $this->assertEquals('mock_test', $endpoint->test);
        $this->assertEquals('mock_1', $endpoint->test_1);
    }

    public function testRateLimitError()
    {
        $this->expectException(\Unsplash\Exception::class);

        VCR::insertCassette('endpoint.json');
        Unsplash\Endpoint::__callStatic('get', ['collections/301', []]);
        VCR::eject();
    }

    public function testRateLimitResponseExists()
    {
        VCR::insertCassette('endpoint.json');
        $res = Unsplash\Endpoint::__callStatic('get', ['collections/300', []]);
        VCR::eject();
        $headers = $res->getHeaders();

        $this->assertEquals('99999999', $headers['X-RateLimit-Remaining'][0]);
    }

    public function testCanMakeArray()
    {
        $endpoint = new Unsplash\Endpoint(['test' => 'mock', 'test_1' => 'mock_1']);

        $this->assertEquals($endpoint->toArray(), ['test' => 'mock', 'test_1' => 'mock_1']);
    }
}
