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

	public function testFindUnknownUser()
	{
		VCR::insertCassette('users.yml');

		$user = $this->user->find('badbadnotgooduser');

		VCR::eject();

		$this->assertEquals(404, $this->user->getStatusCode());
		$this->assertEquals(false, $this->user->isGoodRequest());
	}

	public function testFindCurrentUser()
	{
		VCR::insertCassette('users.yml');

		$user = $this->user->current();

		VCR::eject();

		$this->assertEquals(200, $this->user->getStatusCode());
	}

	public function testFindCurrentUserOnUnconnectedUser()
	{
		$this->user = new Unsplash\User($this->provider);

		VCR::insertCassette('users.yml');

		$user = $this->user->current();

		VCR::eject();

		$this->assertEquals(401, $this->user->getStatusCode());
		$this->assertEquals(false, $this->user->isGoodRequest());
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
		$this->markTestIncomplete(
          'Due to an issue with VCR, we do not run this test.'
        );
		
		$newInstagramUsername = 'dechuck'.time();

		VCR::insertCassette('users.yml');

		$updatedUser = $this->user->update(['instagram_username'=>$newInstagramUsername]);

		VCR::eject();

		$this->assertEquals(200, $this->user->getStatusCode());
		$this->assertEquals($newInstagramUsername, $updatedUser['instagram_username']);
	}
}