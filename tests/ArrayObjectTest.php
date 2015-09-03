<?php

namespace Crew\Unsplash\Tests;

use Crew\Unsplash;

class ArrayObjectTest extends BaseTest
{
	// private $arrayObject;

	public function setUp()
	{
		parent::setUp();
	}

	public function testConstructorAcceptHeaders()
	{
		$headers = ['X-Per-Page' => ['10'], 'X-Total' => ['100']];
		$arrayObject = new Unsplash\ArrayObject([], $headers);

		$this->assertInstanceOf('ArrayObject', $arrayObject);
	}

	public function testTotalPage()
	{
		$headers = ['X-Per-Page' => ['10'], 'X-Total' => ['100']];
		$arrayObject = new Unsplash\ArrayObject([], $headers);

		$this->assertEquals(10, $arrayObject->totalPages());
	}

	public function testNextPage()
	{
		$headers = ['Link' => ['<http://api.staging.unsplash.com/photos?page=266>; rel="last", <http://api.staging.unsplash.com/photos?page=2>; rel="next"']];

		$arrayObject = new Unsplash\ArrayObject([], $headers);
		$pages = ['first' => null, 'next' => 2, 'prev' => null, 'last' => 266];

		$this->assertEquals($pages, $arrayObject->getPages());
	}

	public function testCurrentPageWhenFirstPage()
	{
		$headers = ['Link' => ['<http://api.staging.unsplash.com/photos?page=266>; rel="last",
		<http://api.staging.unsplash.com/photos?page=2>; rel="next"']];

		$arrayObject = new Unsplash\ArrayObject([], $headers);

		$this->assertEquals(1, $arrayObject->currentPage());
	}

	public function testCurrentPageWhenLastPage()
	{
		$headers = ['Link' => ['<http://api.staging.unsplash.com/photos?page=1>; rel="first",
		<http://api.staging.unsplash.com/photos?page=265>; rel="prev"']];

		$arrayObject = new Unsplash\ArrayObject([], $headers);

		$this->assertEquals(266, $arrayObject->currentPage());
	}

	public function testCurrentPageWhenMiddlePage()
	{	
		$headers = ['Link' => ['<http://api.staging.unsplash.com/photos?page=1>; rel="first",
		<http://api.staging.unsplash.com/photos?page=264>; rel="prev",
		<http://api.staging.unsplash.com/photos?page=266>; rel="last",
		<http://api.staging.unsplash.com/photos?page=266>; rel="next"']];

		$arrayObject = new Unsplash\ArrayObject([], $headers);

		$this->assertEquals(265, $arrayObject->currentPage());
	}
}