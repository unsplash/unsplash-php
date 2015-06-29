<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;

class CategoryTest extends BaseTest
{
	public function setUp()
	{
		parent::setUp();

		$connection = new Unsplash\Connection($this->provider, $this->accessToken);
		Unsplash\HttpClient::$connection = $connection;
	}

	public function testFindCategory()
	{
		VCR::insertCassette('categories.yml');

		$category = Unsplash\Category::find(2);

		VCR::eject();

		$this->assertEquals(2, $category->id);
	}

	/**
	 * @expectedException Crew\Unsplash\Exception
	 * @expectedExceptionCode 404
	 */
	public function testErrorOnNoCategory()
	{
		VCR::insertCassette('categories.yml');

		$category = Unsplash\Category::find(1);

		VCR::eject();
	}

	public function testFindAllCategory()
	{
		VCR::insertCassette('categories.yml');

		$categories = Unsplash\Category::all();

		VCR::eject();

		$this->assertEquals(6, $categories->count());
	}

	public function testFindCategoryPhotos()
	{
		VCR::insertCassette('categories.yml');

		$category = Unsplash\Category::find(2);
		$photos = $category->photos();

		VCR::eject();

		$this->assertEquals(10, $photos->count());
	}

	public function testFindNoPhotoForACategory()
	{
		VCR::insertCassette('categories.yml');

		$category = Unsplash\Category::find(2);
		$photos = $category->photos(2000);

		VCR::eject();

		$this->assertEquals(0, $photos->count());
	}
}