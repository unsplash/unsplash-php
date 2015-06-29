<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;

class UserTest extends BaseTest
{
	public function setUp()
	{
		parent::setUp();

		$connection = new Unsplash\Connection($this->provider, $this->accessToken);
		Unsplash\HttpClient::$connection = $connection;
	}

	public function testFindUser()
	{
		VCR::insertCassette('users.yml');

		$user = Unsplash\User::find('dechuck');

		VCR::eject();

		$this->assertEquals('dechuck', $user->username);
	}

	/**
	 * @expectedException Crew\Unsplash\Exception
	 * @expectedExceptionCode 404
	 */
	public function testFindUnknownUser()
	{
		VCR::insertCassette('users.yml');

		$user = Unsplash\User::find('badbadnotgooduser');

		VCR::eject();
	}

	public function testFindCurrentUser()
	{
		VCR::insertCassette('users.yml');

		$user = Unsplash\User::current();

		VCR::eject();

		// $this->assertEquals(200, $this->user->getStatusCode());
	}

	/**
	 * @expectedException Crew\Unsplash\Exception
	 * @expectedExceptionCode 401
	 */
	public function testFindCurrentUserOnUnconnectedUser()
	{
		$connection = new Unsplash\Connection($this->provider);
		Unsplash\HttpClient::$connection = $connection;

		VCR::insertCassette('users.yml');

		$user = Unsplash\User::current();

		VCR::eject();
	}

	public function testFindUserPhotos()
	{
		VCR::insertCassette('users.yml');

		$user = Unsplash\User::find('lukechesser');
		$photos = $user->photos();

		VCR::eject();

		$this->assertEquals(8, $photos->count());
	}

	public function testUpdateUser()
	{
		$this->markTestIncomplete(
          'Due to an issue with VCR, we do not run this test.'
        );
		
		$newInstagramUsername = 'dechuck'.time();

		VCR::insertCassette('users.yml');
		$user = Unsplash\User::find('dechuck');
		$user->update(['instagram_username'=>$newInstagramUsername]);

		VCR::eject();

		$this->assertEquals($newInstagramUsername, $user->instagram_username);
	}
}