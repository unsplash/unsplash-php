<?php 

namespace Crew\Unsplash\Tests;

use Mockery as m;
use Crew\Unsplash;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    public static $unsplashProvider;

    public static function setUpBeforeClass()
    {
        self::$unsplashProvider = new Unsplash\Provider([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        $url = self::$unsplashProvider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull(self::$unsplashProvider->state);
    }

    public function testAuthorizationUrlWithScopes()
    {
        $scopes = ['public', 'read_user'];
        self::$unsplashProvider->setScopes($scopes);
        $url = self::$unsplashProvider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertEquals(implode(',', $scopes), $query['scope']);
    }

    public function testUrlAuthorize()
    {
        $url = self::$unsplashProvider->urlAuthorize();
        $uri = parse_url($url);

        $this->assertEquals('/oauth/authorize', $uri['path']);
    }

    public function testUrlAccessToken()
    {
        $url = self::$unsplashProvider->urlAccessToken();
        $uri = parse_url($url);

        $this->assertEquals('/oauth/token', $uri['path']);
    }

    public function testGetAccessToken()
    {
        $response = m::mock('Guzzle\Http\Message\Response');
        $response->shouldReceive('getBody')->times(1)->andReturn('{"access_token": "mock_access_token", "expires": 3600, "refresh_token": "mock_refresh_token", "uid": 1}');

        $client = m::mock('Guzzle\Service\Client');
        $client->shouldReceive('setBaseUrl')->times(1);
        $client->shouldReceive('post->send')->times(1)->andReturn($response);
        self::$unsplashProvider->setHttpClient($client);

        $token = self::$unsplashProvider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        $this->assertEquals('mock_access_token', $token->accessToken);
        $this->assertLessThanOrEqual(time() + 3600, $token->expires);
        $this->assertGreaterThanOrEqual(time(), $token->expires);
        $this->assertEquals('mock_refresh_token', $token->refreshToken);
        $this->assertEquals('1', $token->uid);
    }

    public function testDefaultScopes()
    {
        $this->assertEquals(['public', 'read_user'], self::$unsplashProvider->getScopes());
    }

    public function testAuthorizationHeader()
    {
        $this->assertEquals('Bearer', self::$unsplashProvider->authorizationHeader);
    }

    public function testUserData()
    {
        $postResponse = m::mock('Guzzle\Http\Message\Response');
        $postResponse->shouldReceive('getBody')->times(1)->andReturn('{"access_token": "mock_access_token","token_type": "Bearer","expires_in": "mock_expires","refresh_token": "mock_refresh_token","scope": "scope1 scope2"}');

        $getResponse = m::mock('Guzzle\Http\Message\Response');
        $getResponse->shouldReceive('getBody')->times(4)->andReturn('{"first_name": "mock_first_name","last_name": "mock_last_name","email": "mock_email","picture": "mock_image_url","promo_code": "teypo","uuid": "mock_id"}');

        $client = m::mock('Guzzle\Service\Client');
        $client->shouldReceive('setBaseUrl')->times(5);
        $client->shouldReceive('setDefaultOption')->times(4);
        $client->shouldReceive('post->send')->times(1)->andReturn($postResponse);
        $client->shouldReceive('get->send')->times(4)->andReturn($getResponse);
        self::$unsplashProvider->setHttpClient($client);

        $token = self::$unsplashProvider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $user = self::$unsplashProvider->getUserDetails($token);

        $this->assertEquals('mock_id', self::$unsplashProvider->getUserUid($token));
        $this->assertNull(self::$unsplashProvider->getUserScreenName($token));
        $this->assertEquals('mock_email', self::$unsplashProvider->getUserEmail($token));
        $this->assertEquals('mock_email', $user->email);
    }
}
