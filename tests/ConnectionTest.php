<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;
use \Dotenv\Dotenv;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
	private $credentials = null;
	private $conneciton = null;

	public function setUp()
	{
		// Turn on the VCR.
		// How do you want to register you best show if it's turn off
		VCR::turnOn();
		$dotenv = new Dotenv(__DIR__);
		$dotenv->load();

		$this->credentials = new Unsplash\Credential([
			'client_id' => getenv('APPLICATION_ID'),
			'client_secret' => getenv('APPLICATION_SECRET_KEY'),
			'redirect_uri' => getenv('REDIRECT_URI')
		]);

		$this->connection  = new Unsplash\Connection(getenv('APPLICATION_ID'), getenv('APPLICATION_SECRET_KEY'), getenv('REDIRECT_URI'));
	}

	public function testConnectionUrlConstruction()
	{
		$url = $this->connection->getConnectionUrl();
		$testedUrl = 'http://api.staging.unsplash.com/oauth/authorize?client_id='.getenv('APPLICATION_ID').'&client_secret='.getenv('APPLICATION_SECRET_KEY').'&redirect_uri='.str_replace(':', '%3A', getenv('REDIRECT_URI'));

		$this->assertEquals($testedUrl, $url);
	}

	public function testClientIdAsAuthorizationToken()
	{
		$this->assertEquals('Client-ID '.getenv('APPLICATION_ID'), $this->connection->getAuthorizationToken());
	}

	public function testAccessTokenAsAuthorizationToken()
	{
		$this->credentials->access_token = '123asd';

		$this->assertEquals('Bearer 123asd', $this->connection->getAuthorizationToken());
	}

	public function testGenerateTokenWithGoodCode()
	{
		VCR::insertCassette('good_access_token.yml');

		$tokenRes = $this->connection->generateToken(getenv('CODE'));

		VCR::eject();

		$this->assertEquals($this->credentials->access_token, $tokenRes['access_token']);
		$this->assertEquals($this->credentials->refresh_token, $tokenRes['refresh_token']);
	}

	/**
	 * @todo write proper test
	 */
	public function testGenerateRefreshToken()
	{

	}

	public function testGenerateUrlQueryForAuthorization()
	{
		$generateTokenQuery = http_build_query([
			'client_id' => getenv('APPLICATION_ID'),
			'client_secret' => getenv('APPLICATION_SECRET_KEY'),
			'code' => getenv('CODE'),
			'redirect_uri' => getenv('REDIRECT_URI'),
			'grant_type' => 'authorization_code',
			'scope' => 'public'
		]);


		$query = $this->invokeMethod($this->connection, 'getTokenQuery', [getenv('CODE')]);

		$this->assertEquals($generateTokenQuery, $query);
	}

	public function testGenerateUrlQueryForRefreshToken()
	{
		$generateTokenQuery = http_build_query([
			'client_id' => getenv('APPLICATION_ID'),
			'client_secret' => getenv('APPLICATION_SECRET_KEY'),
			'grant_type' => 'refresh_token',
			'refresh_token' => getenv('REFRESH_TOKEN')
		]);

		$query = $this->invokeMethod($this->connection, 'getTokenQuery', [getenv('REFRESH_TOKEN'), 'refresh_token']);

		$this->assertEquals($generateTokenQuery, $query);
	}

	/**
	 * Call protected/private method of a class.
	 *
	 * @param object &$object    Instantiated object that we will run method on.
	 * @param string $methodName Method name to call
	 * @param array  $parameters Array of parameters to pass into method.
	 *
	 * @return mixed Method return.
	 */
	private function invokeMethod(&$object, $methodName, array $parameters = array())
	{
	    $reflection = new \ReflectionClass(get_class($object));
	    $method = $reflection->getMethod($methodName);
	    $method->setAccessible(true);

	    return $method->invokeArgs($object, $parameters);
	}
}