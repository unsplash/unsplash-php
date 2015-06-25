<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;

class UserTest extends BaseTest
{
	public function setUp()
	{
		parent::setUp();

		$this->user = new Unsplash\User($this->provider, (object)['accessToken' => $this->accessToken]);
	}

	public function testFindUser()
	{
		VCR::insertCassette('users.yml');

		$user = $this->user->find('dechuck');

		VCR::eject();

		$this->assertEquals(200, $this->user->getStatusCode());
		$this->assertEquals('dechuck', $user['username']);
	}

	public function testFindCurrentUser()
	{
		VCR::insertCassette('users.yml');

		$user = $this->user->current();

		VCR::eject();

		$this->assertEquals(200, $this->user->getStatusCode());
	}

	public function testFindUserPhotos()
	{
		VCR::insertCassette('users.yml');

		$userPhotos = $this->user->photos('lukechesser');

		VCR::eject();

		$this->assertEquals(200, $this->user->getStatusCode());
		$this->assertEquals(8, count($userPhotos));
	}

	public function testUpdateUser()
	{
		VCR::insertCassette('users.yml');
		
		$updatedUser = $this->user->update(['instagram_username'=>'dechuck1']);

		VCR::eject();

		$this->assertEquals(200, $this->user->getStatusCode());
		$this->assertEquals('dechuck1', $updatedUser['instagram_username']);
	}
}