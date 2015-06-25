<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;

class CategoryTest extends BaseTest
{
	public function setUp()
	{
		parent::setUp();

		$this->category = new Unsplash\Category($this->provider, (object)['accessToken' => $this->accessToken]);
	}

	public function testFindCategory()
	{
		VCR::insertCassette('categories.yml');

		$category = $this->category->find(2);

		VCR::eject();

		$this->assertEquals(200, $this->category->getStatusCode());
		$this->assertEquals(2, $category['id']);
	}

	public function testFindAllCategory()
	{
		VCR::insertCassette('categories.yml');

		$categories = $this->category->findAll();

		VCR::eject();

		$this->assertEquals(200, $this->category->getStatusCode());
		$this->assertEquals(6, count($categories));
	}

	public function testFindCategoryPhotos()
	{
		VCR::insertCassette('categories.yml');

		$categoryPhotos = $this->category->photos(2);

		VCR::eject();

		$this->assertEquals(200, $this->category->getStatusCode());
		$this->assertEquals(10, count($categoryPhotos));
	}
}