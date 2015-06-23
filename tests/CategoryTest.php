<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \Dotenv\Dotenv;
use \VCR\VCR;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		VCR::turnOn();
		$dotenv = new Dotenv(__DIR__);
		$dotenv->load();

		$this->connection  = new Unsplash\Connection(getenv('APPLICATION_ID'), getenv('APPLICATION_SECRET_KEY'));
		$this->category = new Unsplash\Category($this->connection);
	}

	public function testFindCategory()
	{
		VCR::insertCassette('find_category.yml');

		$category = $this->category->find(2);

		VCR::eject();

		$this->assertEquals(200, $this->category->getStatusCode());
		$this->assertEquals(2, $category['id']);
	}

	public function testFindAllCategory()
	{
		VCR::insertCassette('find_all_categories.yml');

		$categories = $this->category->findAll();

		VCR::eject();

		$this->assertEquals(200, $this->category->getStatusCode());
		$this->assertEquals(6, count($categories));
	}

	public function testFindCategoryPhotos()
	{
		VCR::insertCassette('find_category_photos.yml');

		$categoryPhotos = $this->category->photos(2);

		VCR::eject();

		$this->assertEquals(200, $this->category->getStatusCode());
		$this->assertEquals(10, count($categoryPhotos));
	}
}