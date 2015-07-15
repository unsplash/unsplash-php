<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;

class PhotoTest extends BaseTest
{
	public function setUp()
	{
		parent::setUp();

		$connection = new Unsplash\Connection($this->provider, $this->accessToken);
		Unsplash\HttpClient::$connection = $connection;
	}

	public function testFindPhoto()
	{
		VCR::insertCassette('photos.yml');

		$photo = Unsplash\Photo::find('ZUaqqMxtxYk');

		VCR::eject();

		$this->assertEquals('ZUaqqMxtxYk', $photo->id);
	}

	public function testFindAllPhotos()
	{
		VCR::insertCassette('photos.yml');

		$photos = Unsplash\Photo::all();

		VCR::eject();

		$this->assertEquals(10, $photos->count());
	}

	public function testSearchPhotos()
	{
		VCR::insertCassette('photos.yml');

		$photos = Unsplash\Photo::search('coffee');

		VCR::eject();

		$this->assertEquals(10, $photos->count());
	}

	public function testPhotographer()
	{
		VCR::insertCassette('photos.yml');

		$photo = Unsplash\Photo::find('ZUaqqMxtxYk');
		$photographer = $photo->photographer();

		VCR::eject();

		$this->assertEquals($photo->user['username'], $photographer->username);
	}

	public function testPostPhotos()
	{
		$this->markTestIncomplete(
          'Due to an issue with VCR, we do not run this test.'
        );
		
		VCR::insertCassette('photos.yml');
		
		$photo = Unsplash\Photo::create(__dir__.'/images/land-test.txt');
		
		VCR::eject();

		$this->assertInstanceOf('Photo', $photo);
	} 
}