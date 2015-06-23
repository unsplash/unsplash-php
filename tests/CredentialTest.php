<?php
namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;

class CredentialTest extends \PHPUnit_Framework_TestCase
{
	public function testCreation()
	{
		$credentials = new Unsplash\Credential(['clientId' => '123abc', 'key' => 'boombompowpow']);

		$this->assertEquals('123abc', $credentials->clientId);
		$this->assertEquals('boombompowpow', $credentials->key);
	}

	public function testToArray()
	{
		$informations = ['clientId' => '123abc', 'key' => 'boombompow'];
		$credentials = new Unsplash\Credential($informations);

		$this->assertEquals($informations, $credentials->toArray());
	}
}