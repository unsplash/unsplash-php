<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;

class PhotoTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		VCR::turnOn();

		$this->connection  = new Unsplash\Connection(getenv('APPLICATION_ID'), getenv('APPLICATION_SECRET_KEY'));
		$this->photo = new Unsplash\Photo($this->connection);
	}

	public function testFindPhoto()
	{
		VCR::insertCassette('find_photo.yml');

		$photo = $this->photo->find('ZUaqqMxtxYk');

		VCR::eject();

		$this->assertEquals(200, $this->photo->getStatusCode());
		$this->assertEquals('ZUaqqMxtxYk', $photo['id']);
	}

	public function testFindAllPhotos()
	{
		VCR::insertCassette('find_all_photo.yml');

		$photos = $this->photo->findAll();

		VCR::eject();

		$this->assertEquals(200, $this->photo->getStatusCode());
		$this->assertEquals(10, count($photos));
	}

	public function testSearchPhotos()
	{
		VCR::insertCassette('search_photo.yml');

		$photos = $this->photo->search('coffee');

		VCR::eject();

		$this->assertEquals(200, $this->photo->getStatusCode());
		$this->assertEquals(10, count($photos));
	}

	// public function testPostPhotos()
	// {
	// 	$this->connection  = new Unsplash\Connection(getenv('APPLICATION_ID'), getenv('APPLICATION_SECRET_KEY'), null, null, getenv('REFRESH_TOKEN'), time()-10);
		
	// 	$photo = fopen('photo.jpg', 'w');

	// 	VCR::insertCassette('create_photo.yml');

	// 	$photo = $this->photo->create('image_name', $photo);

	// 	VCR::eject();
	// } 
}