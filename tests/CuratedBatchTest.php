<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \VCR\VCR;

class CuratedBatchTest extends BaseTest
{
	public function setUp()
	{
		parent::setUp();

		$this->curatedBatch = new Unsplash\CuratedBatch($this->provider, (object)['accessToken' => $this->accessToken]);
	}

	public function testFindCuratedBatch()
	{
		VCR::insertCassette('curated_batches.yml');

		$curatedBatch = $this->curatedBatch->find(68);

		VCR::eject();

		$this->assertEquals(200, $this->curatedBatch->getStatusCode());
		$this->assertEquals('68', $curatedBatch['id']);
	}

	public function testFindAllCuratedBatches()
	{
		VCR::insertCassette('curated_batches.yml');

		$curatedBatches = $this->curatedBatch->findAll();

		VCR::eject();

		$this->assertEquals(200, $this->curatedBatch->getStatusCode());
		$this->assertEquals(10, count($curatedBatches));
	}

	public function testFindCuratedBatchPhotos()
	{
		VCR::insertCassette('curated_batches.yml');

		$curatedBatchesPhotos = $this->curatedBatch->photos(68);

		VCR::eject();

		$this->assertEquals(200, $this->curatedBatch->getStatusCode());
		$this->assertEquals(10, count($curatedBatchesPhotos));
	}
}