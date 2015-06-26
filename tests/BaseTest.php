<?php

namespace Crew\Unsplash\Tests;

use Mockery as m;
use \VCR\VCR;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
	protected $provider = null;
	protected $accessToken = 'f6bbae941202599676563d06640b4cd99b8cbcf54daae19eb112fbeff92647df';

	public function setUp()
	{
		$this->provider = m::mock('Crew\Unsplash\Provider\Unsplash', [
			'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none'
		]);
		$this->provider->client_id = 'mock_client_id';

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