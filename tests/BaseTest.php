<?php

namespace Crew\Unsplash\Tests;

use Mockery as m;
use \VCR\VCR;
use Dotenv\Dotenv;
use \League\OAuth2\Client\Token\AccessToken;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
	protected $provider = null;
	protected $accessToken;

	public function setUp()
	{
		// Only load env file if it exist.
		// It will use the env variable on server if there's no file
		if (file_exists(__DIR__ . '/.env')) {
			$dotenv = new Dotenv(__DIR__);
			$dotenv->load();
		}
		
		$this->provider = m::mock('Crew\Unsplash\Provider', [
			'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none'
		]);
		$this->provider->clientId = 'mock_client_id';

		$this->accessToken = new AccessToken(['access_token' => getenv('ACCESS_TOKEN')]);

		VCR::configure()->setStorage('json');
		VCR::turnOn();
	}

	/**
 	 * getPrivateMethod
 	 *
 	 * @author	Joe Sexton <joe@webtipblog.com>
 	 * @param 	string $className
 	 * @param 	string $methodName
 	 * @return	ReflectionMethod
 	 */
	public function executePrivateMethod($object, $methodName, $params = []) {
		$className = get_class($object);

		$reflector = new \ReflectionClass( $className );
		$method = $reflector->getMethod( $methodName );
		$method->setAccessible( true );
 
		return $method->invokeArgs($object, $params);
	}
}