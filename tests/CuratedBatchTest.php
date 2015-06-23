<?php

namespace Crew\Unsplash\Tests;

use \Crew\Unsplash as Unsplash;
use \Dotenv\Dotenv;
use \VCR\VCR;

class CuratedBatchTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		VCR::turnOn();
		$dotenv = new Dotenv(__DIR__);
		$dotenv->load();

		$this->connection  = new Unsplash\Connection(getenv('APPLICATION_ID'), getenv('APPLICATION_SECRET_KEY'));
		$this->curatedBatch = new Unsplash\CuratedBatch($this->connection);
	}

	public function testFindCuratedBatch()
	{
		VCR::insertCassette('find_curated_batch.yml');

		$curatedBatch = $this->curatedBatch->find(68);

		VCR::eject();

		$this->assertEquals(200, $this->curatedBatch->getStatusCode());
		$this->assertEquals('68', $curatedBatch['id']);
	}

	public function testFindAllCuratedBatches()
	{
		VCR::insertCassette('find_all_curated_batches.yml');

		$curatedBatches = $this->curatedBatch->findAll();

		VCR::eject();

		$this->assertEquals(200, $this->curatedBatch->getStatusCode());
		$this->assertEquals(10, count($curatedBatches));
	}

	public function testFindCuratedBatchPhotos()
	{
		VCR::insertCassette('find_curated_batch_photos.yml');

		$curatedBatchesPhotos = $this->curatedBatch->photos(68);

		VCR::eject();

		$this->assertEquals(200, $this->curatedBatch->getStatusCode());
		$this->assertEquals(10, count($curatedBatchesPhotos));
	}
}