<?php 

namespace Crew\Unsplash\Tests;

use Mockery as m;
use Crew\Unsplash;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;

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
        $this->assertNotNull(self::$unsplashProvider->getState());
    }

    public function testAuthorizationUrlWithScopes()
    {
        $scopes = ['public', 'read_user'];
        $url = self::$unsplashProvider->getAuthorizationUrl(['scope' => $scopes]);
        $uri = parse_url($url);

        parse_str($uri['query'], $query);

        $this->assertEquals(implode(' ', $scopes), $query['scope']);
    }

    public function testUrlAuthorize()
    {
        $url = self::$unsplashProvider->getBaseAuthorizationUrl();
        $uri = parse_url($url);

        $this->assertEquals('/oauth/authorize', $uri['path']);
    }

    public function testUrlAccessToken()
    {
        $url = self::$unsplashProvider->getBaseAccessTokenUrl([]);
        $uri = parse_url($url);

        $this->assertEquals('/oauth/token', $uri['path']);
    }

    public function testGetAccessToken()
    {
        $handler = new MockHandler([
            new Response(200, [], '{"access_token": "mock_access_token","token_type": "Bearer","expires_in": "mock_expires","refresh_token": "mock_refresh_token","scope": "scope1 scope2"}'),
        ]);

        self::$unsplashProvider->setHttpClient(new Client(['handler' => $handler]));

        $token = self::$unsplashProvider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertLessThanOrEqual(time() + 3600, $token->getExpires());
        $this->assertGreaterThanOrEqual(time(), $token->getExpires());
        $this->assertEquals('mock_refresh_token', $token->getRefreshToken());
    }

    public function testUserData()
    {
        $handler = new MockHandler([
            new Response(200, [], '{"access_token": "mock_access_token","token_type": "Bearer","expires_in": "mock_expires","refresh_token": "mock_refresh_token","scope": "scope1 scope2"}'),
            new Response(202, [], '{"first_name": "mock_first_name","last_name": "mock_last_name", "picture": "mock_image_url","id": "mock_id"}'),
        ]);

        self::$unsplashProvider->setHttpClient(new Client(['handler' => $handler]));

        $token = self::$unsplashProvider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $user = self::$unsplashProvider->getResourceOwner($token);

        $this->assertEquals('mock_id', $user->id);
    }

    /**
     * @expectedException Crew\Unsplash\Exception
     * @expectedExceptionMessage ["Not accessible: Endpoint not accessible"]
     */
    public function testErrorOnGetAccessToken()
    {
        $handler = new MockHandler([
            new Response(400, [], json_encode([
                'error' => 'Not accessible', 
                'error_description' => 'Endpoint not accessible'
            ])),
        ]);

        self::$unsplashProvider->setHttpClient(new Client(['handler' => $handler]));
        self::$unsplashProvider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }
}
