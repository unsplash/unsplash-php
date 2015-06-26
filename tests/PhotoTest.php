<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;

class PhotoTest extends BaseTest
{
	public function setUp()
	{
		parent::setUp();

		$this->photo = new Unsplash\Photo($this->provider, (object)['accessToken' => $this->accessToken]);
	}

	public function testFindPhoto()
	{
		VCR::insertCassette('photos.yml');

		$photo = $this->photo->find('ZUaqqMxtxYk');

		VCR::eject();

		$this->assertEquals(200, $this->photo->getStatusCode());
		$this->assertEquals('ZUaqqMxtxYk', $photo['id']);
	}

	public function testFindAllPhotos()
	{
		VCR::insertCassette('photos.yml');

		$photos = $this->photo->findAll();

		VCR::eject();

		$this->assertEquals(200, $this->photo->getStatusCode());
		$this->assertEquals(10, count($photos));
	}

	public function testSearchPhotos()
	{
		VCR::insertCassette('photos.yml');

		$photos = $this->photo->search('coffee');

		VCR::eject();

		$this->assertEquals(200, $this->photo->getStatusCode());
		$this->assertEquals(10, count($photos));
	}

	public function testPostPhotos()
	{
		$this->markTestIncomplete(
          'Due to an issue with VCR, we do not run this test.'
        );

		$photo = fopen(__dir__.'/images/land-test.txt', 'r');
		
		VCR::insertCassette('photos.yml');
		
		$photo = $this->photo->create($photo);
		
		VCR::eject();

		$this->assertEquals(201, $this->photo->getStatusCode());
	} 
}